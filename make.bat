@echo off

goto :comment
Windows friend-ly version without Make command and without sh available
:comment


SET COMPOSE_FILE=docker-compose.yml

IF "%1"=="init" (
    docker-compose -f %COMPOSE_FILE% up -d --build
    docker-compose -f %COMPOSE_FILE% exec php composer install --no-interaction
    docker-compose -f %COMPOSE_FILE% exec php bin/console doctrine:migrations:migrate --no-interaction
    docker-compose -f %COMPOSE_FILE% exec php bin/console app:load-products
) ELSE IF "%1"=="up" (
    docker-compose -f %COMPOSE_FILE% up -d
) ELSE IF "%1"=="status" (
    docker-compose -f %COMPOSE_FILE% ps
) ELSE IF "%1"=="remove" (
      docker-compose -f %COMPOSE_FILE% down --volumes --remove-orphans
) ELSE IF "%1"=="down" (
    docker-compose -f %COMPOSE_FILE% down
) ELSE IF "%1"=="bash" (
      docker-compose exec php bash
) ELSE IF "%1"=="test" (
        docker-compose exec php bin/phpunit
) ELSE (
    echo "Unrecognized command. Use 'init', 'up', 'status', 'bash', 'remove', 'test' or 'down'.".
    exit /b 1
)

