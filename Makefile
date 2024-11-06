
test:
	./vendor/bin/phpunit

stat-analise:
	./vendor/bin/phpstan analyse src -l 7

snif-test:
	 ./vendor/bin/phpcs src/Initializers/MongoPoolInitializer.php