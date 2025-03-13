######################################################################################
#
#  Set the envrironmet variables as text files to jenkins container
#  where jenkins can read the and define as variables.
# 
#  This should be done by permanent env variables....  But how?
#
#  See how the env variales are used in 
#  jenkinsPhp/jenkingsfile and jenkinsPhp/jenkinsfile.sh 
#  in the sections Deploy  
######################################################################################



export PHP_PIPELINE_FTP_HOST_URL=ftp.tantonius.com/domains/tantonius.com/public_html/jenkins-php-pipeline
export PHP_PIPELINE_FTP_USER=xxxx
export PHP_PIPELINE_FTP_PASSWORD=xxxxxx


export JENKINS_ENV_PATH=/var/jenkins_home/tuulia/php_pipeline/environment
#
##################################################################
#
function set_variable_to_container {
    VARIABLE=$1
    FILE_NAME="$VARIABLE"
    VALUE="${!VARIABLE}"
    
    echo "$VALUE" >$FILE_NAME
    docker cp $FILE_NAME "jenkings_local:$JENKINS_ENV_PATH/$FILE_NAME"
    docker exec -it --user root jenkings_local chmod a+x "$JENKINS_ENV_PATH/$FILE_NAME"
    rm $FILE_NAME
} 



docker exec -it --user root jenkings_local mkdir -p $JENKINS_ENV_PATH
docker exec -it --user root jenkings_local chmod a+rw $JENKINS_ENV_PATH

set_variable_to_container "PHP_PIPELINE_FTP_HOST_URL"   
set_variable_to_container "PHP_PIPELINE_FTP_USER" 
set_variable_to_container "PHP_PIPELINE_FTP_PASSWORD" 


docker exec -it jenkings_local ls $JENKINS_ENV_PATH

