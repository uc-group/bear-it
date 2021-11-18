<?php

namespace App\Controller\Api\Pages;

use App\Entity\Pages\Book;
use App\Http\Response\SuccessResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/page/{projectId}/book-list', name: 'api_page_book_list', methods: ['GET'])]
class ListBooksController extends AbstractController
{
    public function __invoke(string $projectId)
    {
        $bookRepository = $this->getDoctrine()->getRepository(Book::class);
        $books = $bookRepository->findBy([
            'project' => $projectId
        ]);

        return new SuccessResponse(array_map(function (Book $book) {
            return $book->toArray();
        }, $books));
    }
}
