PHP=php bin/console
SYMFONY=symfony

.PHONY: start create-db test-unit test-functional
start:
	$(SYMFONY) server:start

create-db:
	$(PHP) doctrine:database:create
	$(PHP) doctrine:migrations:migrate --no-interaction
	$(PHP) hautelook:fixtures:load --no-interaction

test-unit:
	php bin/phpunit tests/unit

test-functional:
	php bin/phpunit tests/functional
