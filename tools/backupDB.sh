#!/bin/sh

DEST=/media/590E-118D/openTimingBackup
DATE=`date +%y%m%d_%H%M`

if [ -d $DEST ]; then
 /opt/lampp/bin/mysqldump -c --skip-extended-insert -u root myrun > $DEST/backup_$DATE.sql
fi
