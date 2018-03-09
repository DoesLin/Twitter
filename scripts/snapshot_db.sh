#!/bin/bash
SCRIPTPATH="$( cd "$(dirname "$0")" ; pwd -P )"
cd "$SCRIPTPATH/../"
source scripts/helpers/macros.sh
check_term

handle_duplicate() {
	name="$1"
	if [[ -e $name.sql ]] ; then
	    i=0
	    while [[ -e $name.$i.sql ]] ; do
		let i++
	    done
	    name=$1.$i.sql
	fi
	echo $name
}

if [ -n "$LOCAL_DBPROJECT" ]; then
        backup "$1"
else
	vagrant ssh -c "export LOCAL_DBPROJECT=true; bash /vagrant/scripts/snapshot_db.sh"
fi
