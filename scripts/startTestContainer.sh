echo
echo #################################################################################
echo "Stop containers (if started)" 
echo
docker stop jenkings_test_mysql jenkings_test_nginx jenkings_test_php
echo
echo #################################################################################
echo  Start containers
echo
cd jenkingsTestContainer/
docker compose up -d
echo
echo #################################################################################
echo Containers started
echo
docker ps
