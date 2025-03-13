
##########################################################################
#
#  Get the current IP of the jenkings_alpine_socat container
#
##########################################################################

echo  jenkings_alpine_socat IP
docker inspect jenkings_alpine_socat |grep 'IPAddress'