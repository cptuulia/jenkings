 


# My Google Notes
https://docs.google.com/document/d/1DPQVp7qVV-yVpLuZbCcUJT8H8rQ3Lu1LmwZ-Dki4AlQ/edit?tab=t.0

# Sources


https://github.com/devopsjourney1/jenkins-101


# Install
https://hub.docker.com/r/jenkins/jenkins/
https://github.com/jenkinsci/docker/blob/master/README.md

```
docker pull jenkins/jenkins:jdk17
docker run --name jenkings_local -p 8080:8080 -p 50000:50000 --restart=on-failure jenkins/jenkins:lts-jdk17 

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

## Restart  and ssh
```
docker restart jenkings_local
docker exec -it jenkings_local sh
```
## Configure

http://localhost:8080/

Select : install recommended plugins and wait...

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


# Php Container


## Install jenkingTestContainer
```
 cd jenkingsTestContainer
 sudo chmod 777 .docker/db/data/
 docker compose up -d

```

http://localhost:8080/manage/cloud/
Click "Install Plugin"
(http://localhost:8080/manage/cloud/)

Select Docker and install

Restart Jenkings if not done automatically

Open
http://localhost:8080/manage/cloud/new