#!/bin/bash

echo "Joining network as $USER"

if [ "$PEER" == "" ]; then
    killall php
else
    echo "Gossiping with $PEER"
    peerPort=`cat data/$PEER.port`
fi

rm -rf data/$USER.json

port=8000
retry=30
gossipPort=0
appPort=0

while [ $retry -gt 0 ]
do
    if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null ; then
        let retry-=1
        let port+=1
    else
        break
    fi
done

echo $port > data/$USER.port

let gossipPort+=$port
let appPort+=$gossipPort
let appPort+=80

php -S localhost:$gossipPort & #For the gossip
#echo ""
php gossip.php $port $peerPort & 
#echo ""
php -S localhost:$appPort -t ../ & #For the application
#echo ""
firefox localhost:$appPort/MESApplication
