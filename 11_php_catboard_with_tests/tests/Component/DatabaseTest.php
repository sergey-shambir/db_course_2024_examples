<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Tests\Common\AbstractDatabaseTestCase;

class DatabaseTest extends AbstractDatabaseTestCase
{
    /**
     * Пример компонентного интеграционного теста
     * - тестирует функции, сохраняющие данные в базу данных
     * - проверяет сохранение и чтение фотографии (таблица image) и поста (таблица post)
     */
    public function testSaveImageAndPostToDatabase(): void
    {
        // Первая часть теста: сохранение изображений (сделано по шаблону Act-Arrange-Assert)

        // Шаг Arrange
        $connection = \connectDatabase();
        $expectedImageData = [
            'path' => 'kitty-smiles.jpeg',
            'width' => 640,
            'height' => 480,
            'mime_type' => 'image/jpeg'
        ];

        // Шаг Act
        $imageId = saveImageToDatabase($connection, $expectedImageData);
        $actualImageData = findImageInDatabase($connection, $imageId);

        // Вторая часть теста: сохранение поста (сделано по шаблону Act-Arrange-Assert)

        // Шаг Assert
        $expectedImageData['id'] = $imageId;
        $this->assertEquals($expectedImageData, $actualImageData);

        // Шаг Arrange
        $expectedPostData = [
            'image_id' => $imageId,
            'description' => 'Котёнок улыбается',
            'author_name' => 'cat.master'
        ];

        // Шаг Act
        $postId = savePostToDatabase($connection, $expectedPostData);
        $actualPostData = findPostInDatabase($connection, $postId);

        // Шаг Assert
        $expectedPostData['id'] = $postId;
        unset($actualPostData['created_at']);
        $this->assertEquals($expectedPostData, $actualPostData);
    }

}
