(
SET mypath=%~dp0%..\..\
cd %mypath% 

echo LOCAL_DBPROJECT=true NO_TERM=true bash /vagrant/scripts/snapshot_db.sh | vagrant ssh
pause
)
