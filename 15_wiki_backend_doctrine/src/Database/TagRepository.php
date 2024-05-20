<?php
declare(strict_types=1);

namespace App\Database;

use App\Model\Domain\Tag;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TagRepository
{
    private EntityManagerInterface $entityManager;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Tag::class);
    }

    /**
     * @param string[] $texts
     * @return Tag[]
     */
    public function findTags(array $texts): array
    {
        if (count($texts) === 0)
        {
            return [];
        }

        $queryBuilder = $this->repository->createQueryBuilder('t');
        $query = $queryBuilder
            ->where($queryBuilder->expr()->in('t.text', ':texts'))
            ->setParameter('texts', $texts, ArrayParameterType::STRING)
            ->getQuery();
        return $query->getResult();
    }

    public function add(Tag $tag): void
    {
        $this->entityManager->persist($tag);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
