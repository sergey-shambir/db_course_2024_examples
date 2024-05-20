-- Данный скрипт создаёт пользователя MySQL в случае, если вы не используете docker.
-- Для окружения в docker-compose пользователь MySQL будет создан автоматически при запуске контейнеров.

CREATE USER 'wiki-backend-app'@'%' IDENTIFIED BY 'kUUTyU7LssSc';

CREATE DATABASE wiki_backend;
GRANT ALL ON wiki_backend.* TO 'wiki-backend-app'@'%';
