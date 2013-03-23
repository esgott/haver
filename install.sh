#!/bin/bash

SOURCE="/home/esgott/workspace/haver"
TARGET="/home/esgott/public_html/elgg/mod/haver"
COMPONENTS="pages manifest.xml start.php"

mkdir -p $TARGET
rm -rf $TARGET/*

for component in $COMPONENTS; do
	cp -R $SOURCE/$component $TARGET
done

chgrp -R http $TARGET
chmod -R 775 $TARGET
