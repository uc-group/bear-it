<?php

namespace App\Controller\Api\Pages;

use App\Entity\Pages\Book;
use App\Http\Response\SuccessResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/page/book/{id}', name: 'api_page_get_book', methods: ['GET'])]
class GetBookController extends AbstractController
{
    public function __invoke(string $id)
    {
        $book = $this->getDoctrine()->getRepository(Book::class)->find($id);

        return new SuccessResponse($book->toArray());
    }
}
