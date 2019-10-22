all:
	@sh -c 'for f in *Test.php; do echo $$f; ../vendor/bin/phpunit `basename $$f .php` || exit 1; done'

clean:
	rm -rf tmp
