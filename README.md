# REST API Training

Этот репозиторий предназначен для выполнения тестовых заданий связанных с методологией REST


### Конфигурация

Скопируйте экземпляр конфига из `.env.example` в `.env`

```shell
cp .env.example .env
```

### Зависимости

Установите зависимости с помощью:

```shell
composer install --ignore-platform-reqs
```

или

```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

## Запуск

Для запуска выполните команду:

```shell
./vendor/bin/sail up -d
# или
php artisan serve
```
// Либо подлючение через OpenServer 

Приложение будет доступно по адресу `localhost:$port`, где `$port` - это значение `APP_PORT` из конфигурации.

## База данных

```shell
./vendor/bin/sail artisan migrate --seed
# или
php artisan migrate --seed
```
