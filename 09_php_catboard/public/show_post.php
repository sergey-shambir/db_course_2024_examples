<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/lib/request.php';
require_once __DIR__ . '/../src/lib/response.php';
require_once __DIR__ . '/../src/lib/database.php';
require_once __DIR__ . '/../src/lib/views.php';

function handleShowPost(): void
{
    $postId = $_GET['post_id'];
    if (!is_numeric($postId))
    {
        writeErrorNotFound();
        exit();
    }

    $connection = connectDatabase();
    $postData = findPostInDatabase($connection, (int)$postId);
    if (!$postData)
    {
        writeErrorNotFound();
        exit();
    }

    $imageData = findImageInDatabase($connection, (int)$postData['image_id']);
    if (!$imageData)
    {
        writeErrorNotFound();
        exit();
    }

    $imageUrlPath = getUploadUrlPath($imageData['path']);
    $postedAt = new DateTimeImmutable($postData['created_at']);

    echo renderView('post_page.php', [
        'post' => [
            'image_url' => $imageUrlPath,
            'description' => $postData['description'],
            'author_name' => $postData['author_name'],
            'created_at' => $postData['created_at'],
            'posted_at' => $postedAt,
        ],
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
