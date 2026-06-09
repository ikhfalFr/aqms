#!/bin/bash
echo "Reading devices... Please wait!!"
sleep 1s
ls /dev/ttyUSB*
ls /dev/ttyD*
ls /dev/ttyM*
cd ~/aqmsGASES/ && python3 aqms_start.py
$SHELL
