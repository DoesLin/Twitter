(
SET mypath=%~dp0%..\..\
SET arg=%1
cd %mypath% 

echo LOCAL_DBPROJECT=true NO_TERM=true bash /vagrant/scripts/tests.sh %arg% | vagrant ssh
pause
)
