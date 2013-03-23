#!/bin/bash

SOURCE="/home/esgott/workspace/haver"
TARGET="/home/esgott/public_html/elgg/mod/haver"
COMPONENTS="manifest.xml start.php"

rm -rf $TARGET/haver/*

for component in $COMPONENTS; do
	cp $SOURCE/$component $TARGET
done

chgrp -R http $TARGET
chmod -R 775 $TARGET 