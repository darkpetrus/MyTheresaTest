#!/bin/bash

# Linux friend-ly version without Make command

DOCKER_COMPOSE="docker-compose -f docker-compose.yml"

init() {
  $DOCKER_COMPOSE up -d --build
  $DOCKER_COMPOSE exec php composer install --no-interaction
  $DOCKER_COMPOSE exec php php bin/console doctrine:migrations:migrate --no-interaction
  $DOCKER_COMPOSE exec php php bin/console app:load-products
}

up() {
  $DOCKER_COMPOSE up -d
}

status() {
  $DOCKER_COMPOSE ps
}

remove() {
  $DOCKER_COMPOSE down --volumes --remove-orphans
}

down() {
  $DOCKER_COMPOSE down
}

bash() {
  docker-compose exec php bash
}

test() {
  docker-compose exec php php bin/phpunit
}

case "$1" in
  init)
    init
    ;;
  bash)
    bash
    ;;
  up)
    up
    ;;
  remove)
    remove
    ;;
  status)
    status
    ;;
  down)
    down
    ;;
  test)
    test
    ;;
  *)
    echo "Unrecognized command. Use 'init', 'up', 'status', 'remove', 'bash', 'test, or 'down'.".
    exit 1
    ;;
esac