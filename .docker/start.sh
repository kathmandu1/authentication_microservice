#!/bin/sh

set -e

echo "docker service running entry point"

# Do first-time setup steps that can't be done in the Dockerfile
# php artisan config:cache
# php artisan view:cache
# php artisan storage:link

# php artisan websocket:serve
echo "Running the queue..."
php /var/www/html/artisan queue:work --verbose --tries=3 --timeout=90
# php /var/www/html/artisan queue:work --verbose --tries=3 --timeout=90

# php /var/www/html/artisan websocket:serve --verbose
## Run the main container command
# exec docker-php-entrypoint "$@"
