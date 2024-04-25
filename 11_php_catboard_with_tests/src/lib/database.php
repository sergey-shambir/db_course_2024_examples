<?php
declare(strict_types=1);

require_once __DIR__ . '/paths.php';

const DATABASE_CONFIG_NAME = 'catboard.db.ini';

/**
 * Создаёт объект класса PDO, представляющий подключение к MySQL.
 */
function connectDatabase(): PDO
{
    $configPath = getConfigPath(DATABASE_CONFIG_NAME);
    if (!file_exists($configPath))
    {
        throw new RuntimeException("Could not find database configuration at '$configPath'");
    }
    $config = parse_ini_file($configPath);
    if (!$config)
    {
        throw new RuntimeException("Failed to parse database configuration from '$configPath'");
    }

    // Проверяем наличие всех ключей конфигурации.
    $expectedKeys = ['dsn', 'user', 'password'];
    $missingKeys = array_diff($expectedKeys, array_keys($config));
    if ($missingKeys)
    {
        throw new RuntimeException('Wrong database configuration: missing options ' . implode(' ', $missingKeys));
    }

    return new PDO($config['dsn'], $config['user'], $config['password']);
}

/**
 * @param PDO $connection
 * @param array{
 *     path:string,
 *     width:int,
 *     height:int,
 *     mime_type:string
 * } $imageData
 * @return int
 */
function saveImageToDatabase(PDO $connection, array $imageData): int
{
    $query = <<<SQL
        INSERT INTO image
          (path, width, height, mime_type)
        VALUES
          (:path, :width, :height, :mime_type)
        SQL;
    $statement = $connection->prepare($query);
    $statement->execute([
        ':path' => $imageData['path'],
        ':width' => $imageData['width'],
        ':height' => $imageData['height'],
        ':mime_type' => $imageData['mime_type']
    ]);

    return (int)$connection->lastInsertId();
}

/**
 * Сохраняет пост в таблицу post, возвращает ID поста.
 *
 * @param array{
 *     image_id:string,
 *     description:string,
 *     author_name:string
 * } $postData
 */
function savePostToDatabase(PDO $connection, array $postData): int
{
    $query = <<<SQL
        INSERT INTO post (image_id, description, author_name)
        VALUES (:image_id, :description, :author_name)
        SQL;

    $statement = $connection->prepare($query);
    $statement->execute([
        ':image_id' => $postData['image_id'],
        ':description' => $postData['description'],
        ':author_name' => $postData['author_name']
    ]);

    return (int)$connection->lastInsertId();
}

/**
 * Извлекает из БД данные поста с указанным ID.
 * Возвращает null, если пост не найден
 *
 * @param PDO $connection
 * @param int $id
 * @return array{
 *     id:int,
 *     description:string,
 *     image_id:string,
 *     author_name:string,
 *     created_at:string,
 * }|null
 */
function findPostInDatabase(PDO $connection, int $id): ?array
{
    $query = <<<SQL
        SELECT
            id,
            image_id,
            description,
            author_name,
            created_at
        FROM post
        WHERE id = $id
        SQL;

    $statement = $connection->query($query);
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

/**
 * Извлекает из БД список недавних постов
 *
 * @param PDO $connection
 * @param int $limit
 * @return array{
 *      id:int,
 *      description:string,
 *      image_id:string,
 *      author_name:string,
 *      created_at:string
 *  }[]
 */
function getRecentPostsFromDatabase(PDO $connection, int $limit): array
{
    // NOTE: запрос сортирует по ID поста, т.к. автоинкрементный ID монотонно возрастает в хронологическом порядке.
    $query = <<<SQL
        SELECT
            id,
            image_id,
            description,
            author_name,
            created_at
        FROM post
        ORDER BY id DESC
        LIMIT $limit
        SQL;

    return $connection->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Извлекает из БД параметры изображения с указанным ID.
 * Возвращает null, если параметры изображения не найдены
 *
 * @param PDO $connection
 * @param int $id
 * @return null|array{
 *     path:string,
 *     width:int,
 *     height:int,
 *     mime_type:string
 * }
 */
function findImageInDatabase(PDO $connection, int $id): ?array
{
    $query = <<<SQL
        SELECT
            id,
            path,
            width,
            height,
            mime_type
        FROM image
        WHERE id = $id
        SQL;

    $statement = $connection->query($query);
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

/**
 * @param PDO $connection
 * @param int[] $ids - список ID изображений
 * @return array<int,array> - словарь, отображающий ID изображения на массив с параметрами изображения
 */
function getImageToIdMapFromDatabase(PDO $connection, array $ids): array
{
    if (!$ids)
    {
        // Обрабатываем пустой список параметров
        return [];
    }

    $placeholders = str_repeat('?, ', count($ids) - 1) . '?';
    $query = <<<SQL
        SELECT
            id,
            path,
            width,
            height,
            mime_type
        FROM image
        WHERE id IN ($placeholders)
        SQL;


    $statement = $connection->prepare($query);
    $statement->execute($ids);
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Из массива создаём словарь, ключом в котором будет ID изображения
    return array_combine(
        array_column($results, 'id'),
        $results
    );
}
