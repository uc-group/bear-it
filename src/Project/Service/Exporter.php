<?php

namespace App\Project\Service;

use App\Entity\Project;
use App\Project\Model\Project\ProjectDescriptor;
use App\Project\Model\Project\ProjectId;
use App\Utils\ComponentExporter\ComponentExporterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Filesystem\Filesystem;

class Exporter
{
    private string $exportDir = '';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ServiceLocator $componentExporters,
        private string $tempProjectExportDir
    ) {}

    public function export(ProjectId $projectId): ?string
    {
        $this->exportDir = sprintf(
            '%s/%s',
            rtrim($this->tempProjectExportDir, '/'),
            $projectId->toString()
        );

        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->exportDir)) {
            $filesystem->mkdir($this->exportDir, 0700);
        }

        $archivePath = sprintf('%s/%s.zip', $this->exportDir, $projectId->toString());
        $descriptorPath = sprintf('%s/project.json', $this->exportDir);

        /** @var Project|null $project */
        $project = $this->entityManager->getRepository(Project::class)->find($projectId->toString());
        if (!$project) {
            return null;
        }

        $projectDescriptor = new ProjectDescriptor($project);
        $this->exportComponents($projectDescriptor);
        $data = $projectDescriptor->toArray();
        file_put_contents($descriptorPath, json_encode($data, JSON_UNESCAPED_UNICODE));

        if ($filesystem->exists($archivePath)) {
            $filesystem->remove($archivePath);
        }

        $archive = new \ZipArchive();
        $archive->open($archivePath, \ZipArchive::CREATE);
        $archive->addFile($descriptorPath, 'project.json');
        foreach ($projectDescriptor->getResources() as $resource) {
            $archive->addFile($resource['file_path'], sprintf('resources/%s', $resource['path']));
        }
        foreach ($projectDescriptor->getFiles() as $file) {
            $archive->addFile($file['file_path'], sprintf('files/%s', $file['path']));
        }

        $archive->close();

        $this->cleanupComponentExports($projectDescriptor);
        $filesystem->remove($descriptorPath);

        return $archivePath;
    }

    private function exportComponents(ProjectDescriptor $projectDescriptor): void
    {
        foreach ($projectDescriptor->getComponents() as $component) {
            if ($this->componentExporters->has($component)) {
                /** @var ComponentExporterInterface $componentExporter */
                $componentExporter = $this->componentExporters->get($component);
                $componentExporter->export($projectDescriptor);
            }
        }
    }

    private function cleanupComponentExports(ProjectDescriptor $projectDescriptor): void
    {
        foreach ($projectDescriptor->getComponents() as $component) {
            if ($this->componentExporters->has($component)) {
                /** @var ComponentExporterInterface $componentExporter */
                $componentExporter = $this->componentExporters->get($component);
                $componentExporter->cleanUp();
            }
        }
    }
}
