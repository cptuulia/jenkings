
. ./scripts/buildPipeLine/data.sh
echo ////////////////////////////////////////////////////
echo //
echo // Executing script with
echo // User $JENKINS_USER
echo // api key $JENKINS_USER_API_KEY
echo // pileline $JENKINS_PIPELINE
echo //
echo // "curl -u $JENKINS_USER:$JENKINS_USER_API_KEY -FSubmit=Build 'http://localhost:8080/job/$JENKINS_PIPELINE/build?delay=0sec'"
echo //
echo //
echo // Please check. 
echo // Modify  scripts/buildPipeLine/data.sh if needed
echo //
echo ////////////////////////////////////////////////////
echo
echo "Press any key to continue"
read -n 1 -s -r -p ""
#curl -u $USER:$API_KEY -FSubmit=Build 'http://localhost:8080/job/$PIPELINE/build?delay=0sec'