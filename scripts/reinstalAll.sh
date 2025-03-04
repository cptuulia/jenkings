
#Jenkings container
JENKINGS_LOCAl_CONTAINER=jenkings_local
JENKINGS_LOCAL_IMAGE="jenkins/jenkins:jdk17"
JENKINGS_LOCAL_IMAGE_TAG="jdk17"

#Alpine Socat container , to connect to php container
JENKINGS_ALPINE_SOCAT_CONTAINER=jenkings_alpine_socat
JENKINGS_ALPINE_SOCAT_IMAGE="alpine/socat"

#php container
JENKINS_PHP_CONTAINER=jenkins_php
JENKINS_PHP_IMAGE=jenkinsphp-php


declare -a CONTAINERS=(
    $JENKINGS_LOCAl_CONTAINER 
    $JENKINS_PHP_CONTAINER
    $JENKINGS_ALPINE_SOCAT_CONTAINER )



#
##################################################################
#
function remove_container {
    CONTAINER=$1

 
    printf "\n\n>docker ps | grep '$CONTAINER'"
    docker ps | grep '$CONTAINER'

    printf "\n\n>docker stop $CONTAINER\n\n" 
    docker stop $CONTAINER
    
    printf "\n\n>docker rm $CONTAINER\n\n"
    docker rm $CONTAINER
    echo $CONTAINER removed
    press_enter_to_continue
}


#
##################################################################
#
function remove_image {
    IMAGE=$1
     printf "\n\n>deleteing image  $IMAGE\n\n"

    docker image ls | grep $IMAGE
    echo
    read -p "Give the image id of $IMAGE if you want to remove it   (others just hit enter)  " image_id 
     if [[ $image_id != "" ]]; then
     echo
        printf "\n\n>docker image rm  $image_id\n\n"
        docker image rm $image_id
        press_enter_to_continue
        # 0 = true
        return 0 
    fi
    # 1 = false
    return 1
} 

#
##################################################################
#
function push_image {
        IMAGE=$1
        printf "\n\n>pusging image $IMAGE\n\n"
        printf "\n\n>login -u tuulia $IMAGE\n"
        docker login -u tuulia

        REMOTE_NAME="tuulia/$IMAGE"


        docker tag $IMAGE $REMOTE_NAME
        docker push $REMOTE_NAME
        printf "\n\n> Image $REMOTE_NAME pushed to https://hub.docker.com/repositories/tuulia \n"
        press_enter_to_continue

} 


#
##################################################################
#
function install_jenkings {
    #https://hub.docker.com/r/jenkins/jenkins/
    #https://github.com/jenkinsci/docker/blob/master/README.md
     echo
       FULL_IMAGE="$JENKINGS_LOCAL_IMAGE:$JENKINGS_LOCAL_IMAGE_TAG";

        printf "\n\n>docker pull $FULL_IMAGE\n\n"
        docker pull $FULL_IMAGE
        press_enter_to_continue

        printf "\n\n>docker run --name $JENKINGS_LOCAl_CONTAINER -p 8080:8080 -p 50000:50000 --restart=on-failure jenkins/jenkins:lts-jdk17 ' \n\n"
        printf "\n\n Note: after this command  you see the message"
        printf "\n----------------------------------\n\n"
        printf "\nPlease use the following password to proceed to installation:\n\n"
        printf "\n ee8a74c50a7d47a5989057be5431cb0c \n\n"
        printf "\nThis may also be found at: /var/jenkins_home/secrets/initialAdminPassword\n"
        printf "\n----------------------------------\n\n"
     
        press_enter_to_continue
           
        docker run --name $JENKINGS_LOCAl_CONTAINER -p 8080:8080 -p 50000:50000   --privileged=true -v /usr/bin/docker:/usr/bin/docker -v /var/run/docker.sock:/var/run/docker.sock --restart=on-failure jenkins/jenkins:lts-jdk17
        printf "\nYou are ready now. You can press control + c and restart by scripts/restartJenkings.sh\n\n"
        echo    
} 


#
##################################################################
#
function install_alpine_socat {
    # see https://hub.docker.com/r/alpine/socat
     echo
        printf "\n\n>docker pull alpine/socat\n\n"
        docker pull alpine/socat
        press_enter_to_continue

        printf "\n\n>Install apline/socat, see function 'install_alpine_socat' \n\n"
        docker run -d --restart=always --name $JENKINGS_ALPINE_SOCAT_CONTAINER \
        -p 127.0.0.1:2376:2375 \
        -v /var/run/docker.sock:/var/run/docker.sock \
        alpine/socat \
        tcp-listen:2375,fork,reuseaddr unix-connect:/var/run/docker.sock
        echo

        echo $JENKINGS_ALPINE_SOCAT_CONTAINER installed, check the IP address below
        docker inspect $JENKINGS_ALPINE_SOCAT_CONTAINER  | grep 'IPAddress'
        press_enter_to_continue
    
} 

#
##################################################################
#
function install_jenkins_php {
    cd jenkinsPhp
    printf "\n\n>docker compose up -d\n\n"
    docker compose up -d
    press_enter_to_continue
    cd ..
}



#
##################################################################
#
function press_enter_to_continue {
    echo
    read -p "Press enter to continue"
    echo
} 


##############################################################################
#
# MAIN
#
#############################################################################

printf "\n\n \n \n" 


for CONTAINER in "${CONTAINERS[@]}"
do

    echo "#################################################################"
    echo "#"
    echo "#  Removing container  $CONTAINER "
    echo "#"
    echo "#################################################################"
    echo 
   

    read -n1 -p "Do you want to reinstall  $CONTAINER  [press y for yes]" doit 
    if [[ $doit == "y" ]]; then

        if [[ $CONTAINER == $JENKINGS_ALPINE_SOCAT_CONTAINER ]]; then
            remove_container $CONTAINER
            remove_image $JENKINGS_ALPINE_SOCAT_IMAGE; 
            install_alpine_socat
        fi

        if [[ $CONTAINER == $JENKINGS_LOCAl_CONTAINER ]]; then
            remove_container $CONTAINER
            remove_image $JENKINGS_LOCAL_IMAGE;  
            install_jenkings
        fi


        if [[ $CONTAINER == $JENKINS_PHP_CONTAINER ]]; then
            remove_container $CONTAINER
            remove_image $JENKINS_PHP_IMAGE
            install_jenkins_php
            push_image $JENKINS_PHP_IMAGE
        fi

    fi
done
