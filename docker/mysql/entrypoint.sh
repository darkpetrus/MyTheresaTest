#!/bin/bash

echo "Waiting MySQL..."
until mysql -h db -u root -proot -e "SHOW DATABASES;"; do
  echo "Waiting MySQL..."
  sleep 3
done

echo "Execute migrations..."
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console app:load-products

exec php-fpm