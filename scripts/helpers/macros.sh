#!/bin/bash

check_term() {
if [ -z "$NO_TERM" ]; then
	if [ ! -t 0 ]; then
		xterm -hold -e "bash $0; echo; echo Terminated. You can close the window."
	fi
fi
}

backup() {
    d=$(pwd)
    cd "$SCRIPTPATH/../"
    schemas_file="sql/schemas"
    entries_file="sql/entries"
    datetime="$(date +"%Y-%m-%d_%H-%M-%S")"
    $(python3 scripts/get_yaml.py "config/db.yaml" "app")
    mysql_cmd="-h$server -u$username -p$password"
            if [ "$1" = "last_is_file" ]; then
                    if ! mysql $mysql_cmd -e "use $db"; then
                        mysqladmin $mysql_cmd create "$db"
                    fi
                    sav_schemas="sql/backup/$datetime.schemas.sql"
                    sav_entries="sql/backup/$datetime.entries.sql"
            else
                    if [ -e "$schemas_file.sql" ]; then
                            mv "$schemas_file.sql" "sql/backup/$datetime.schemas.sql"
                            sav_schemas="$schemas_file.sql"
                    fi
                    if [ -e "$entries_file.sql" ]; then
                            mv "$entries_file.sql" "sql/backup/$datetime.entries.sql"
                            sav_entries="$entries_file.sql"
                    fi
            fi
            mysqldump $mysql_cmd --no-data --no-create-db $db > $sav_schemas

            mysqldump $mysql_cmd --no-create-info --no-create-db $db > $sav_entries
    cd $d
}
