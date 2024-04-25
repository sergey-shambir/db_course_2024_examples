<?php
/**
 * @var array{
 *   id: int,
 *   image_url: string,
 *   description: string,
 *   author_name: string,
 *   created_at: string,
 *   posted_at: DateTimeImmutable
 * }[] $posts
 */

/**
 * @param int $postId
 * @return string
 */
function getPostPageUrl(int $postId): string
{
    return "/show_post.php?post_id=$postId";
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/navigation_bar.css">
    <link rel="stylesheet" href="/css/posts.css">
</head>
<body>
<?php require(__DIR__ . '/navigation_bar.php') ?>
<?php foreach ($posts as $post): ?>
    <div class="cat-post-container">
        <a href="<?= getPostPageUrl($post['id']) ?>">
            <img src="<?= $post['image_url'] ?>"
                 alt="Кошачье фото"
                 class="cat-post-image"
            />
        </a>
        <p class="cat-post-description">
            <?= htmlentities($post['description']) ?>
        </p>
        <p class="cat-post-metadata">
            Автор: <?= htmlentities($post['author_name']) ?>,
            опубликовано <?= $post['posted_at']->format('Y-m-d в H:i') ?>
        </p>
    </div>
<?php endforeach; ?>
</body>
</html>
