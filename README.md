# Bear-it
Project management tool for task and time management.

## Project building

First create your development environment, by creating `docker-compse.yml` file and `dev` folder.
```bash
cp docker-compose.dev.yml docker-compose.yml
cp -r dev.sample dev
```
OR (symlink version)
```bash
ln -s docker-compose.dev.yml docker-compose.yml
ln -s dev.sample dev
```

Run containers:
```bash
docker-compose up -d
```

Install composer packages:
```bash
docker-compose exec -u bear-it server bash -c "composer install"
```

Prepare database:
```bash
# doctrine:database:create
docker-compose exec -u bear-it server bash -c "./bin/console d:d:c"
# doctrine:schema:update
docker-compose exec -u bear-it server bash -c "./bin/console d:s:u -f"
```

Your project should be ready to go. Try by entering http://localhost/ in your browser.

## Setup app login 
Application uses github oauth, so in order to fully work, you need clientid and secret. Create your application at: https://github.com/settings/developers
This is pretty basic oauth setup, so it should be clear. Callback URL should be `/auth-github` with your domain in front. If you have any troubles with creating your oauth let us know.

Once you have you auth pair, user those and place in your local environment override in `.env.local` file.
```ini
GITHUB_CLIENT_ID=clientid
GITHUB_CLIENT_SECRET=clientsecret
``` 

## Read more

* [Security - Roles, Policies and access functions](doc/security/index.md)
* [Request Validation](doc/api/validation.md)
* [Project components](doc/project-components.md)
