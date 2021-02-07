# Symfony sample crud api

### Configure
`cp -rp .env.example .env` configure db connection

`composer install`

`php bin/console doctrine:database:create`

`php bin/console doctrine:migrations:migrate`

### Run
`php -S 127.0.0.1:8000 -t public/`

#### Swagger
`http://127.0.0.1:8000/docs/index.html`
