Here I have followed the notes on:
[https://github.com/jenkinsci/docker/blob/master/README.md](https://github.com/jenkinsci/docker/blob/master/README.md)

You may find some usefull infomation on the following link:
[https://labex.io/tutorials/jenkins-how-to-manage-jenkins-docker-container-lifecycle-414503](https://labex.io/tutorials/jenkins-how-to-manage-jenkins-docker-container-lifecycle-414503)

With the following commands we install the Jenkins in the container jenkins_local, which is an addition to the official installation notes.

```


#
# variables 
#

# Path tp dockrt dock
#Ubuntu  24
DOCKER_SOCK="/var/run/docker.sock"
JENKINS_HOME="/var/jenkins_home"

# Allow docker within the jenkins_local container 
# Instead of installing Docker in jenkins_local we use the Docker
# of the host computer by enabling permissions of the docker.sock
# Please find the path to way to docker.sock and allow the permissions in your system
sudo chmod 777 $DOCKER_SOCK


#pull the jenkins image
docker pull jenkins/jenkins:lts-jdk17

```

```
# run jenkins image by a container name jenkins_local
docker run -d \
--name jenkins_local \
-p 8080:8080 \
-p 50000:50000 \
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