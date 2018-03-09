(
SET mypath=%~dp0%..\..\
cd %mypath% 

echo LOCAL_DBPROJECT=true NO_TERM=true bash /vagrant/scripts/reset_env.sh | vagrant ssh
pause
)
