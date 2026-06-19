#/bin/bash
 
set -e
 
BUILD_DIR=$1
SERVER_SSH_KEYFILE=$2
SERVER_USERNAME=$3
SERVER_NAME=$4
SERVER_DEPLOY_DIR=$5
SERVER_SSH_PORT=$6
 
echo 'creating tmp dir into remote server...'
SERVER_TMP_DIR=$(ssh -i $SERVER_SSH_KEYFILE -p $SERVER_SSH_PORT $SERVER_USERNAME@$SERVER_NAME "mktemp -d")
 
echo 'uploading application to tmp dir...'
scp -r -P $SERVER_SSH_PORT -i $SERVER_SSH_KEYFILE $BUILD_DIR/. $SERVER_USERNAME@$SERVER_NAME:$SERVER_TMP_DIR/
 
echo 'deleting application...'
ssh -i $SERVER_SSH_KEYFILE -p $SERVER_SSH_PORT $SERVER_USERNAME@$SERVER_NAME "rm -fr $SERVER_DEPLOY_DIR/*"
 
echo "copying remote tmp dir (which now includes secrets) into actual remote deploy dir..."
ssh -i "$SERVER_SSH_KEYFILE" -p "$SERVER_SSH_PORT" "$SERVER_USERNAME"@"$SERVER_NAME" "cp -dR $SERVER_TMP_DIR/. $SERVER_DEPLOY_DIR"
 
echo 'removing tmp dir...'
ssh -i $SERVER_SSH_KEYFILE -p $SERVER_SSH_PORT $SERVER_USERNAME@$SERVER_NAME "rm -fr $SERVER_TMP_DIR"