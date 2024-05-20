# Утилита doctrine-migrations

Утилита `doctrine-migrations` библиотеки Doctrine выполняет задачи, связанные с миграциями проекта.

В проекте есть вспомогательный скрипт `bin/doctrine-migrations`, который выполняет предоставленный библиотекой PHP-скрипт `vendor/bin/doctrine-migrations`.

Примеры использования:

```bash
# Создание миграции
bin/doctrine-migrations migrations:generate

# Миграция базы данных до последней версии
bin/doctrine-migrations --no-interaction migrations:migrate --allow-no-migration

# !!! ОПАСНОЕ ДЕЙСТВИЕ !!!
# Откат последней миграции
bin/doctrine-migrations --no-interaction migrations:migrate prev
```
