#!/bin/bash

dir=$(cd "$(dirname "$0")";pwd);

cd ${dir}
/usr/bin/php7.2 ./../../vendor/bin/phpunit $* .
