#!/bin/sh

while true
do
  chgrp -R 33 /var/www/html
  chmod -R g+rs /var/www/html

  sleep 60
done