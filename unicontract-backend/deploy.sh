#!/bin/bash
set -e

BUILD_DIR=$1
SERVER_SSH_KEYFILE=$2
SERVER_USERNAME=$3
SERVER_NAME=$4
SERVER_DEPLOY_DIR=$5
SERVER_SSH_PORT=$6

ARCHIVE_FILE="$BUILD_DIR.tar.gz"
SERVER_TEMP_DIR="/tmp/unicontr-deploy-$(uuidgen)"
SAML_CERTS_DIR="vendor/onelogin/php-saml/certs"

echo 'archiving build results...'
tar -czf "$ARCHIVE_FILE" "$BUILD_DIR"

echo 'deploying on remote server...'
scp -P "$SERVER_SSH_PORT" -i "$SERVER_SSH_KEYFILE" "$ARCHIVE_FILE" "$SERVER_USERNAME@$SERVER_NAME:$SERVER_TEMP_DIR.tar.gz"

ssh -i "$SERVER_SSH_KEYFILE" -p "$SERVER_SSH_PORT" "$SERVER_USERNAME@$SERVER_NAME" "
set -e

echo '  * saving useful files into temp dir /tmp/unicontr-deploy-$(uuidgen)'
mkdir -p '$SERVER_TEMP_DIR'

if [ -f '$SERVER_DEPLOY_DIR/.env' ]; then
    cp '$SERVER_DEPLOY_DIR/.env' '$SERVER_TEMP_DIR/.env'
fi

if [ -d '$SERVER_DEPLOY_DIR/storage/app' ]; then
    cp -a '$SERVER_DEPLOY_DIR/storage/app' '$SERVER_TEMP_DIR/storage-app'
fi

if [ -d '$SERVER_DEPLOY_DIR/storage/logs' ]; then
    cp -a '$SERVER_DEPLOY_DIR/storage/logs' '$SERVER_TEMP_DIR/storage-logs'
fi

if [ -d '$SERVER_DEPLOY_DIR/storage/backups' ]; then
    cp -a '$SERVER_DEPLOY_DIR/storage/backups' '$SERVER_TEMP_DIR/storage-backups'
fi

if [ -d '$SERVER_DEPLOY_DIR/$SAML_CERTS_DIR' ]; then
    mkdir -p '$SERVER_TEMP_DIR/saml-certs'
    cp -a '$SERVER_DEPLOY_DIR/$SAML_CERTS_DIR/.' '$SERVER_TEMP_DIR/saml-certs/'
fi

if [ -f '$SERVER_DEPLOY_DIR/artisan' ]; then
    echo '  * clearing app cache'
    php $SERVER_DEPLOY_DIR/artisan cache:clear
fi

echo '  * removing old files from deploy dir'
rm -rf $SERVER_DEPLOY_DIR/*

echo '  * extracting build results into temp dir'
tar -xzf '$SERVER_TEMP_DIR.tar.gz' -C '$SERVER_TEMP_DIR'
echo '  * copying build results into deploy dir'
cp -dR '$SERVER_TEMP_DIR/$BUILD_DIR/.' '$SERVER_DEPLOY_DIR/'

echo '  * restoring useful files from temp dir into deploy dir'
if [ -f '$SERVER_TEMP_DIR/.env' ]; then
    cp '$SERVER_TEMP_DIR/.env' '$SERVER_DEPLOY_DIR/.env'
    echo '  * .env OK'
fi

if [ -d '$SERVER_TEMP_DIR/storage-app' ]; then
    mkdir -p '$SERVER_DEPLOY_DIR/storage'
    cp -a '$SERVER_TEMP_DIR/storage-app/.' '$SERVER_DEPLOY_DIR/storage/app/'
    echo '  * storage/app OK'
fi

if [ -d '$SERVER_TEMP_DIR/storage-logs' ]; then
    mkdir -p '$SERVER_DEPLOY_DIR/storage'
    cp -a '$SERVER_TEMP_DIR/storage-logs/.' '$SERVER_DEPLOY_DIR/storage/logs/'
    echo '  * storage/logs OK'
fi

if [ -d '$SERVER_TEMP_DIR/storage-backups' ]; then
    mkdir -p '$SERVER_DEPLOY_DIR/storage'
    cp -a '$SERVER_TEMP_DIR/storage-backups/.' '$SERVER_DEPLOY_DIR/storage/backups/'
    echo '  * storage/backups OK'
fi

if [ -d '$SERVER_TEMP_DIR/saml-certs' ]; then
    mkdir -p '$SERVER_DEPLOY_DIR/$SAML_CERTS_DIR'
    cp -a '$SERVER_TEMP_DIR/saml-certs/.' '$SERVER_DEPLOY_DIR/$SAML_CERTS_DIR/'
    echo '  * saml-certs OK'
fi

echo '  * initializing app and directories'
cd '$SERVER_DEPLOY_DIR'
mkdir -p bootstrap/cache storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/backups
chmod -R ug+rwx storage bootstrap/cache

if [ ! -f .env ]; then
    echo 'ERROR: .env not found on server — copy it manually before deploying'
    exit 1
fi

php artisan storage:link --force
php artisan migrate --force
php artisan optimize:clear
php artisan optimize

echo '  * cleaning'
rm -rf '$SERVER_TEMP_DIR' '$SERVER_TEMP_DIR.tar.gz'
"

rm -f "$ARCHIVE_FILE"

echo 'deploy completed successfully'
