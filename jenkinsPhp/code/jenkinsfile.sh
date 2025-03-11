
# Here are the test CLI commands to replicate the pipeline

# connect to jenkins container, you can do this also in the host, 
# just make sure you are in an empty folder
./scripts/sshJenkinsLocal.sh
# or make an empty folder
mkdir pipelineTest; cd  pipelineTest

# Define env pariables
DOCKER_IMAGE_NAME="jenkinsphp-php" # Docker image name
DOCKER_CONTAINER_NAME="jenkins-php-pipeline" # Docker image version tag
JENKINS_WORKSPACE_PATH="/var/jenkins_home/workspace/dddd/jenkinsPhp/code/"


# clone php file if needed
rm -rf *

    git clone https://github.com/cptuulia/jenkings
    cd jenkings
    git checkout pipeline  # check your current branch!!
    cd jenkinsPhp; 
    mv code/* ../..
    cd ../..;
    rm -rf jenkings

# start $DOCKER_CONTAINER_NAME 
    docker run -d -v .:/var/www  --name $DOCKER_CONTAINER_NAME $DOCKER_IMAGE_NAME

# command to ssh to $DOCKER_CONTAINER_NAME 
# docker exec -it $DOCKER_CONTAINER_NAME bash


# copy files to $DOCKER_CONTAINER_NAME  
cd $JENKINS_WORKSPACE_PATH





# dont worry about tar: .: file changed as we read it
    tar -czvf   example.tar .
    docker cp example.tar $DOCKER_CONTAINER_NAME:/var/www/
   
docker exec $DOCKER_CONTAINER_NAME ls
docker exec $DOCKER_CONTAINER_NAME tar -xvf example.tar
docker exec $DOCKER_CONTAINER_NAME ls
rm  example.tar

# install vendor files

docker exec $DOCKER_CONTAINER_NAME    composer install  

# run phpunit
docker exec $DOCKER_CONTAINER_NAME ./vendor/bin/phpunit  -c Tests/phpunit.xml    Tests/Feature/simpleTest.php 

# stop and remove $DOCKER_CONTAINER_NAME 
docker stop $DOCKER_CONTAINER_NAME ;docker rm $DOCKER_CONTAINER_NAME 
   