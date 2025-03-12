 


# My Google Notes
https://docs.google.com/document/d/1DPQVp7qVV-yVpLuZbCcUJT8H8rQ3Lu1LmwZ-Dki4AlQ/edit?tab=t.0

# Sources


https://github.com/devopsjourney1/jenkins-101


# Install
https://hub.docker.com/r/jenkins/jenkins/
https://github.com/jenkinsci/docker/blob/master/README.md


See also script 
```
 scripts/reinstalAll.sh
```

```
docker pull jenkins/jenkins:jdk17
docker run --name jenkings_local -p 8080:8080 -p 50000:50000 --privileged=true -v jenkins_home:/var/jenkins_home -v/usr/bin/docker:/usr/bin/docker -v /var/run/docker.sock:/var/run/docker.sock --restart=on-failure  jenkins/jenkins:lts-jdk17

```

You should see the following and no command prompt
```
Jenkins initial setup is required. An admin user has been created and a password generated.
Please use the following password to proceed to installation:

ee8a74c50a7d47a5989057be5431cb0c

This may also be found at: /var/jenkins_home/secrets/initialAdminPassword

*************************************************************
*************************************************************
*************************************************************

2025-02-27 08:56:08.296+0000 [id=38]	INFO	jenkins.InitReactorRunner$1#onAttained: Completed initialization
2025-02-27 08:56:08.325+0000 [id=24]	INFO	hudson.lifecycle.Lifecycle#onReady: Jenkins is fully up and running
2025-02-27 08:56:09.463+0000 [id=51]	INFO	h.m.DownloadService$Downloadable#load: Obtained the updated data file for hudson.tasks.Maven.MavenInstaller
2025-02-27 08:56:09.464+0000 [id=51]	INFO	hudson.util.Retrier#start: Performed the action check updates server successfully at the attempt #1
```

Create network and check
```
docker network create jenkins
docker network ls
```

Note the lines below enable to use Docker
```
  -v/usr/bin/docker:/usr/bin/docker -v /var/run/docker.sock:/var/run/docker.sock 
```

Also do this in the host machine
```
sudo chmod 777 /var/run/docker.sock
```
## Restart  and ssh
```
docker restart jenkings_local
docker exec -it jenkings_local sh
```
## Configure

http://localhost:8080/

Select : install recommended plugins and wait...


username admin
password 123


Give the password shown in the installation. If you did not get it
do 
```
docker exec -it jenkings_local sh
more /var/jenkins_home/secrets/initialAdminPassword
```
After first login, by the generated password change on page
http://localhost:8080/user/admin/security/
to 
username admin
password 123

# Alpine Socat 

You ony need this, if you want to use Agents.
I rather use direct containers.
This is required for the connectio  a docker user agent.
https://hub.docker.com/r/alpine/socat

```
 docker run -d --restart=always --name jenkings_alpine_socat \
        -p 127.0.0.1:2376:2375 \
        -v /var/run/docker.sock:/var/run/docker.sock \
        alpine/socat \
        tcp-listen:2375,fork,reuseaddr unix-connect:/var/run/docker.sock
```

You can test by
```
# get 'IPAddress by
docker inspect jenkings_alpine_socat |grep 'IPAddress'
# "SecondaryIPAddresses": null,
#            "IPAddress": "172.17.0.2",
#                    "IPAddress": "172.17.0.2",
#


nc -zv IIPAddress  2375

```


# jenkinsphp

In this folder I have the definition for the container I use for the PHP pipeline job.
See more in chapter Php Pipeline



https://docs.google.com/document/d/1DPQVp7qVV-yVpLuZbCcUJT8H8rQ3Lu1LmwZ-Dki4AlQ/edit?tab=t.0