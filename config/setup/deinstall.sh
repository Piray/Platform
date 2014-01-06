#!/bin/sh

APACHE_CONFIG_PATH="/usr/local/etc/apache24/Includes"

PIRAY_PLATFORM_CONFIG_FILE="Platform-hostname.conf Platform-modules.conf Platform-storage.conf Platform-web.conf"
RESTART_APACHE="no"

for config_file in $PIRAY_PLATFORM_CONFIG_FILE; do
    if [ -f "$APACHE_CONFIG_PATH/$config_file" ]; then
        rm -f $APACHE_CONFIG_PATH/$config_file
        echo "Remove $APACHE_CONFIG_PATH/$config_file"
        RESTART_APACHE="yes"
    else
        echo "$APACHE_CONFIG_PATH/$config_file is not exist"
    fi
done

if [ $RESTART_APACHE = "yes" ]; then
    echo "Deinstall Platform finished and restart Apache now ..."
    /usr/local/etc/rc.d/apache24 restart
fi
