# CatBoard — простой пример взаимодействия PHP и MySQL

Пример написан в процедурном стиле и использует PHP-PDO для взаимодействия с MySQL.

# Способы запуска

## Запуск на Windows и Linux

Для запуска требуется:

- PHP версии 8.2 или выше
- MySQL версии 8.0 или выше

Сначала настройте файл конфигурации:

1. В каталоге config/ создайте файл `catboard.db.ini` из примера `example.catboard.db.ini`
    - в терминале: `cp config/example.catboard.db.ini config/catboard.db.ini`
2. Откройте файл `catboard.db.ini` и проверьте данные для подключения к базе данных
3. Инициализируйте схему базы данных
    - выполните SQL-скрипт `data/00-create-database.sql`
    - выполните SQL-скрипт `data/10-init-schema.sql`

Запустите [встроенный веб-сервер PHP](https://www.php.net/manual/ru/features.commandline.webserver.php) в терминале:

```bash
php -S localhost:8080 -t public

```

После запуска откройте в браузере: http://localhost:8080

## Запуск на Linux с помощью docker-compose

Для запуска требуется:

- PHP версии 8.2 или выше
- Docker и [docker-compose](https://docs.docker.com/compose/install/linux/)

Сначала настройте файл конфигурации:

- скопируйте его из примера в терминале: `cp config/example.catboard.db.ini config/catboard.db.ini`

Затем запустите MySQL:

```bash
docker-compose up

```

Выполните скрипт инициализации схемы:

```bash
docker exec -i catboard-db mysql -ucatboard -pOok4au5a catboard < data/10-init-schema.sql
```

Запустите [встроенный веб-сервер PHP](https://www.php.net/manual/ru/features.commandline.webserver.php) в терминале:

```bash
php -S localhost:8080 -t public

```

После запуска откройте в браузере: http://localhost:8000

В файле `docker-compose.yml` указана привязка порта 3306, при этом разрешены только запросы по адресу `127.0.0.1`

Подключиться к БД можно так:
- через docker контейнер: `docker exec -it catboard-db mysql -ucatboard -pOok4au5a catboard`
- через клиент, установленный локально: `mysql -h127.0.0.1 -ucatboard -pOok4au5a catboard`

# Материалы

См. [Полезные материалы](docs/see-also.md) в каталоге docs.
