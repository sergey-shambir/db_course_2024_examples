<?php
declare(strict_types=1);

namespace App\Database;

use App\Model\Domain\Article;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ArticleRepository
{
    private EntityManagerInterface $entityManager;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Article::class);
    }

    public function findOne(int $id): ?Article
    {
        return $this->repository->find($id);
    }

    public function add(Article $article): void
    {
        $this->entityManager->persist($article);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }

    /**
     * @param int[] $ids
     * @return void
     */
    public function delete(array $ids): void
    {
        if (count($ids) === 0)
        {
            return;
        }

        $queryBuilder = $this->repository->createQueryBuilder('a');
        $query = $queryBuilder->delete(Article::class, 'a')
            ->where($queryBuilder->expr()->in('a.id', ':ids'))
            ->setParameter('ids', $ids, ArrayParameterType::INTEGER)
            ->getQuery();

        $query->execute();
    }
}
