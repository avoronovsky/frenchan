#!/bin/bash

TARGET_DIR="/var/www/frenchan"

for file in $(ls)
do
	cp -r $file $TARGET_DIR
done
