import serial
import time
import struct
import sys
import collections
import db

# --- KONFIGURASI ---
PORT_OPC = '/dev/ttyPM'    
BAUDRATE = 9600
ID_SENSOR = 3              
PIN_RAW = 10            
PIN_MEAN = 11            
TIMEOUT = 2
WAIT_TIME = 1e-06
WINDOW_SIZE = 5

# Antrian untuk perhitungan rata-rata bergerak
history_pm1   = collections.deque(maxlen=WINDOW_SIZE)
history_pm2_5 = collections.deque(maxlen=WINDOW_SIZE)
history_pm10  = collections.deque(maxlen=WINDOW_SIZE)

def initOPC(ser):
    """Inisialisasi mode SPI pada OPC-N3 melalui UART."""
    try:
        time.sleep(1)
        ser.write(bytearray([0x5A, 0x01])) 
        ser.read(3)
        time.sleep(WAIT_TIME)
        ser.write(bytearray([0x5A, 0x03]))
        ser.read(9)
        time.sleep(WAIT_TIME)
        ser.write(bytearray([0x5A, 0x02, 0x92, 0x07]))
        ser.read(2)
        time.sleep(WAIT_TIME)
    except Exception as e:
        print(f"Failed to Init OPC: {e}")

def set_fan_laser(ser, state):
    """Mengatur status Kipas dan Laser (True=ON, False=OFF)."""
    try:
        fan_val = 0x03 if state else 0x02
        laz_val = 0x07 if state else 0x06
        
        # Power ON/OFF Fan
        for _ in range(5):
            ser.write(bytearray([0x61, 0x03]))
            nl = ser.read(2)
            if nl == b"\xff\xf3" or nl == b"\xf3\xff":
                time.sleep(WAIT_TIME)
                ser.write(bytearray([0x61, fan_val]))
                ser.read(2)
                time.sleep(2 if state else 0.1)
                break
            time.sleep(0.1)

        # Power ON/OFF Laser
        for _ in range(5):
            ser.write(bytearray([0x61, 0x03]))
            nl = ser.read(2)
            if nl == b"\xff\xf3" or nl == b"\xf3\xff":
                time.sleep(WAIT_TIME)
                ser.write(bytearray([0x61, laz_val]))
                ser.read(2)
                break
            time.sleep(0.1)
            
        status = "ON" if state else "OFF"
        print(f"Fan & Laser turned {status}")
        
    except Exception as e:
        print(f"Error setting Fan/Laser: {e}")

def rightbytes(response):
    """Membersihkan byte echo/padding dari komunikasi SPI-over-UART."""
    hist_response = []
    for j, k in enumerate(response):
        if ((j + 1) % 2) == 0: 
            hist_response.append(k)
    return hist_response

def get_pm_data(ser):
    """Mengambil data histogram dan membedah nilai PM1, PM2.5, dan PM10."""
    ser.reset_input_buffer()
    ser.write(bytearray([0x61, 0x30])) # Perintah baca histogram
    nl = ser.read(2)
    
    if nl == b'\xff\xf3' or nl == b'\xf3\xff':
        # Mengirim 86 byte dummy untuk menarik 172 byte data
        for i in range(86):
            ser.write(bytearray([0x61, 0x01]))
            time.sleep(0.000001)
        
        time.sleep(0.01)
        expected_bytes = 172
        raw = ser.read(expected_bytes)

        if len(raw) == expected_bytes:
            clean_data = rightbytes(raw)

            if len(clean_data) >= 72:
                try:
                    # Debugging: Cek apakah byte mentah berbeda?
                    # print(f"Hex PM2.5: {bytes(clean_data[64:68]).hex()} | Hex PM10: {bytes(clean_data[68:72]).hex()}")
                    
                    # Menggunakan '<f' untuk Little Endian Float
                    pm1   = struct.unpack('<f', bytes(clean_data[60:64]))[0]
                    pm2_5 = struct.unpack('<f', bytes(clean_data[64:68]))[0]
                    pm10  = struct.unpack('<f', bytes(clean_data[68:72]))[0]
                    
                    if (pm1 != pm1) or (pm2_5 != pm2_5) or (pm10 != pm10):
                         print("Warning: NaN detected in data")
                         return None
                         
                    return pm1, pm2_5, pm10
                except Exception as e:
                    print(f"Struct Parsing Error: {e}")
                    return None
        else:
            print(f"Incomplete Read: Expected {expected_bytes}, got {len(raw)}")
            
    return None

def calculate_rolling_mean(val, history_deque):
    """Menghitung rata-rata dari N data terakhir."""
    history_deque.append(val)
    if len(history_deque) > 0:
        return sum(history_deque) / len(history_deque)
    return 0.0

def main():
    try:
        client = serial.Serial(
            port=PORT_OPC,
            baudrate=BAUDRATE,
            parity=serial.PARITY_NONE,
            stopbits=serial.STOPBITS_ONE,
            bytesize=serial.EIGHTBITS,
            timeout=TIMEOUT
        )
    except Exception as e:
        print(f"FATAL: Cannot open port {PORT_OPC}. Error: {e}")
        db.update_sensor_values(ID_SENSOR, PIN_RAW, -999)
        db.update_sensor_values(ID_SENSOR, PIN_MEAN, -999)
        return

    initOPC(client)
    set_fan_laser(client, True)
    print("Waiting for sensor stabilization (5s)...")
    time.sleep(5)

    while True:
        try:
            raw_values = get_pm_data(client)

            if raw_values:
                raw_pm1, raw_pm25, raw_pm10 = raw_values

                mean_pm1 = calculate_rolling_mean(raw_pm1, history_pm1)
                mean_pm25 = calculate_rolling_mean(raw_pm25, history_pm2_5)
                mean_pm10 = calculate_rolling_mean(raw_pm10, history_pm10)

                # Format String untuk Database
                str_raw = (f"PM1;{round(raw_pm1, 2)};"
                           f"PM2.5;{round(raw_pm25, 2)};"
                           f"PM10;{round(raw_pm10, 2)};"
                           f"END_PM")
                
                db.update_sensor_values(ID_SENSOR, PIN_RAW, str_raw)

                str_mean = (f"PM1_MEAN;{round(mean_pm1, 2)};"
                            f"PM2.5_MEAN;{round(mean_pm25, 2)};"
                            f"PM10_MEAN;{round(mean_pm10, 2)};"
                            f"END_PM_MEAN")
                            
                db.update_sensor_values(ID_SENSOR, PIN_MEAN, str_mean)
                print(f"Data Updated: PM2.5={round(raw_pm25, 2)} | PM10={round(raw_pm10, 2)}")
                
            else:
                print("Read Warning (Skipping update)")
                time.sleep(0.5)

        except Exception as e:
            print(f"Exception: {e}")
            try:
                client.close()
                time.sleep(1)
                client.open()
                initOPC(client)
                set_fan_laser(client, True)
            except:
                pass

        time.sleep(2)

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("Stopping...")
        sys.exit()
