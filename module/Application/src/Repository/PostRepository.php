<?php

namespace Application\Repository;

use Application\Entity\Post;
use Doctrine\ORM\EntityRepository;

/**
 * Class PostRepository
 * @package Application\Repository\
 */
class PostRepository extends EntityRepository
{
    /**
     * @return array|null
     */
    public function findAllHavingAnyTag(): ?array
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('p')
            ->from(Post::class, 'p')
            ->join('p.tags', 't')
            ->where('p.status = ?1')
            ->orderBy('p.dateCreated', 'DESC')
            ->setParameter('1', Post::STATUS_PUBLISHED)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $tagName
     * @return array|null
     */
    public function findAllByTagName(string $tagName): ?array
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('p')
            ->from(Post::class, 'p')
            ->join('p.tags', 't')
            ->where('t.name = ?1')
            ->andWhere('p.status = ?2')
            ->orderBy('p.dateCreated', 'DESC'   )
            ->setParameter('1', $tagName)
            ->setParameter('2', Post::STATUS_PUBLISHED)
            ->getQuery()
            ->getResult();
    }
}
