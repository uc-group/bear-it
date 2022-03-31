<?php

namespace App\Controller\Api\Pages;

use App\Doctrine\Repository\BookRepository;
use App\Http\Response\SuccessResponse;
use App\Pages\Model\Book\BookId;
use App\Pages\Model\Book\NavigationElement;
use App\RequestValidator\Pages\UpdateNavigation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/page/book/{bookId}/navigation', name: 'api_pages_book_update_navigation', methods: ['PUT'])]
#[RequestValidator(UpdateNavigation::class)]
class UpdateBookNavigationController extends AbstractController
{
    public function __construct(
        private BookRepository $bookRepository
    ) {}

    public function __invoke(string $bookId, array $navigation)
    {
        $navigation = NavigationElement::fromArray($navigation);
        $book = $this->bookRepository->load(BookId::fromString($bookId));
        if (!$book) {
            throw $this->createNotFoundException();
        }

        $book->updateNavigation($navigation);
        $this->bookRepository->save($book);

        return new SuccessResponse();
    }
}