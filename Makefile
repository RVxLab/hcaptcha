.PHONY: testbench

EXEC="docker-compose exec -u app workspace"

uid := $(shell id -u)
gid := $(shell id -g)

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

## Misc
sh:
	"$(EXEC)" sh
