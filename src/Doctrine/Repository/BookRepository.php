<?php

namespace App\Doctrine\Repository;

use App\Entity\Pages\Book as BookEntity;
use App\Entity\Project as ProjectEntity;
use App\Entity\ProjectResource;
use App\Pages\Model\Book\Book;
use App\Pages\Model\Book\BookId;
use Doctrine\ORM\EntityManagerInterface;

class BookRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(Book $book)
    {
        $entity = $this->entityManager->getRepository(BookEntity::class)->find($book->id()->toString());

        if (!$entity) {
            $project = $this->entityManager->getReference(
                ProjectEntity::class,
                $book->id()->getProjectId()->toString()
            );

            $entity = new BookEntity($project, $book->id(), $book->name);

            $projectResource = new ProjectResource(
                $project,
                $book->id()->number(),
                BookId::class
            );
            $this->entityManager->persist($projectResource);
        } else {
            $entity->name = $book->name;
        }

        $entity->navigation = $book->navigation();
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
