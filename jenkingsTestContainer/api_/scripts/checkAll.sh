echo "##############################################"
echo "./scripts/phpunit.sh Tests/Integration/ "
echo "##############################################"
./scripts/phpunit.sh Tests/Integration/
read -p "Press enter to continue"
echo "##############################################"
echo "./scripts/phpunit.sh Tests/Feature/ "
echo "##############################################"
./scripts/phpunit.sh Tests/Feature/
read -p "Press enter to continue"
echo "##############################################"
echo "./scripts/phpstan.sh "
echo "##############################################"
./scripts/phpstan.sh
read -p "Press enter to continue"

echo "##############################################"
echo "./scripts/insertDemoContent.sh"
echo "##############################################"
cd ..
./scripts/resetDump.sh
cd api_
