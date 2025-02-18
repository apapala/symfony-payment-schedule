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

If you would like to drop schema:
```
php bin/console doctrine:database:drop --force
```
After that you will have to execute:
```
php bin/console doctrine:database:create && php bin/console doctrine:fixtures:load
```

### Run useful commands from within your docker container:
```
composer php-cs-fixer
composer phpstan
```

## Example usage

Request:
```
curl --location --request POST 'http://localhost:8089/api/payment-schedule/calculate' \
--header 'X-API-KEY: api-key-here' \
--header 'Content-Type: application/json' \
--data '{
    "productType": "premium_sub",
    "productName": "Premium Subscription",
    "productPrice": {
        "amount": 10001,
        "currency": "PLN"
    },
    "productSoldDate": "2024-07-15T14:30:00+02:00"
}'
```
Response:
```
{
    "status": "success",
    "data": [
        {
            "amount": 8.34,
            "currency": "PLN",
            "dueDate": "2024-07-15T12:30:00.000000+00:00"
        },
        {
            "amount": 8.34,
            "currency": "PLN",
            "dueDate": "2024-08-15T12:30:00.000000+00:00"
        },
        {
            "amount": 8.34,
            "currency": "PLN",
            "dueDate": "2024-09-15T12:30:00.000000+00:00"
        },
        {
            "amount": 8.34,
            "currency": "PLN",
            "dueDate": "2024-10-15T12:30:00.000000+00:00"
        },
        {
            "amount": 8.34,
            "currency": "PLN",
            "dueDate": "2024-11-15T12:30:00.000000+00:00"
        },
        {
            "amount": 8.33,
            "currency": "PLN",
            "dueDate": "2024-12-15T12:30:00.000000+00:00"
        },
        {
            "amount": 8.33,
            "currency": "PLN",
            "dueDate": "2025-01-15T12:30:00.000000+00:00"
        },
        {
            "amount": 8.33,
            "currency": "PLN",
            "dueDate": "2025-02-15T12:30:00.000000+00:00"
        },
        {
            "amount": 8.33,
            "currency": "PLN",
            "dueDate": "2025-03-15T12:30:00.000000+00:00"
        },
        {
            "amount": 8.33,
            "currency": "PLN",
            "dueDate": "2025-04-15T12:30:00.000000+00:00"
        },
        {
            "amount": 8.33,
            "currency": "PLN",
            "dueDate": "2025-05-15T12:30:00.000000+00:00"
        },
        {
            "amount": 8.33,
            "currency": "PLN",
            "dueDate": "2025-06-15T12:30:00.000000+00:00"
        }
    ]
}
```