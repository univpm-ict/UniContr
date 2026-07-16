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
umask 0002

mkdir -p '$SERVER_TEMP_DIR' '$SERVER_DEPLOY_DIR'
cd '$SERVER_DEPLOY_DIR'

echo '  * saving useful files into temp dir'
if [ -f .env ]; then
    cp .env '$SERVER_TEMP_DIR/.env'
fi

if [ -d storage/app ]; then
    cp -a storage/app '$SERVER_TEMP_DIR/storage-app'
fi

if [ -d storage/logs ]; then
    cp -a storage/logs '$SERVER_TEMP_DIR/storage-logs'
fi

if [ -d '$SAML_CERTS_DIR' ]; then
    mkdir -p '$SERVER_TEMP_DIR/saml-certs'
    cp -a '$SAML_CERTS_DIR/.' '$SERVER_TEMP_DIR/saml-certs/'
fi

echo '  * removing old files from deploy dir'
find . -mindepth 1 -maxdepth 1 -exec rm -rf {} +

echo '  * extracting build results into temp dir'
tar -xzf '$SERVER_TEMP_DIR.tar.gz' -C '$SERVER_TEMP_DIR'

echo '  * copying build results into deploy dir'
cp -dR '$SERVER_TEMP_DIR/$BUILD_DIR/.' .

echo '  * restoring useful files from temp dir'
if [ -f '$SERVER_TEMP_DIR/.env' ]; then
    cp '$SERVER_TEMP_DIR/.env' .env
    echo '  * .env OK'
fi

if [ -d '$SERVER_TEMP_DIR/storage-app' ]; then
    mkdir -p storage/app
    cp -a '$SERVER_TEMP_DIR/storage-app/.' storage/app/
    echo '  * storage/app OK'
fi

if [ -d '$SERVER_TEMP_DIR/storage-logs' ]; then
    mkdir -p storage/logs
    cp -a '$SERVER_TEMP_DIR/storage-logs/.' storage/logs/
    echo '  * storage/logs OK'
fi

if [ -d '$SERVER_TEMP_DIR/saml-certs' ]; then
    mkdir -p '$SAML_CERTS_DIR'
    cp -a '$SERVER_TEMP_DIR/saml-certs/.' '$SAML_CERTS_DIR/'
    echo '  * saml-certs OK'
fi

if [ ! -f .env ]; then
    echo 'ERROR: .env not found on server — copy it manually before deploying'
    exit 1
fi

echo '  * initializing app directories'
mkdir -p bootstrap/cache storage/app/public storage/framework/cache/data \
         storage/framework/sessions storage/framework/views storage/logs storage/backups
chmod -R ug+rwx storage bootstrap/cache

echo '  * initializing app'
php artisan storage:link --force
php artisan optimize:clear

echo '  * cleaning'
rm -rf '$SERVER_TEMP_DIR' '$SERVER_TEMP_DIR.tar.gz'
"

rm -f "$ARCHIVE_FILE"

echo 'deploy completed successfully'
