# Where's my money 🥵 - back

### Prerequisites

Install the docker stack as defined in the [tools repository](https://gitlab.com/where-is-my-moneyy/back/-/tree/main/docker)

## Install

Connect to the apache container and follow steps :

```bash
# Connect to container
docker-compose exec app bash
# OR
make app

cd back/

# Composer
composer install --no-interaction

# Install a fresh & empty database
php artisan migrate
```

## Try local

http://back-local.wimm.com:8080

## More

Mysql container available here :

```bash
# Connect to container
docker-compose exec db bash
# OR
make db
```
