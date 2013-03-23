#!/bin/bash

SOURCE="/home/esgott/workspace/haver"
TARGET="/home/esgott/public_html/elgg/mod/haver"

rm -rf $TARGET/haver/*

cp $SOURCE/manifest.xml $TARGET

chgrp -R http $TARGET
chmod -R 775 $TARGET 