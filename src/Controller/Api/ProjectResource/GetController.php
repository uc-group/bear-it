<?php

namespace App\Controller\Api\ProjectResource;

use App\Entity\Project;
use App\Entity\ProjectResource;
use App\Http\Response\SuccessResponse;
use App\Project\Model\Project\ResourceId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\UnicodeString;

#[Route('/api/project-resource/{id}', requirements: ['id' => '^[A-Z]+-\d+$'])]
class GetController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke(string $id)
    {
        $id = ResourceId::fromString($id);
        /** @var ProjectResource $resource */
        $resource = $this->entityManager->getRepository(ProjectResource::class)->findOneBy([
            'project' => $this->entityManager->getReference(Project::class, $id->getProjectId()->toString()),
            'resourceNumber' => $id->number()
        ]);

        if (!$resource) {
            throw $this->createNotFoundException();
        }

        return new SuccessResponse([
            'type' => $this->getResourceType($resource),
            'project' => $resource->getProject()->id(),
            'number' => $resource->getResourceNumber()
        ]);
    }

    private function getResourceType(ProjectResource $resource)
    {
        $reflection = new \ReflectionClass($resource->getResourceType());
        $string = new UnicodeString($reflection->getShortName());

        return preg_replace('/_id$/', '', $string->snake());
    }
}