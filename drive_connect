#!/bin/bash

# Find the external USB drive
usb_drive=$(diskutil list external | grep -m1 external | awk '{print $1}')

# Check if a USB drive was found
if [ -z "$usb_drive" ]; then
    echo "No external USB drive found."
    exit 1
fi

# Check if the drive is already mounted
if mount | grep "$usb_drive" > /dev/null; then
    echo "USB drive $usb_drive is already mounted."
else
    # Attempt to mount the drive
    diskutil mount "$usb_drive"
    if [ $? -eq 0 ]; then
        echo "USB drive $usb_drive mounted successfully."
    else
        echo "Failed to mount USB drive $usb_drive."
    fi
fi
