docker restart jenkings_local
docker restart jenkings_alpine_socat
echo 
echo  jenkings_alpine_socat IP
docker inspect jenkings_alpine_socat |grep 'IPAddress'