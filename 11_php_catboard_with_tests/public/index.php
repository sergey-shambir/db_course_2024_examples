<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/lib/request.php';
require_once __DIR__ . '/../src/lib/response.php';
require_once __DIR__ . '/../src/lib/database.php';
require_once __DIR__ . '/../src/lib/views.php';

const PAGE_SIZE = 5;

function handleShowPost(): void
{
    $connection = connectDatabase();

    // Получаем список постов
    $posts = getRecentPostsFromDatabase($connection, PAGE_SIZE);

    // Получаем словарь, отображащий ID изображения на его параметры
    $imageIds = array_column($posts, 'image_id');
    $imageByIdMap = getImageToIdMapFromDatabase($connection, $imageIds);

    $postViews = [];
    foreach ($posts as $postData)
    {
        $postedAt = new DateTimeImmutable($postData['created_at']);

        $imageData = $imageByIdMap[$postData['image_id']] ?? null;
        $imageUrlPath = $imageData ? getUploadUrlPath($imageData['path']) : '';

        $postViews[] = [
            'id' => $postData['id'],
            'image_url' => $imageUrlPath,
            'description' => $postData['description'],
            'author_name' => $postData['author_name'],
            'created_at' => $postData['created_at'],
            'posted_at' => $postedAt,
        ];
    }

    echo renderView('posts_feed_page.php', [
        'posts' => $postViews
    ]);
}

try
{
    if (isRequestHttpMethod(HTTP_METHOD_GET))
    {
        handleShowPost();
    }
    else
    {
        writeRedirectSeeOther($_SERVER['REQUEST_URI']);
    }
}
catch (Throwable $ex)
{
    error_log((string)$ex);
    writeInternalError();
}
