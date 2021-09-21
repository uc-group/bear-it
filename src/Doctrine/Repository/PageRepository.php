<?php

namespace App\Doctrine\Repository;

use App\Entity\Pages\Page;
use App\Entity\Project;
use App\Pages\Model\Page as PageModel;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class PageRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function findPage(string $pageId): Page
    {
        return $this->entityManager->getRepository(Page::class)->find($pageId);
    }

    public function createPage(PageModel $pageModel): Page
    {
        $project = $this->entityManager->getRepository(Project::class)->find($pageModel->getProjectId()->toString());

        $page = new Page(Uuid::uuid4(), $pageModel->getName(), $pageModel->getContent(), $project);
        $this->entityManager->persist($page);
        $this->entityManager->flush();

        return $page;
    }

    public function updatePage(Page $page)
    {
        $this->entityManager->persist($page);
        $this->entityManager->flush();
    }
}
