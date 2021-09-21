<?php

namespace App\Controller\Api\Pages;

use App\Doctrine\Repository\PageRepository;
use App\Http\Response\SuccessResponse;
use App\Pages\Command\PageEdit;
use App\RequestValidator\Pages\Edit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Yc\RequestValidationBundle\Attributes\RequestValidator;

#[Route('/api/page/edit', name: 'api_page_edit')]
#[RequestValidator(Edit::class)]
class EditPageController extends AbstractController
{
    public function __construct(
        private PageRepository $pageRepository
    ) {}

    public function __invoke(PageEdit $pageEdit)
    {
        $page = $this->pageRepository->findPage($pageEdit->getId());
        $page->update($pageEdit);

        $this->pageRepository->updatePage($page);

        return new SuccessResponse($page->getId());
    }
}
