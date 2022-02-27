.PHONY: testbench

EXEC="docker-compose exec -u app workspace"

uid=1000
gid=1000

## Docker
up:
	docker-compose up -d

start: up

down:
	docker-compose down

stop: down

dcbuild:
	docker-compose build

composer:
	"$(EXEC)" composer $(cmd)

## Artisan
artisan: testbench

testbench:
	"$(EXEC)" php vendor/bin/testbench $(cmd)

## Testing
phpcsfixer:
	"$(EXEC)" vendor/bin/php-cs-fixer fix

phpstan:
	"$(EXEC)" php -d memory_limit=-1 vendor/bin/phpstan analyze

test:
	"$(EXEC)" vendor/bin/testbench package:test

test-coverage:
	"$(EXEC)" vendor/bin/testbench package:test --coverage-text

test-all: phpstan phpcsfixer test

## Npm
npm:
	"$(EXEC)" npm $(cmd)

npm-dev:
	"$(EXEC)" npm run dev

npm-watch:
	"$(EXEC)" npm run watch

npm-prod:
	"$(EXEC)" npm run prod

## Misc
sh:
	"$(EXEC)" sh
