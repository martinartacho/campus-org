#!/bin/bash

# Script per iniciar worker de cua Laravel manualment
echo "Iniciant worker de cua Laravel..."
echo "Prem Ctrl+C per aturar"
echo "----------------------------------------"

cd /var/www/campus.upg.cat
php artisan queue:work --queue=imports,default --sleep=3 --timeout=60 --memory=256
