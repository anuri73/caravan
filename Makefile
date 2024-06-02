include .env.dist

CONTAINER_NGINX=${ENV_CONTAINER_NGINX}
CONTAINER_PHP=${ENV_CONTAINER_PHP_FPM}
DC=docker-compose
DC_PHP=@$(DC) exec $(CONTAINER_PHP)
DC_PHP_COMPOSER_INSTALL=$(DC_PHP) composer
UP=@$(DC) -f docker-compose.yml up -d

export

# Build docker compose containers
build-and-up:
	@$(UP) --build --remove-orphans

# Copy docker-compose.yml file
copy-docker-compose:
	@$(RUN) cp -n docker-compose.yml.dist docker-compose.yml || true

# Copy .env file
copy-env:
	@$(RUN) cp -n .env.dist .env || true

# Create web network
create-web-network:
	@$(RUN) docker network create web || true

# Down containers
down:
	@$(DC) down

# install : Install project
install:
	make copy-env
	make copy-docker-compose
	make create-web-network
	make build-and-up
	make php-composer-install

nginx-ssh:
	@$(DC) exec $(CONTAINER_NGINX) bash

php-composer-install:
	@$(DC_PHP_COMPOSER_INSTALL) install --no-scripts --no-suggest -o

php-composer-clear-var:
	@$(DC_PHP) rm -rf var

php-composer-clear-vendor:
	@$(DC_PHP) rm -rf vendor

php-ssh:
	@$(DC) exec $(CONTAINER_PHP) bash

# Show container statuses
ps:
	@$(DC) ps

# Pull images
pull:
	@$(DC) pull

# Reset project settings and data
remove:
	make php-composer-clear-var
	make php-composer-clear-vendor
	make down
	make remove-web-network
	make remove-docker-compose
	make remove-env

# Ropy docker-compose.yml file
remove-docker-compose:
	@$(RUN) rm -rf docker-compose.yml

# Remove .env file
remove-env:
	@$(RUN) rm -rf .env

# Remove web network
remove-web-network:
	@$(RUN) docker network remove web || true

# Up: Mount the containers
up:
	@$(UP) -f docker-compose.yml

