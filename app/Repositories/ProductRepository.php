<?php 

namespace App\Repositories;

use App\Entities\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductRepository 
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Product::class)->findAll();
    }

    public function findById(string $id): ?Product
    {
        return $this->entityManager->find(Product::class, $id);
    }

    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function delete(Product $product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    public function findPaginated(int $page = 1, int $limit = 10): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }

}