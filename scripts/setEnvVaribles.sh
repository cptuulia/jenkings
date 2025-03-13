
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


###############################################################################
# shell script on jenkins side
##  
: '
#git clone https://github.com/cptuulia/jenkings.git
#ls
#rm -rf jenkings
export JENKINS_ENV_PATH=/var/jenkins_home/tuulia/php_pipeline/environment/

 PHP_PIPELINE_FTP_HOST_URL=$(more "$JENKINS_ENV_PATH/PHP_PIPELINE_FTP_HOST_URL")
 PHP_PIPELINE_FTP_HOST_URL=$(more "$JENKINS_ENV_PATH/PHP_PIPELINE_FTP_USER")
 PHP_PIPELINE_FTP_HOST_URL= 
echo $PHP_PIPELINE_FTP_HOST_URL
echo $PHP_PIPELINE_FTP_USER
echo $PHP_PIPELINE_FTP_PASSWORD


git config git-ftp.user $PHP_PIPELINE_FTP_USER
    git config git-ftp.url $PHP_PIPELINE_FTP_HOST_URL
    git config git-ftp.password $PHP_PIPELINE_FTP_PASSWORD

git ftp init    

#git ftp    push


'   

### PIPLINE
:' 

        pipeline {
            agent any

               environment {
                GIT_BRANCH = 'main'
                GIT_REPO_URL = 'https://github.com/cptuulia/jenkings'
                JENKINS_ENV_PATH='/var/jenkins_home/tuulia/php_pipeline/environment'
               
            }

           
            stages {

                 stage('Clone Repository') {
                steps {
                    script {
                        echo "Cloning repository from GitHub..."
                        git url: "${env.GIT_REPO_URL}", branch: "${env.GIT_BRANCH}"  
                        echo "Repository is cloned"                   
                    }
                }
            }

                stage('Deploy') {
                    steps {
                        script {
                        def PHP_PIPELINE_FTP_HOST_URL= sh(returnStdout: true, script: 'cat "$JENKINS_ENV_PATH/PHP_PIPELINE_FTP_HOST_URL"')
                        def PHP_PIPELINE_FTP_USER= sh(returnStdout: true, script: 'cat "$JENKINS_ENV_PATH/PHP_PIPELINE_FTP_USER"')
                        def PHP_PIPELINE_FTP_PASSWORD= sh(returnStdout: true, script: 'cat "$JENKINS_ENV_PATH/PHP_PIPELINE_FTP_PASSWORD"')
                          
                        echo "TEST  $JENKINS_ENV_PATH  sss ${PHP_PIPELINE_FTP_PASSWORD } "
                        echo "PHP_PIPELINE_FTP_HOST_URL ${PHP_PIPELINE_FTP_HOST_URL} "
                        echo "PHP_PIPELINE_FTP_USER ${PHP_PIPELINE_FTP_USER} "
                        echo "PHP_PIPELINE_FTP_PASSWORD ${PHP_PIPELINE_FTP_PASSWORD} "
                                

                        sh('git config git-ftp.user "${PHP_PIPELINE_FTP_USER}"') 
                        sh('git config git-ftp.url $PHP_PIPELINE_FTP_USER')
                        sh('git config git-ftp.password "${PHP_PIPELINE_FTP_PASSWORD}"')
                        sh('git config --get git-ftp.url')
                       // sh('git ftp init')           
                        }
                    }
                }                
            }

        }
'
