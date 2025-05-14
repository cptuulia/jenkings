
##################################################################################
#
#  Jenkins shell script to execute the test job
#
##################################################################################



##################################################################################
#
# Define env variables
#
##################################################################################

DOCKER_PHP_IMAGE_NAME="jenkins_php" 
DOCKER_PHP_CONTAINER_NAME="jenkins_php" 


DOCKER_MYSQL_IMAGE_NAME="mysql:8.0"
DOCKER_MYSQL_CONTAINER_NAME="jenkins_mysql"
DATABASE_NAME=jenkins_db

DOCKER_NETWORK_NAME=jenkins-example

#UPDARE!!!
GIT_REPO_URL="https://github.com/cptuulia/jenkins.git"
#UPDARE!!!
GIT_BRANCH=demotuulia
 

##################################################################################
#
# clone php files
#
##################################################################################

# Clone the repo and checkout the correct branch
rm -rf *
# make sure that you have rights to clone the repo or make it public
git clone $GIT_REPO_URL
cd jenkins
git fetch
git checkout $GIT_BRANCH

# Move the php code to the root of the workspace folder and delete the repo folder
mv code/* ..
cd ..;
rm -rf jenkins




##################################################################################
#
# create containers
#
##################################################################################

# Create network, if does not exist
docker network ls|grep  $DOCKER_NETWORK_NAME > /dev/null || docker network create --driver bridge  $DOCKER_NETWORK_NAME



###########################################
#
# create containers
#
###########################################
# set flag to ignore errors so that the script does not crash
set -e

# Clean up container and images, if they exist( || true prevents the job to crash)
docker stop $DOCKER_PHP_CONTAINER_NAME || true
docker rm $DOCKER_PHP_CONTAINER_NAME || true

# create and start php container
docker run -d -v .:/var/www  --name $DOCKER_PHP_CONTAINER_NAME --network $DOCKER_NETWORK_NAME $DOCKER_PHP_IMAGE_NAME


###########################################
#
# mysql container
#
###########################################

# Clean up container and images
docker stop $DOCKER_MYSQL_CONTAINER_NAME || true
sleep 5
docker rm $DOCKER_MYSQL_CONTAINER_NAME || true
sleep 5

# start mysql container
# access to client: docker exec -it jenkins_mysql bash -c "mysql -u root -proot jenkins_db"
docker run -d \
-v ./.docker/db/data:/var/lib/mysql \
-v ./.docker/logs:/var/log/mysql \
-v ./.docker/db/my.cnf:/etc/mysql/conf.d/my.cnf \
-v ./.docker/db/sql:/docker-entrypoint-initdb.d \
-e MYSQL_ROOT_PASSWORD='root' \
-e MYSQL_DATABASE=$DATABASE_NAME \
-p 3306:3306 \
--network $DOCKER_NETWORK_NAME \
--name $DOCKER_MYSQL_CONTAINER_NAME $DOCKER_MYSQL_IMAGE_NAME  

sleep 10

##################################################################################
#
# Create database
#
##################################################################################
echo "CREATE DATABASE IF NOT EXISTS $DATABASE_NAME; 
USE $DATABASE_NAME;  
DROP TABLE  IF EXISTS  Test;
CREATE TABLE Test (id int NOT NULL AUTO_INCREMENT, name varchar(255),   PRIMARY KEY (id));"  > createTableTest.sql;
docker exec -i $DOCKER_PHP_CONTAINER_NAME  mysql -h $DOCKER_MYSQL_CONTAINER_NAME -uroot -proot  <createTableTest.sql
rm createTableTest.sql

##################################################################################
#
# Copy php files and install vendor files
#
##################################################################################



# With a container in a container we cannot mount a folder. we need to copy the
# files a tar file and uncompress it
tar -czvf   code.tar *
sleep 5

docker exec $DOCKER_PHP_CONTAINER_NAME rm -rf /var/www/composer.lock
docker exec $DOCKER_PHP_CONTAINER_NAME rm -rf vendor
docker cp code.tar $DOCKER_PHP_CONTAINER_NAME:/var/www/
docker exec $DOCKER_PHP_CONTAINER_NAME tar -xvf code.tar
# uncomment the line below if you want to check
# docker exec $DOCKER_PHP_CONTAINER_NAME ls /var/www

sleep 5
rm  code.tar

# install vendor files

docker exec $DOCKER_PHP_CONTAINER_NAME    composer install  


##################################################################################
#
# Inspect
#
##################################################################################

# run phpunit and phpstan
docker exec $DOCKER_PHP_CONTAINER_NAME ./vendor/bin/phpunit  -c Tests/phpunit.xml    Tests/Feature/simpleTest.php 
docker exec $DOCKER_PHP_CONTAINER_NAMEvendor/bin/phpstan analyse -c config/phpstan.neon --memory-limit 500M

##################################################################################
#
# clean up
#
##################################################################################

# stop and remove $DOCKER_PHP_CONTAINER_NAME 
docker stop $DOCKER_PHP_CONTAINER_NAME ;docker rm $DOCKER_PHP_CONTAINER_NAME 
docker stop $DOCKER_MYSQL_CONTAINER_NAME; docker rm $DOCKER_MYSQL_CONTAINER_NAME; 


