# Test assignment project
Test assignment project done for one of companies that I have applied to.

## Running locally

Create `.env` file based on `.env.dist` within `./docker/php` directory.

Run project locally by executing  `docker-compose up` in your command line.

From within your docker container run:
```
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --dump-sql --force
php bin/console doctrine:fixtures:load --no-interaction
```

Create api user
```
php bin/console app:create-user admin@example.com password123 ROLE_ADMIN,ROLE_USER
```
And use generated api-key in requests to app.

Then access webapp at http://localhost:8089/.

If you would like to drop schema and recreate it with fixtures:
```
php bin/console doctrine:database:drop --force && \
php bin/console doctrine:database:create &&  \
php bin/console doctrine:schema:update --dump-sql --force && \
php bin/console doctrine:fixtures:load --no-interaction && \
php bin/console app:create-user admin@example.com password123 ROLE_ADMIN,ROLE_USER
```
Remember about creating an API user in order to execute any requests.

### Run useful commands from within your docker container:
```
composer php-cs-fixer
composer phpstan
```

### Symfony Messenger
Messenger is used within this project. In order to set up doctrine transport run
```
docker exec symfony_php bin/console messenger:transport:setup-transport
```
Then run consumer. Please note that currently below is available as a standalone docker container that runs in a background, 
so once you run `docker-compose up` you will have everything set up.
```
docker exec symfony_php php bin/console messenger:consume async -vv
```

## Usage and API doc
Run messenger consumer

Access http://localhost:8089/api/doc for an API doc.

Or copy this to Postman or other tool http://localhost:8089/api/doc.json ?