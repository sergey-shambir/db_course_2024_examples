# Пример: деревья в базе данных

Пример для курса "Базы данных", сделан на PHP8 и MySQL8, проверен на Linux.

## Материалы

В понимании примера могут помочь следующие материалы:

- [MySQL 8.0 Reference Manual :: 13.2.20 WITH (Common Table Expressions)](https://dev.mysql.com/doc/refman/8.0/en/with.html#common-table-expressions-recursive)
- [Часть 1. Иерархические структуры данных и Doctrine](https://www.opennet.ru/docs/RUS/hierarchical_data/)

## Запуск тестов примера в Linux с помощью Docker

1. Установить docker и docker-compose
2. Запустить контейнеры по инструкции из файла `docs/docker.md`
3. Либо выполнить Bash-скрипт `docker/bin/tree-of-life-db-init`, либо открыть в MySQL Workbench и выполнить последовательно SQL запросы из файлов
    - `data/02_init_schema.sql`
    - `data/03_nested_set_routines.sql`
4. Запустить `vendor/bin/phpunit`

## Запуск тестов примера в Windows или в Linux без Docker

Краткий план действий:

1. Установить MySQL 8 и PHP 8.2
2. Запустить MySQL server
3. Открыть в MySQL Workbench и выполнить последовательно SQL запросы из двух файлов:
    - `data/01_create_database_and_user.sql`
    - `data/02_init_schema.sql`
    - `data/03_nested_set_routines.sql`
4. Запустить `vendor/bin/phpunit`

## Запуск только определённых тестов

```bash
-- Запуск только тестов в классах, содержащих в названии AdjacencyList
vendor/bin/phpunit --filter AdjacencyList

-- Запуск только тестов в классах, содержащих в названии NestedSet
vendor/bin/phpunit --filter NestedSet
```
