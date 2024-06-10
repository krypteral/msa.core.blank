#!/bin/bash

cat >> /var/spool/cron/crontabs/root <<EOF
@reboot php -r 'file_put_contents("/var/www/crontestrun.log", "OK\r\n", FILE_APPEND);'
EOF

chmod 0600 /var/spool/cron/crontabs/root
chown root:crontab /var/spool/cron/crontabs/root