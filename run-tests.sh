#!/bin/bash
php bin/phpunit --bootstrap vendor/autoload.php --colors  --stop-on-error --group=Vcs src/
php bin/phpunit --bootstrap vendor/autoload.php --colors  --stop-on-error --group=Command src/
