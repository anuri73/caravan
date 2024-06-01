DC=docker-compose
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

# Show container statuses
ps:
	@$(DC) ps

# Pull images
pull:
	@$(DC) pull

# Reset project settings and data
remove:
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

