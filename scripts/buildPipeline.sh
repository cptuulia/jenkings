

. ./scripts/buildPipeLine/data.sh

NEXT_BUILD_NUMBER=$(docker exec jenkings_local tail /var/jenkins_home/jobs/$JENKINS_PIPELINE/nextBuildNumber)   

JENKINS_DELAY_SEC=$JENKINS_DELAY"sec"

LINK="http://localhost:8080/job/$JENKINS_PIPELINE/"
echo ////////////////////////////////////////////////////
echo //
echo // Executing script with
echo // User $JENKINS_USER
echo // api key $JENKINS_USER_API_KEY
echo // pipeline $JENKINS_PIPELINE
echo // delay $JENKINS_DELAY_SEC 
echo  // NEXT_BUILD_NUMBER $NEXT_BUILD_NUMBER
echo //
echo // "curl -u $JENKINS_USER:$JENKINS_USER_API_KEY -FSubmit=Build 'http://localhost:8080/job/$JENKINS_PIPELINE/build?delay=$JENKINS_DELAY_SEC'"
echo //
echo //
echo // Please check. 
echo // Modify  scripts/buildPipeLine/data.sh if needed
echo //
echo // You also check the status on echo Build started, you can follow on 
echo -e "\e]8;;$LINK\e\\// $LINK   \e]8;;\e\\"
echo // It works better of no running builds
echo ////////////////////////////////////////////////////
echo
echo "Press any key to continue"
read -n 1 -s -r -p ""

curl -u $JENKINS_USER:$JENKINS_USER_API_KEY -FSubmit=Build "http://localhost:8080/job/$JENKINS_PIPELINE/build?delay=$JENKINS_DELAY_SEC"

echo
JENKINS_DELAY=$(($JENKINS_DELAY + 10))
echo Build started, you can follow on:
echo -e "\e]8;;$LINK\e\\ $LINK   \e]8;;\e\\"
echo sleep $JENKINS_DELAY  seconds  before executing: docker exec jenkings_local tail -f "/var/jenkins_home/jobs/php-pipeline/builds/$NEXT_BUILD_NUMBER/log"
echo 
echo if this crashes try the web page or manual execution of the command... Sometimes there are delays
sleep $JENKINS_DELAY
docker exec jenkings_local tail -f "/var/jenkins_home/jobs/php-pipeline/builds/$NEXT_BUILD_NUMBER/log"