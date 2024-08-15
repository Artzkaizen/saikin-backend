#!/bin/bash

# Copy contents of .env.example to .env
cp .env.example .env

# Get configs for .env file
echo "Hello, and welcome to OV setup!"
echo "Quickly configure your .env file settings."

read -r -p 'APP_NAME[Laravel]:' APP_NAME
APP_NAME=${APP_NAME:-'Laravel'}
APP_NAME=${APP_NAME//[\"\']/}
APP_NAME="\"$APP_NAME\""

read -r -p 'APP_URL[http://localhost]:' APP_URL
APP_URL=${APP_URL:-'http://localhost'}
APP_URL=${APP_URL//\//\\\/}
APP_URL=${APP_URL//[\"\']/}
APP_URL="\"$APP_URL\""

read -r -p 'DB_HOST[db]:' DB_HOST
DB_HOST=${DB_HOST:-'db'}
DB_HOST=${DB_HOST//[\"\']/}
DB_HOST="\"$DB_HOST\""

read -r -p 'DB_PORT[3306]:' DB_PORT
DB_PORT=${DB_PORT:-'3306'}
DB_PORT=${DB_PORT//[\"\']/}
DB_PORT="\"$DB_PORT\""

read -r -p 'DB_DATABASE[laravel]:' DB_DATABASE
DB_DATABASE=${DB_DATABASE:-'laravel'}
DB_DATABASE=${DB_DATABASE//[\"\']/}
DB_DATABASE="\"$DB_DATABASE\""

read -r -p 'DB_USERNAME[root]:' DB_USERNAME
DB_USERNAME=${DB_USERNAME:-'root'}
DB_USERNAME=${DB_USERNAME//[\"\']/}
DB_USERNAME="\"$DB_USERNAME\""

read -r -p 'DB_PASSWORD[laravel]:' DB_PASSWORD
DB_PASSWORD=${DB_PASSWORD:-'laravel'}

# Write configs to .env file
echo "Currently writing config to .env file."

# Fix permissions if necessary
chmod 755 .
chmod 644 .env

# Use sudo if needed, and replace sed command to avoid permission issues
sed -i.bak "s/^APP_NAME.*/APP_NAME=$APP_NAME/" ./.env
sed -i.bak "s/^APP_URL.*/APP_URL=$APP_URL/" ./.env
sed -i.bak "s/^DB_HOST.*/DB_HOST=$DB_HOST/" ./.env
sed -i.bak "s/^DB_PORT.*/DB_PORT=$DB_PORT/" ./.env
sed -i.bak "s/^DB_DATABASE.*/DB_DATABASE=$DB_DATABASE/" ./.env
sed -i.bak "s/^DB_USERNAME.*/DB_USERNAME=$DB_USERNAME/" ./.env
sed -i.bak "s/^DB_PASSWORD.*/DB_PASSWORD=$DB_PASSWORD/" ./.env

echo "Completed writing config to .env file."

# Waiting for MySQL to be available
echo "Waiting for MySQL to be available..."
for i in {1..30}; do
  if mysql -h db -u root -plaravel -e "SELECT 1" > /dev/null 2>&1; then
    echo "MySQL is available!"
    break
  fi
  echo "Waiting for MySQL..."
  sleep 5
done

# Install project dependencies
composer install

# Generate application key and JWT secret
php artisan key:generate
php artisan jwt:secret

# Allow public access to Laravel storage folder
echo "Currently enabling public access to Laravel storage folder."

chmod -R 777 ./storage/app
chmod -R 777 ./storage/framework
chmod -R 777 ./storage/logs

echo "Completed public access to Laravel storage folder."



php artisan migrate --force
php artisan db:seed --force

rm ./.env.bak

# Start Apache in the foreground
apache2-foreground
