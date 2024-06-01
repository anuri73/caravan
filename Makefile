DC=docker-compose
UP=@$(DC) -f docker-compose.yml up -d

export

# Build docker compose containers
build-and-up:
	@$(UP) --build --remove-orphans

# Copy .env file
copy-docker-compose:
	@$(RUN) cp -n docker-compose.yml.dist docker-compose.yml || true

# Copy .env file
copy-env:
	@$(RUN) cp -n .env.dist .env || true

# install : Install project
install:
	make copy-env
	make copy-docker-compose
	make build-and-up

pull:
	@$(DC) pull

# Up: Mount the containers
up:
	@$(UP) -f docker-compose.yml

