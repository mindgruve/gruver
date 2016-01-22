#!/bin/bash
php $(dirname $0)/bin/phpunit --bootstrap vendor/autoload.php --colors  --stop-on-error --group=config src/
