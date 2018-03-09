#!/bin/bash
SCRIPTPATH="$( cd "$(dirname "$0")" ; pwd -P )"
cd "$SCRIPTPATH/../"
source scripts/helpers/macros.sh
check_term

case "$1" in 
  test ) env="test";;
  * ) env="app";;
esac

schemas_file="sql/schemas"
entries_file="sql/entries"

if [ -n "$LOCAL_DBPROJECT" ]; then
$(python3 scripts/get_yaml.py "config/db.yaml" "$env")
mysql_cmd="-h$server -u$username -p$password"
	if [ "$env" == "app" ]; then
            backup "last_is_file"
        fi
        cat "$schemas_file.sql" | awk '!/^(use|create database|drop database|USE|CREATE DATABASE|DROP DATABASE)/' > buf.sql
        if [ "$env" = "app" ]; then
            cat "$entries_file.sql" | awk '!/^(use|create database|drop database|USE|CREATE DATABASE|DROP DATABASE)/' >> buf.sql
        fi
	bash scripts/purge_db.sh "$env"
	mysqladmin $mysql_cmd create "$db"
	mysql $mysql_cmd $db < buf.sql
	rm buf.sql
else
	vagrant ssh -c "export LOCAL_DBPROJECT=true; bash /vagrant/scripts/populate_db.sh"
fi
