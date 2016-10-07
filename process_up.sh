#!/usr/bin/env bash
php artisan queue:work --daemon --tries=3 --queue=crawling &

echo "running crawler"

php artisan queue:work --daemon --tries=3 --queue=logging &

echo "running logger"

php artisan queue:work --daemon --tries=3 --queue=mailing &

echo "running mailer"

php artisan queue:work --daemon --tries=3 --queue=alerting &

echo "running alert"

php artisan queue:work --daemon --tries=3 --queue=syncing &

echo "running syncer"

php artisan queue:work --daemon --tries=3 --queue=reporting &

echo "running report"

php artisan queue:work --daemon --tries=3 --queue=deleting &

echo "running delete"
