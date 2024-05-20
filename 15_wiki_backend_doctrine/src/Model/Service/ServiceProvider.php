<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Common\Doctrine\DoctrineProvider;
use App\Common\Doctrine\Synchronization;
use App\Database\ArticleQueryService;
use App\Database\ArticleRepository;
use App\Database\TagRepository;
use App\Model\Domain\TagDomainService;

final class ServiceProvider
{
    private ?ArticleService $articleService = null;
    private ?ArticleQueryService $articleQueryService = null;
    private ?ArticleRepository $articleRepository = null;
    private ?TagDomainService $tagDomainService = null;
    private ?TagRepository $tagRepository = null;

    public static function getInstance(): self
    {
        static $instance = null;
        if ($instance === null)
        {
            $instance = new self();
        }
        return $instance;
    }

    public function getArticleService(): ArticleService
    {
        if ($this->articleService === null)
        {
            $synchronization = new Synchronization(DoctrineProvider::getConnection());
            $this->articleService = new ArticleService($synchronization, $this->getArticleRepository(), $this->getTagDomainService());
        }
        return $this->articleService;
    }

    public function getArticleQueryService(): ArticleQueryService
    {
        if ($this->articleQueryService === null)
        {
            $this->articleQueryService = new ArticleQueryService(DoctrineProvider::getConnection());
        }
        return $this->articleQueryService;
    }

    private function getArticleRepository(): ArticleRepository
    {
        if ($this->articleRepository === null)
        {
            $this->articleRepository = new ArticleRepository(DoctrineProvider::getEntityManager());
        }
        return $this->articleRepository;
    }

    private function getTagDomainService(): TagDomainService
    {
        if ($this->tagDomainService === null)
        {
            $this->tagDomainService = new TagDomainService($this->getTagRepository());
        }
        return $this->tagDomainService;
    }

    private function getTagRepository(): TagRepository
    {
        if ($this->tagRepository === null)
        {
            $this->tagRepository = new TagRepository(DoctrineProvider::getEntityManager());
        }
        return $this->tagRepository;
    }
}
