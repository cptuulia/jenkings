
echo Witjh this script you can run as an example a php sctipt jenkingsTestContainer/phpinfo.php
echo by the container jenkingstestcontainer-php
echo " docker run -it --rm --name tuulia_test -v "$PWD":/usr/src/myapp -w /usr/src/myapp jenkingstestcontainer-php php  jenkingsTestContainer/phpinfo.php"
docker run -it --rm --name tuulia_test -v "$PWD":/usr/src/myapp -w /usr/src/myapp jenkingstestcontainer-php php  jenkingsTestContainer/phpinfo.php
