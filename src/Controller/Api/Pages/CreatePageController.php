<?php

namespace App\Controller\Api\Pages;

use App\Doctrine\Repository\PageRepository;
use App\Http\Response\SuccessResponse;
use App\Pages\Model\Page;
use App\RequestValidator\Pages\Create;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/page/create', name: 'api_page_create')]
#[RequestValidator(Create::class)]
class CreatePageController extends AbstractController
{
    public function __construct(
        private PageRepository $pageRepository
    ) {}

    public function __invoke(Page $page)
    {
        $pageEntity = $this->pageRepository->createPage($page);

        return new SuccessResponse($pageEntity->getId());
    }
}
