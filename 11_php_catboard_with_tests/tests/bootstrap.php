<?php
declare(strict_types=1);

// Устанавливаем переменную окружения, сигнализирующую приложению, что оно
//  запускается в режиме тестирования.
putenv('APP_ENV=test');

require_once __DIR__ . '/../vendor/autoload.php';

// Подключаем файл с функциями для работы с базой данных (это нужно для получения соединения)
require_once __DIR__ . '/../src/lib/database.php';
