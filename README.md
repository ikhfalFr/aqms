# STREAMING SENSOR PM MENGGUNAKAN USB SPI Vendor
## Requirements
`php8.0` or newest, `python3`, `pip3`, `composer`, 'pymodbus versi 2.5'

## Installation
1. Clone Repository
```bash
git clone https://github.com/ikhfalFr/aqmsEFS2.git && cd aqmsEFS2 && git checkout EFS2
```
2. Install Python3 Library
```bash
pip3 install -r requirements.txt --break-system-packages
```
3. Install Dependency PHP
```bash
cd gui && composer install
```
4. Add Launcher
```bash
cd .. && chmod a+x launch_aqms.desktop && cp launch_aqms.desktop ~/Desktop
```

5. Create Services
```bash
sudo cp services/*.service /etc/systemd/system/
```
6. Start Services
```bash
systemctl start aqms-driver-alpha
systemctl start aqms-averaging
systemctl start aqms-pm
```
7. Stop Services
```bash
systemctl stop aqms-driver-alpha
systemctl stop aqms-averaging
systemctl stop aqms-pm
```
8. Enable automaticly on boot:
```bash
systemctl enable aqms-driver-alpha
systemctl enable aqms-averaging
systemctl enable aqms-pm
```
9. Disable service:
```bash
systemctl disable aqms-driver-alpha
systemctl disable aqms-averaging
systemctl disable aqms-pm
```
10. Check service:
```bash
systemctl status aqms-driver-alpha
systemctl status aqms-averaging
systemctl status aqms-pm
```

## Setup Crontab
`sudo crontab -e` then choose `nano`
1. Average 1 Minute
```bash
* * * * * /usr/bin/php /home/mx/aqms-efs2/gui/spark command:avg1min >/dev/null 2>&1
```
2. Average 30 Minute
```bash
* * * * * /usr/bin/php /home/mx/aqms-efs2/gui/spark command:avg30min >/dev/null 2>&1
```
3. Pengiriman data 30 detik
```bash
* * * * * /usr/bin/php /home/mx/aqms-efs2/gui/spark command:sentdata1sec >/dev/null 2>&1
* * * * * sleep 30; /usr/bin/php /home/mx/aqms-efs2/gui/spark command:sentdata1sec >/dev/null 2>&1
```
4. Pengiriman data rerata 1 menit tiap 30 detik
```bash
*/30 * * * * sleep 60; /usr/bin/php /home/mx/aqms-efs2/gui/spark command:sentdata1min >/dev/null 2>&1
```
5. Menghapus file log di writeable logs CI
```bash
0 */12 * * * find /home/mx/aqms-efs2/gui/writeable/logs -type f -name 'log-*.log' -mtime +3 -exec rm -f {} \;
```
