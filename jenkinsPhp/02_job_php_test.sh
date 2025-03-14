
#######################################################################
#
#  This is the second  part to test
#
# 
#
#######################################################################

##################################################################################
#
# Define env variables
#
##################################################################################

DOCKER_IMAGE_NAME="jenkinsphp-php" 
DOCKER_PHP_CONTAINER_NAME="jenkins-php-pipeline" 


DOCKER_MYSQL_IMAGE_NAME="mysql:8.0"
DOCKER_MYSQL_CONTAINER_NAME="jenkins-mysql-pipeline"
DATABASE_NAME=jenkins-php_db_name

DOCKER_NETWORK_NAME=jenkins-php-pipeline



GIT_REPO=main
 

##################################################################################
#
# clone php files
#
##################################################################################

rm -rf *
git clone https://github.com/cptuulia/jenkings
    cd jenkings
git checkout $GIT_REPO
cd jenkinsPhp; 
mv code/* ../..
cd ../..;
rm -rf jenkings



##################################################################################
#
# create containers
#
##################################################################################

# Create network, if does not exist
docker network ls|grep  $DOCKER_NETWORK_NAME > /dev/null || docker network create --driver bridge  $DOCKER_NETWORK_NAME
# set flag to igonre errors
set -e


###########################################
#
# $DOCKER_PHP_CONTAINER_NAME
#
###########################################

# || true means no crash if error
docker stop  $DOCKER_PHP_CONTAINER_NAME || true
docker rm  $DOCKER_PHP_CONTAINER_NAME || true

# start $DOCKER_PHP_CONTAINER_NAME 
docker run -d -v .:/var/www  --name $DOCKER_PHP_CONTAINER_NAME --network $DOCKER_NETWORK_NAME $DOCKER_IMAGE_NAME


###########################################
#
# $DOCKER_MYSQL_CONTAINER_NAME
#
###########################################

# || true means no crash if error
docker stop  $DOCKER_MYSQL_CONTAINER_NAME || true
docker rm  $DOCKER_MYSQL_CONTAINER_NAME || true

# start  $DOCKER_MYSQL_CONTAINER_NAME
docker run -d \
-v ./.docker/db/data:/var/lib/mysql \
-v ./.docker/logs:/var/log/mysql \
-v ./.docker/db/my.cnf:/etc/mysql/conf.d/my.cnf \
-v ./.docker/db/sql:/docker-entrypoint-initdb.d \
-e MYSQL_ROOT_PASSWORD='root' \
-e MYSQL_DATABASE=$DATABASE_NAME \
-e MYSQL_USER='jenkins-php_db_user' \
-e MYSQL_PASSWORD='jenkins-php_db_pass' \
-p 3306:3306 \
--network $DOCKER_NETWORK_NAME \
--name $DOCKER_MYSQL_CONTAINER_NAME $DOCKER_MYSQL_IMAGE_NAME 


sleep 10

##################################################################################
#
# Copy php files and install vendor files
#
##################################################################################



# don'  t worry about tar: .: file changed as we read it
tar -czvf   example.tar *
sleep 5

docker cp example.tar $DOCKER_PHP_CONTAINER_NAME:/var/www/
docker exec $DOCKER_PHP_CONTAINER_NAME tar -xvf example.tar

docker exec $DOCKER_PHP_CONTAINER_NAME  ls /var/www/

sleep 5
rm  example.tar

# install vendor files

docker exec $DOCKER_PHP_CONTAINER_NAME    composer install  

##################################################################################
#
# Create database
#
##################################################################################
echo "DROP TABLE  IF EXISTS  Test;
CREATE TABLE Test (id int NOT NULL AUTO_INCREMENT, name varchar(255),   PRIMARY KEY (id));
SHOW tables;" > createTableTest.sql
docker exec -i $DOCKER_PHP_CONTAINER_NAME  mysql -h $DOCKER_MYSQL_CONTAINER_NAME -uroot -proot $DATABASE_NAME <createTableTest.sql
rm createTableTest.sql

##################################################################################
#
# Run tests
#
##################################################################################

# run phpunit
docker exec $DOCKER_PHP_CONTAINER_NAME ./vendor/bin/phpunit  -c Tests/phpunit.xml    Tests/Feature/simpleTest.php 


##################################################################################
#
# clean up
#
##################################################################################

# stop and remove $DOCKER_PHP_CONTAINER_NAME 
docker stop $DOCKER_PHP_CONTAINER_NAME ;docker rm $DOCKER_PHP_CONTAINER_NAME 
docker stop $DOCKER_MYSQL_CONTAINER_NAME; docker rm $DOCKER_MYSQL_CONTAINER_NAME; 


