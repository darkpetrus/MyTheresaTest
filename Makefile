init:
	docker-compose -f docker-compose.yml up -d --build
	docker-compose -f docker-compose.yml exec php composer install --no-interaction
	docker-compose -f docker-compose.yml exec php bin/console doctrine:migrations:migrate --no-interaction
	docker-compose -f docker-compose.yml exec php bin/console app:load-products

up:
	docker-compose -f docker-compose.yml up -d

remove:
	docker-compose -f docker-compose.yml down --volumes --remove-orphans

down:
	docker-compose -f docker-compose.yml down

bash:
	docker-compose exec php bash

test:
	docker-compose exec php bin/phpunit
