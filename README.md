# Test assignment project
Test assignment project done for one of companies that I have applied to.

## Running locally

Create `.env` file based on `.env.dist` within `./docker/php` directory.

Run project locally by executing  `docker-compose up` in your command line.

Then access webapp at http://localhost:8089/.

Run useful commands from within your docker container:
```
composer php-cs-fixer
composer phpstan
```