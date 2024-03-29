<?php

namespace App\Controller\Api\Pages;

use App\Entity\Pages\Page;
use App\Http\Response\SuccessResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/page/{projectId}/list', name: 'api_page_list')]
class ListPagesController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke(string $projectId)
    {
        $pageRepository = $this->entityManager->getRepository(Page::class);
        $pages = $pageRepository->findBy([
            'project' => $projectId
        ]);

        return new SuccessResponse(array_map(function (Page $page) {
            return $page->toArray();
        }, $pages));
    }
}
