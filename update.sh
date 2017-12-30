#!/bin/sh

git pull
sudo rm -rf cache/twig/*
find * -uid `id -u` -type f ! -ipath 'cache/*' ! -ipath '*/tmp/*' | xargs chmod 644
find * -uid `id -u` -type d ! -ipath 'cache/*' ! -ipath '*/tmp/*' | xargs chmod 755
find * -uid `id -u` -name '*.sh' | xargs chmod 755
chmod 777 cache/twig
