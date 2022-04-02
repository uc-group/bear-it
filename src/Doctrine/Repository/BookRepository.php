<?php

namespace App\Doctrine\Repository;

use App\Doctrine\Traits\ProjectResourceTrait;
use App\Entity\Pages\Book as BookEntity;
use App\Pages\Model\Book\Book;
use App\Pages\Model\Book\BookId;
use Doctrine\ORM\EntityManagerInterface;

class BookRepository
{
    use ProjectResourceTrait;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(Book $book)
    {
        $entity = $this->entityManager->getRepository(BookEntity::class)->find($book->id()->toString());

        if (!$entity) {
            $this->createProjectResource($book->id());
            $entity = new BookEntity($book->id(), $book->name);
        } else {
            $entity->name = $book->name;
        }

        $entity->navigation = $book->navigation()->toArray();
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function load(BookId $bookId): ?Book
    {
        /** @var BookEntity $entity */
        $entity = $this->entityManager->getRepository(BookEntity::class)->find($bookId->toString());
        if (!$entity) {
            return null;
        }

        return Book::fromArray([
            'id' => $entity->getId()->toString(),
            'name' => $entity->name,
            'navigation' => $entity->navigation
        ]);
    }
}
