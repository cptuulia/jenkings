
#######################################################################
#
#  This is the third part to deploy
#
#  It deployes the updated files to  https://jenkins-php-pipeline.tantonius.com/
#
#  See more in in chapter  PHP Free Style on
# https://docs.google.com/document/d/1DPQVp7qVV-yVpLuZbCcUJT8H8rQ3Lu1LmwZ-Dki4AlQ/edit?tab=t.0#heading=h.23c4adaoldy0
#
#######################################################################





REPO_PATH="/var/jenkins_home/tuulia/php_pipeline/gitRepo"
PHP_PIPELINE_FTP_HOST_URL="ftp.tantonius.com/domains/tantonius.com/public_html/jenkins-php-pipeline"


cd $REPO_PATH
cd jenkings
git pull

git config git-ftp.url $PHP_PIPELINE_FTP_HOST_URL
git config git-ftp.user $tantonius_com_ftp_username
git config git-ftp.password $tantonius_com_ftp_password

# check if the config is correct 
#git config --get git-ftp.url
#git config --get git-ftp.user
#git config --get git-ftp.password

git ftp push 





