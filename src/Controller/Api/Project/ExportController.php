<?php

namespace App\Controller\Api\Project;

use App\Http\Response\SuccessResponse;
use App\Project\Model\Project\ProjectId;
use App\Project\Service\Exporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project-export/{projectId}.zip')]
class ExportController extends AbstractController
{
    public function __construct(
        private Exporter $projectExporter
    ) {}

    public function __invoke(string $projectId)
    {
        $archivePath = $this->projectExporter->export(ProjectId::fromString($projectId));
        if (!$archivePath) {
            throw $this->createNotFoundException();
        }

        return new BinaryFileResponse($archivePath);
    }
}
