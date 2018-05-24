#!/bin/bash
kill -9 $(lsof -t -i:5005)
cd ../../../
php yii yii2multiresponse/server/start 5005