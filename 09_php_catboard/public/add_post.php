<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/lib/request.php';
require_once __DIR__ . '/../src/lib/response.php';
require_once __DIR__ . '/../src/lib/database.php';
require_once __DIR__ . '/../src/lib/uploads.php';
require_once __DIR__ . '/../src/lib/views.php';

const SHOW_POST_URL = '/show_post.php';
const POST_ID_PARAM = 'post_id';

function showAddPostForm(?string $errorMessage = null): void
{
    echo renderView('add_post_form.php', [
        'errorMessage' => $errorMessage
    ]);
}

function handleAddPostForm(): void
{
    // Разбор параметров формы
    $fileInfo = $_FILES['image'] ?? null;
    $description = $_POST['description'] ?? null;
    $authorName = $_POST['author_name'] ?? null;
    if (!$fileInfo || !$description || !$authorName)
    {
        showAddPostForm(errorMessage: 'Все поля обязательны для заполнения');
        http_response_code(HTTP_STATUS_400_BAD_REQUEST);
        return;
    }

    // Загрузка изображения в каталог uploads/
    try
    {
        $imageInfo = uploadImageFile($fileInfo);
    }
    catch (InvalidArgumentException $exception)
    {
        showAddPostForm(errorMessage: $exception->getMessage());
        http_response_code(HTTP_STATUS_400_BAD_REQUEST);
        return;
    }

    // Сохранение параметров изображения в базу данных
    $connection = connectDatabase();
    $imageId = saveImageToDatabase($connection, $imageInfo);

    $postId = savePostToDatabase($connection, [
        'image_id' => $imageId,
        'description' => $description,
        'author_name' => $authorName
    ]);

    $postUrl = SHOW_POST_URL . '?' . http_build_query([POST_ID_PARAM => $postId]);
    writeRedirectSeeOther($postUrl);
}

try
{
    if (isRequestHttpMethod(HTTP_METHOD_GET))
    {
        showAddPostForm();
    }
    elseif (isRequestHttpMethod(HTTP_METHOD_POST))
    {
        handleAddPostForm();
    }
    else
    {
        writeRedirectSeeOther('/');
    }
}
catch (Throwable $ex)
{
    error_log((string)$ex);
    writeInternalError();
}
