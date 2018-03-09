#!/bin/bash
SCRIPTPATH="$( cd "$(dirname "$0")" ; pwd -P )"
cd "$SCRIPTPATH/../"
source scripts/helpers/macros.sh

if [ $# -lt 1 ] || [ -z "$1" ]; then
    echo "USAGE : ./purge_db.sh env"
    exit
fi

$(python3 scripts/get_yaml.py "config/db.yaml" "$1")

echo "drop database if exists $db" | mysql -h"$server" -u"$username" -p"$password"

