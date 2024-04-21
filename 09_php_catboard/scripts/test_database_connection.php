<?php
declare(strict_types=1);

require_once __DIR__ . '/src/database.php';

$connection = connectDatabase();

$postId = savePostToDatabase($connection, [
    'title' => 'Новый пост',
    'subtitle' => 'Новый подзаголовок',
    'content' => 'Текст-рыба для нового поста',
]);
$post = findPostInDatabase($connection, $postId);
var_dump($post);
