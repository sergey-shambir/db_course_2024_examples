<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Model\Article;
use PHPUnit\Framework\TestCase;

/**
 * Этот тест несёт мало пользы и добавлен в целях иллюстрации подхода к модульному тестированию.
 */
class ArticleTest extends TestCase
{
    public function testEditArticle(): void
    {
        // Шаг 1. Arrange (подготовка состояния)
        $firstAuthorId = 307;
        $secondAuthorId = 417;
        $article = new Article(
            id: 10,
            version: 1,
            title: '(Черновик) B+ деревья',
            content: <<<TEXT
                B+-деревья — это основа физической структуры реляционных баз данных.
                
                Именно они ответственны за сочетание двух характеристик реляционных СУБД...
                TEXT
            ,
            tags: ['MySQL', 'PostgreSQL'],
            createdAt: new \DateTimeImmutable(),
            createdBy: $firstAuthorId
        );

        // Шаг 2. Act (выполнение действия)
        $content = <<<TEXT
                    B+-деревья — это основа физической структуры реляционных баз данных.
                    
                    Именно они ответственны за сочетание двух характеристик реляционных СУБД:
                    
                    - Высокая скорость работы как для небольших запросов, так и для больших 
                    - Устойчивость данных к перезагрузке при условии сохранности внешнего диска
                    TEXT;
        $article->edit(
            userId: $secondAuthorId,
            title: 'B+ деревья',
            content: $content,
            tags: ['MySQL', 'B+-деревья', 'Индексы'],
        );

        // Шаг 3. Assert (проверка утверждений)
        $this->assertArticle(
            $article,
            title: 'B+ деревья',
            createdBy: $firstAuthorId,
            tags: ['MySQL', 'B+-деревья', 'Индексы'],
            content: $content,
            updatedBy: $secondAuthorId
        );
    }

    /**
     * @param Article $article
     * @param string $title
     * @param int $createdBy
     * @param string[] $tags
     * @param string $content
     * @param int|null $updatedBy
     * @return void
     */
    public function assertArticle(
        Article $article,
        string $title,
        int $createdBy,
        array $tags = [],
        string $content = '',
        int $updatedBy = null): void
    {
        $this->assertEquals($title, $article->getTitle());
        $this->assertEquals($tags, $article->getTags());
        $this->assertEquals($createdBy, $article->getCreatedBy());
        $this->assertEquals($updatedBy, $article->getUpdatedBy());
        $this->assertEquals($content, $article->getContent());
    }
}
