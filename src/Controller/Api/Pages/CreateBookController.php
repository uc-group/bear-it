<?php

namespace App\Controller\Api\Pages;

use App\Doctrine\Repository\BookRepository;
use App\Http\Response\SuccessResponse;
use App\Pages\Model\Book\Book;
use App\Pages\Model\Book\BookId;
use App\Project\Model\Project\ProjectId;
use App\Project\Service\ReferenceGeneratorFactory;
use App\RequestValidator\Pages\CreateBook;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/page/book', name: 'api_page_book_create', methods: ['POST'])]
#[RequestValidator(CreateBook::class)]
class CreateBookController
{
    public function __construct(
        private ReferenceGeneratorFactory $referenceGeneratorFactory,
        private BookRepository $repository
    ) {}

    public function __invoke(string $name, ProjectId $projectId)
    {
        $referenceGenerator = $this->referenceGeneratorFactory->get($projectId);
        $id = $referenceGenerator->generate()[0];
        $bookId = BookId::fromString($id->toString());

        $this->repository->save(Book::create($bookId, $name));

        return new SuccessResponse([
            'id' => $bookId->toString()
        ]);
    }
}
