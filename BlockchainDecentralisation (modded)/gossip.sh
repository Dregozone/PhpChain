#!/bin/bash

echo "Starting node for user $USER"
if [ "$PEER" == "" ]; then
    killall php
else
    echo "Bootstrapping network with node $PEER"
    peerPort=`cat BlockchainDecentralisation/data/$PEER.port`
fi
rm -rf BlockchainDecentralisation/data/$USER.json
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

echo $port > BlockchainDecentralisation/data/$USER.port
php -S localhost:$port &
echo ""
php BlockchainDecentralisation/gossip.php $port $peerPort
