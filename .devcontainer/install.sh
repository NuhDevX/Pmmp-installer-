#!/bin/bash

mkdir -p repos builds
chmod -R 777 repos builds

# Start PHP dev server
php -S 0.0.0.0:8080 > /dev/null 2>&1 &
echo "Server started at http://localhost:8080"
