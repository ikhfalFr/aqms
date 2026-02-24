import serial
import time
arduino = serial.Serial(port='COM5', baudrate=9600, timeout=.1)


def write_read():
    arduino.write(bytes(b'switch,0,*'))
    time.sleep(1)
    data = arduino.readline()
    print(data)
    # arduino.close()
    return data
# sleep(100)


write_read()
write_read()
# while True:
# num = input("Enter a number: ")  # Taking input from user
# value = write_read()
# value = write_read("REQ,*")
# print(value)  # printing the value
# time.sleep(1)
