
#######################################################################
#
#  This is the first one time job to initialize the project php_shell
#
#  It initializes the git repo on Jenkins server and https://jenkins-php-pipeline.tantonius.com/
#
#  See more in in chapter  PHP Free Style on
# https://docs.google.com/document/d/1DPQVp7qVV-yVpLuZbCcUJT8H8rQ3Lu1LmwZ-Dki4AlQ/edit?tab=t.0#heading=h.23c4adaoldy0
#
#######################################################################



# to initialize run this in jenkins container, it is not recommended to run Free Style Jobs as root
mkdir -p /var/jenkins_home/tuulia/php_pipeline/gitRepo
chmod a+rw /var/jenkins_home/tuulia/php_pipeline/gitRepo

REPO_PATH="/var/jenkins_home/tuulia/php_pipeline/gitRepo"
PHP_PIPELINE_FTP_HOST_URL="ftp.tantonius.com/domains/tantonius.com/public_html/jenkins-php-pipeline"


cd $REPO_PATH

rm -rf *
git clone https://github.com/cptuulia/jenkings
cd jenkings

git config git-ftp.url $PHP_PIPELINE_FTP_HOST_URL
git config git-ftp.user $tantonius_com_ftp_username
git config git-ftp.password $tantonius_com_ftp_password

# check if the config is correct 
#git config --get git-ftp.url
#git config --get git-ftp.user
#git config --get git-ftp.password

git ftp init 





