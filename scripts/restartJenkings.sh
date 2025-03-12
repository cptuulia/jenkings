# stop mysql on host
. ~/scripts/stopMysql.sh

docker restart jenkings_local
docker restart jenkings_alpine_socat
docker restart jenkins_mysql
echo 
echo  jenkings_alpine_socat IP
docker inspect jenkings_alpine_socat |grep 'IPAddress'


echo "Allow docker with Jenkings (in Ubuntu)"
sudo chmod 777 /var/run/docker.sock
