Here I have followed the notes on
https://github.com/jenkinsci/docker/blob/master/README.md
https://labex.io/tutorials/jenkins-how-to-manage-jenkins-docker-container-lifecycle-414503

With the following commands we install the Jenkins in the container jenkins_local, which is an addition to the official installation notes.
```
# Allow docker within the jenkins_local container 
# Instead of installing Docker in jenkins_local we use the Docker
# of the host computer by enabling permissions of the docker.sock
# Please find the way to allow the permissions in your system
# Ubuntu  24
sudo chmod 777 /var/run/docker.sock


#pull the jenkins image
docker pull jenkins/jenkins:lts-jdk17

```


# run jenkins image by a container name jenkins_local


 ```
docker run -d \
 --name jenkins_local \
 -p 8080:8080 \
 -p 50000:50000 \
 -v /var/run/docker.sock:/var/run/docker.sock \
 -v jenkins_home:/var/jenkins_home \
 jenkins/jenkins:lts-jdk17
```







After the installation you can restart the jenkins container  by

 ```
# Allow docker within the jenkins_local container 
# Instead of installing Docker in jenkins_local we use the Docker
# of the host computer by enabling permissions of the docker.sock
# Please find the way to allow the permissions in your system
# Ubuntu 24
sudo chmod 777 /var/run/docker.sock


# Restart
docker restart jenkins_local
 ```






After this script, follow the standard Jenkins configuration by opening
http://localhost:8080/
and following the instructions. Install the recommended plugins.

