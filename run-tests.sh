#!/bin/bash
php bin/phpunit --bootstrap vendor/autoload.php --colors  --stop-on-error --group=config src/
