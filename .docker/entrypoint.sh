#!/bin/sh

set -e

# Do first-time setup steps that can't be done in the Dockerfile
# php artisan migrate
php artisan config:cache
php artisan view:cache
php artisan storage:link


php artisan websocket:serve
## Run the main container command
exec docker-php-entrypoint "$@"
