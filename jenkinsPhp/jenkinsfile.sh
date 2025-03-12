
# Here are the test CLI commands to replicate the pipeline


#################################################################################
#
#  Create workdir
#
#################################################################################



mkdir pipelineTest; cd  pipelineTest


##################################################################################
#
# Define env variables
#
##################################################################################

DOCKER_IMAGE_NAME="jenkinsphp-php" 
DOCKER_CONTAINER_NAME="jenkins-php-pipeline" 
JENKINS_WORKSPACE_PATH="/var/jenkins_home/workspace/dddd/jenkinsPhp/code/"

DOCKER_MYSQL_IMAGE_NAME="mysql:8.0"
DOCKER_MYSQL_CONTAINER_NAME="jenkins-mysql-pipeline"
DATABASE_NAME=jenkins-php_db_name

DOCKER_NETWORK_NAME=jenkins-php-pipeline

 
##################################################################################
#
# clone php files
#
##################################################################################

rm -rf *
git clone https://github.com/cptuulia/jenkings
    cd jenkings
git checkout pipeline  # check your current branch!!
cd jenkinsPhp; 
mv code/* ../..
cd ../..;
rm -rf jenkings



##################################################################################
#
# create containers
#
##################################################################################

 docker network create $DOCKER_NETWORK_NAME

# start $DOCKER_CONTAINER_NAME 
docker run -d -v .:/var/www  --name $DOCKER_CONTAINER_NAME --network $DOCKER_NETWORK_NAME $DOCKER_IMAGE_NAME

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

#
# command to ssh to $DOCKER_CONTAINER_NAME 
# docker exec -it  $DOCKER_CONTAINER_NAME bash

# command to ssh to  $DOCKER_MYSQL_CONTAINER_NAME
# docker exec -it  $DOCKER_MYSQL_CONTAINER_NAME  mysql -u root -proot
# or run myql from the php container
# docker exec -it $DOCKER_CONTAINER_NAME  mysql -h $DOCKER_MYSQL_CONTAINER_NAME -uroot -proot $DATABASE_NAME
#

##################################################################################
#
# Copy php files and install vendor files
#
##################################################################################



# don'  t worry about tar: .: file changed as we read it
tar -czvf   example.tar .
docker cp example.tar $DOCKER_CONTAINER_NAME:/var/www/
   docker exec $DOCKER_CONTAINER_NAME tar -xvf example.tar

rm  example.tar

# install vendor files

docker exec $DOCKER_CONTAINER_NAME    composer install  

##################################################################################
#
# Create database
#
##################################################################################
echo "DROP TABLE  IF EXISTS  Test;
CREATE TABLE Test (id int NOT NULL AUTO_INCREMENT, name varchar(255),   PRIMARY KEY (id));
SHOW tables;" > createTableTest.sql
docker exec -i $DOCKER_CONTAINER_NAME  mysql -h $DOCKER_MYSQL_CONTAINER_NAME -uroot -proot $DATABASE_NAME <createTableTest.sql
rm createTableTest.sql

##################################################################################
#
# Run tests
#
##################################################################################

# run phpunit
docker exec $DOCKER_CONTAINER_NAME ./vendor/bin/phpunit  -c Tests/phpunit.xml    Tests/Feature/simpleTest.php 

##################################################################################
#
# clean up
#
##################################################################################

# stop and remove $DOCKER_CONTAINER_NAME 
docker stop $DOCKER_CONTAINER_NAME ;docker rm $DOCKER_CONTAINER_NAME 
docker stop $DOCKER_MYSQL_CONTAINER_NAME; docker rm $DOCKER_MYSQL_CONTAINER_NAME; sudo rm -r .docker/db


# remove workfolder
cd ..
sudo rm -rf pipelineTest