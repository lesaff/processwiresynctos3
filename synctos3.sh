#!/bin/bash

# Variables
BUCKET_NAME=$1
LOCAL_DIRECTORY=$2 
AWS_REGION=$3
LOG_FILE="${2}/public/site/assets/logs/synctos3.txt"
# Authentication
export AWS_ACCESS_KEY_ID=$4
export AWS_SECRET_KEY=$5
# Sync command
aws s3 sync "$LOCAL_DIRECTORY" "s3://$BUCKET_NAME/site/" --region "$AWS_REGION" --exclude "*" --include "*.css" --include "*.js" --include "*.jpg" --include "*.jpeg" --include "*.png" --include "*.gif" --include "*.svg"  --include "*.webp"  --include "*.pdf" --include "*.docx" --include "*.pptx"  --include "*.xlsx" --include "*.woff" --include "*.woff2" --include "*.ttf" --include "*.otf" --include "*.eot" --include "*.ico" --include "*.csv" --delete 

# Optional: Log output
echo "AWS S3 background sync completed."