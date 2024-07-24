# REST API Training

Этот репозиторий предназначен для выполнения домашних работ в рамках курса по разработке REST API на Laravel. 

## Установка

Текущее приложение готово для запуска и работы с ним через [Laravel Sail](https://laravel.com/docs/10.x/sail) с использованием [Docker](https://www.docker.com/) и [WSL](https://learn.microsoft.com/ru-ru/windows/wsl/install). 
Однако, это не означает, что вы не сможете воспользоваться своими привычными средствами для разворачивания проекта.

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

Приложение будет доступно по адресу `localhost:$port`, где `$port` - это значение `APP_PORT` из конфигурации.

## База данных

```shell
./vendor/bin/sail artisan migrate --seed
# или
php artisan migrate --seed
```