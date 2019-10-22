all:
	chmod 777 cache/twig

#
# composer
#

composer-install:
	php composer.phar install

composer-update:
	php composer.phar update

composer-selfupdate:
	php composer.phar selfupdate
