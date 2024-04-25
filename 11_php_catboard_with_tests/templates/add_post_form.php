<?php
/**
 * @var string|null $errorMessage
 */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/navigation_bar.css">
    <link rel="stylesheet" href="/css/add_post_form.css">
</head>
<body>
<?php require(__DIR__ . '/navigation_bar.php') ?>
<div class="form-container">
    <form id="cat-form" class="form" action="add_post.php" method="post" enctype="multipart/form-data">
        <div class="form-field form-field-full-width">
            <h2>кошки это хорошо</h2>
        </div>
        <div class="form-field">
            <label for="cat-form-image">Кошачье фото</label>
            <input name="image" type="file" id="cat-form-image" required
                   accept="image/webp,image/jpeg"/>
        </div>
        <div class="form-field">
            <label for="cat-form-description">Описание</label>
            <textarea name="description" id="cat-form-description" required maxlength="300"></textarea>
        </div>
        <div class="form-field">
            <label for="cat-form-author-name">Ваше имя</label>
            <input type="text" name="author_name" id="cat-form-author-name" required maxlength="100"/>
        </div>
        <?php if ($errorMessage): ?>
            <div class="form-field form-field-full-width">
                <p class="form-error"><?= $errorMessage ?></p>
            </div>
        <?php endif; ?>
        <div class="form-field form-field-full-width">
            <button type="submit">Отправить</button>
        </div>
    </form>
</div>
</body>
</html>
