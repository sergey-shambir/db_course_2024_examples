<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Common\Database\TransactionalExecutor;
use App\Database\ArticleRepository;
use App\Database\TagRepository;
use App\Model\Article;
use App\Model\Data\CreateArticleParams;
use App\Model\Data\EditArticleParams;
use App\Model\Exception\ArticleNotFoundException;
use App\Model\Service\ArticleService;
use App\Tests\Common\AbstractDatabaseTestCase;

class ArticleServiceTest extends AbstractDatabaseTestCase
{
    /**
     * Это ПЛОХОЙ пример теста: он проверяет всего один метод, а лучше проверять целый бизнес-сценарий.
     * Пример оставлен для иллюстрации.
     */
    public function testCreateArticle(): void
    {
        // Шаг 1. Arrange
        // В данном случае мы только создаём сервис
        $service = $this->createArticleService();
        $firstAuthorId = 10;

        // Шаг 2. Act
        $articleId = $service->createArticle(new CreateArticleParams(
            userId: $firstAuthorId,
            title: '(Черновик) B+ деревья',
            tags: ['MySQL', 'PostgreSQL'],
        ));

        // Шаг 3. Assert
        $article = $service->getArticle($articleId);
        $this->assertArticle($article, title: '(Черновик) B+ деревья', createdBy: $firstAuthorId, tags: ['MySQL', 'PostgreSQL']);
    }

    public function testCreateEditAndDeleteArticle(): void
    {
        // Шаг 1. Arrange
        // В данном случае мы только создаём сервис
        $service = $this->createArticleService();
        $firstAuthorId = 10;

        // Шаг 2. Act
        $articleId = $service->createArticle(new CreateArticleParams(
            userId: $firstAuthorId,
            title: '(Черновик) B+ деревья',
            tags: ['MySQL', 'PostgreSQL'],
        ));

        // Шаг 3. Assert
        $article = $service->getArticle($articleId);
        $this->assertArticle($article,
            title: '(Черновик) B+ деревья',
            createdBy: $firstAuthorId,
            tags: ['MySQL', 'PostgreSQL']
        );

        // Шаг 1. Arrange
        $secondAuthorId = 17;

        // Шаг 2. Act
        $content = <<<TEXT
                    B+-деревья — это основа физической структуры реляционных баз данных.
                    
                    Именно они ответственны за сочетание двух характеристик реляционных СУБД:
                    
                    - Высокая скорость работы как для небольших запросов, так и для больших 
                    - Устойчивость данных к перезагрузке при условии сохранности внешнего диска
                    TEXT;
        $service->editArticle(new EditArticleParams(
            id: $articleId,
            userId: $secondAuthorId,
            title: 'B+ деревья',
            content: $content,
            tags: ['MySQL', 'B+-деревья', 'Индексы'],
        ));

        // Шаг 3. Assert
        $article = $service->getArticle($articleId);
        $this->assertArticle(
            $article,
            title: 'B+ деревья',
            createdBy: $firstAuthorId,
            content: $content,
            tags: ['MySQL', 'B+-деревья', 'Индексы'],
            updatedBy: $secondAuthorId
        );

        // Шаг 2. Act
        $service->deleteArticle($articleId);

        // Шаг 3. Assert
        $this->expectException(ArticleNotFoundException::class);
        $service->getArticle($articleId);
    }

    public function testBatchDeleteArticles(): void
    {
        // Шаг 1. Arrange
        // В данном случае мы только создаём сервис
        $service = $this->createArticleService();
        $authorId = 10;

        // Шаг 2. Act
        $firstArticleId = $service->createArticle(new CreateArticleParams(
            userId: $authorId,
            title: 'B+ деревья',
            tags: ['MySQL', 'PostgreSQL'],
        ));
        $secondArticleId = $service->createArticle(new CreateArticleParams(
            userId: $authorId,
            title: 'Индексы',
            tags: ['MySQL', 'PostgreSQL', 'SQL'],
        ));
        $thirdArticleId = $service->createArticle(new CreateArticleParams(
            userId: $authorId,
            title: 'План выполнения запроса',
            tags: ['MySQL', 'EXPLAIN', 'SQL'],
        ));
        $service->batchDeleteArticles([$firstArticleId, $secondArticleId]);

        // Шаг 3. Assert
        $article = $service->getArticle($thirdArticleId);
        $this->assertArticle($article,
            title: 'План выполнения запроса',
            createdBy: $authorId,
            tags: ['MySQL', 'EXPLAIN', 'SQL']
        );

        $this->assertThrows(
            static fn() => $service->getArticle($firstArticleId),
            ArticleNotFoundException::class
        );
        $this->assertThrows(
            static fn() => $service->getArticle($secondArticleId),
            ArticleNotFoundException::class
        );
    }

    private function assertThrows(\Closure $closure, string $exceptionClass): void
    {
        $actualExceptionClass = null;
        try
        {
            $closure();
        }
        catch (\Throwable $e)
        {
            $actualExceptionClass = $e::class;
        }
        $this->assertEquals($exceptionClass, $actualExceptionClass, "$exceptionClass exception should be thrown");
    }

    private function assertArticle(
        Article $article,
        string $title,
        int $createdBy,
        string $content = '',
        array $tags = [],
        ?int $updatedBy = null,
    ): void
    {
        $this->assertArticleTags($tags, $article);
        $this->assertEquals($createdBy, $article->getCreatedBy(), 'article created by');
        $this->assertEquals($title, $article->getTitle(), 'article title');
        $this->assertEquals($content, $article->getContent(), 'article content');
        $this->assertEquals($updatedBy, $article->getUpdatedBy(), 'article updated by');
    }

    private function assertArticleTags(array $expected, Article $article): void
    {
        $actual = $article->getTags();
        sort($expected);
        sort($actual);
        $this->assertEquals($expected, $actual, 'article tags');
    }

    private function createArticleService(): ArticleService
    {
        $connection = $this->getConnection();
        return new ArticleService(
            new TransactionalExecutor($connection),
            new ArticleRepository($connection),
            new TagRepository($connection)
        );
    }
}
