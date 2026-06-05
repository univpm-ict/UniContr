#!/bin/bash
set -e

BUILD_DIR=$1

rm -fr "$BUILD_DIR"
mkdir -p "$BUILD_DIR"

tar \
    --exclude="./$BUILD_DIR" \
    --exclude='./temp' \
    --exclude='./.git' \
    --exclude='./tests' \
    --exclude='./node_modules' \
    --exclude='./.env' \
    --exclude='./.env.example' \
    --exclude='./storage/app' \
    --exclude='./storage/logs' \
    --exclude='./storage/framework' \
    --exclude='./storage/backups' \
    --exclude='./storage/tempdir' \
    --exclude='./vendor' \
    --exclude='./Jenkinsfile' \
    --exclude='./DockerFile.build' \
    --exclude='./DockerFile.deploy' \
    --exclude='./build.sh' \
    --exclude='./deploy.sh' \
    --exclude='./phpunit.xml' \
    --exclude='./phpunit.xml.bak' \
    --exclude='./_ide_helper.php' \
    --exclude='./.vscode' \
    --exclude='./coverage' \
    --exclude='./sp.key' \
    --exclude='./sp.crt' \
    -cf - . | tar -xf - -C "$BUILD_DIR"

cd "$BUILD_DIR"
composer install --no-dev --optimize-autoloader --no-interaction

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/backups \
    bootstrap/cache
