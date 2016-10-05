#!/usr/bin/env bash
php artisan queue:work --daemon --queue=crawling &

echo "running crawler"

php artisan queue:work --daemon --queue=logging &

echo "running logger"

php artisan queue:work --daemon --queue=mailing &

echo "running mailer"

php artisan queue:work --daemon --queue=alerting &

echo "running alert"

php artisan queue:work --daemon --queue=syncing &

echo "running syncer"

php artisan queue:work --daemon --queue=reporting &

echo "running report"

