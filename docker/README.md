# Local installation for development

## Prerequisites

Install Docker

Install docker-compose

### Mac

Install Docker for Mac : https://store.docker.com/editions/community/docker-ce-desktop-mac

## Source code

Get the source code of the other projets that you need:

```bash
# Back (Laravel)
git clone https://gitlab.com/where-is-my-moneyy/back
# Front (React + TS)
git clone https://gitlab.com/where-is-my-moneyy/front
```

## Stack

Add in your hosts file:

```
127.0.0.1 back-local.wimm.com front-local.wimm.com
```

Run the docker stack:

```bash
cd back/
docker-compose build
# OR
make build

docker-compose up -d
# OR
make dev
```

## Applications

Check this URL:

http://back-local.wimm.com:8080

If you want to install one of the apps:

- [Back](https://gitlab.com/where-is-my-moneyy/back)
- [Front](https://gitlab.com/where-is-my-moneyy/front)

If you need to open a bash on one of the containers:

```bash
# App server
docker-compose exec app bash
# OR
make app
```
