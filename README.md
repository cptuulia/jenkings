
# Installation of the Jenkins container

Here we install the Jenkins server to a container jenkins_local.


Here I have followed the notes on:
[https://github.com/jenkinsci/docker/blob/master/README.md](https://github.com/jenkinsci/docker/blob/master/README.md)

You may find some usefull infomation on the following link:
[https://labex.io/tutorials/jenkins-how-to-manage-jenkins-docker-container-lifecycle-414503](https://labex.io/tutorials/jenkins-how-to-manage-jenkins-docker-container-lifecycle-414503)

With the following commands we install the Jenkins in the container jenkins_local, which is an addition to the official installation notes.


## Install container
```
#
# variables 
#

# The variables below are defined fot Ubuntu 24
# Verify these variables in your system.

# Allow docker inside a docker container     
# Read more info from 
# https://stackoverflow.com/questions/27879713/is-it-ok-to-run-docker-from-inside-docker
DOCKER_PATH="/usr/bin/docker"
DOCKER_SOCK="/var/run/docker.sock"

# Path to jenkins home
JENKINS_HOME="/var/jenkins_home"


sudo chmod 777 $DOCKER_SOCK


#pull the jenkins image
docker pull jenkins/jenkins:lts-jdk17

```

```
# run jenkins image by a container name jenkins_local
docker run -d \
--privileged=true \
--name jenkins_local \
-p 8080:8080 \
-p 50000:50000 \
-v /usr/bin/docker:$DOCKER_PATH \
-v /var/run/docker.sock:$DOCKER_SOCK \
-v jenkins_home:$JENKINS_HOME \
jenkins/jenkins:lts-jdk17

```

After the installation you can restart the jenkins container  by

```
# Allow docker within the jenkins_local container 
sudo chmod 777 $DOCKER_SOCK
# Restart
docker restart jenkins_local
```

After this script, follow the standard Jenkins configuration by opening
[http://localhost:8080](http://localhost:8080)
and following the instructions. Install the recommended plugins.

You can access to the container by
```
docker exec -it   --user root jenkins_local  sh
```

## Install Git Ftp

To be able to deploy to the live server wee need to install Git Ftp

```
#run in thr host computer:
docker exec -it --user root jenkins_local  apt-get update 
docker exec -it --user root jenkins_local  apt-get install git-ftp
```

# login to the container
docker exec -it   --user root jenkins_local  sh


# Creating php container image

Create an image 'jenkins_php' for the container 'jenkins_php' by the following
command in the root of this repo.
```
docker compose up -d
```