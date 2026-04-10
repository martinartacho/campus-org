#!/bin/bash

# Script ràpid per processar només cua d'imports
echo "Processant jobs d'imports pendents..."
echo "----------------------------------------"

cd /var/www/campus.upg.cat
php artisan queue:work --queue=imports --timeout=60 --max-time=300
