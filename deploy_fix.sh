#!/bin/bash

# Deploy script for dev.upg.cat
echo "=== DEPLOY A DEV.UPG.CAT ==="

# Push to production server
ssh -o IdentitiesOnly=yes -i ~/.ssh/id_rsa git@dev.upg.cat "cd /var/www/campus-org && git pull origin fix/payment-integration-webhook && php artisan cache:clear && php artisan config:clear && php artisan route:clear"

echo "✅ Deploy completado"
