<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Common\Doctrine\Synchronization;
use App\Database\ArticleRepository;
use App\Model\Data\CreateArticleParams;
use App\Model\Data\EditArticleParams;
use App\Model\Domain\Article;
use App\Model\Domain\TagDomainService;
use App\Model\Exception\ArticleNotFoundException;

class ArticleService
{
    private Synchronization $synchronization;
    private ArticleRepository $articleRepository;
    private TagDomainService $tagDomainService;

    public function __construct(Synchronization $synchronization, ArticleRepository $articleRepository, TagDomainService $tagDomainService)
    {
        $this->synchronization = $synchronization;
        $this->articleRepository = $articleRepository;
        $this->tagDomainService = $tagDomainService;
    }

    /**
     * @param int $id
     * @return Article
     * @throws ArticleNotFoundException
     */
    public function getArticle(int $id): Article
    {
        $article = $this->articleRepository->findOne($id);
        if (!$article)
        {
            throw new ArticleNotFoundException("Cannot find article with id $id");
        }
        return $article;
    }

    public function createArticle(CreateArticleParams $params): int
    {
        return $this->synchronization->doWithTransaction(function () use ($params) {
            $tags = $this->tagDomainService->findOrCreateTags($params->getTags());

            $article = new Article(
                $params->getTitle(),
                '',
                $tags,
                $params->getUserId()
            );
            $this->articleRepository->add($article);
            $this->articleRepository->flush();

            return $article->getId();
        });
    }

    /**
     * @param EditArticleParams $params
     * @return void
     * @throws ArticleNotFoundException
     */
    public function editArticle(EditArticleParams $params): void
    {
        $this->synchronization->doWithTransaction(function () use ($params) {
            $tags = $this->tagDomainService->findOrCreateTags($params->getTags());

            $article = $this->getArticle($params->getId());
            $article->edit($params->getUserId(), $params->getTitle(), $params->getContent(), $tags);
            $this->articleRepository->flush();
        });
    }

    public function deleteArticle(int $id): void
    {
        $this->articleRepository->delete([$id]);
    }

    /**
     * @param int[] $ids
     * @return void
     */
    public function batchDeleteArticles(array $ids): void
    {
        $this->articleRepository->delete($ids);
    }
}
