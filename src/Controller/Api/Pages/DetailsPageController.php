<?php

namespace App\Controller\Api\Pages;

use App\Entity\Pages\Page;
use App\Http\Response\SuccessResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/page/details/{id}', name: 'api_page_details')]
class DetailsPageController extends AbstractController
{
    public function __invoke(string $id)
    {
        $page = $this->getDoctrine()->getRepository(Page::class)->find($id);

        return new SuccessResponse($page->toArray());
    }
}
