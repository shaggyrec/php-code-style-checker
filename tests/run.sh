#!/bin/bash
dir=$(cd "$(dirname "$0")";pwd);

cd ${dir}/unit
php ../../vendor/bin/phpunit .
