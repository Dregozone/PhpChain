#!/bin/bash

echo "Starting node for user $USER"
if [ "$PEER" == "" ]; then
    killall php
else
    echo "Bootstrapping network with node $PEER"
    peerPort=`cat data/$PEER.port`
fi
rm -rf data/$USER.json
port=8000
retry=30
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
php -S localhost:$port &
echo ""
php gossip.php $port $peerPort
