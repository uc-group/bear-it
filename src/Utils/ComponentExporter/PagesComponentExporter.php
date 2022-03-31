<?php

namespace App\Utils\ComponentExporter;

use App\Entity\Pages\Book;
use App\Entity\Pages\Page;
use App\Project\Model\Project\ProjectDescriptor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class PagesComponentExporter implements ComponentExporterInterface
{
    private string $exportDir = '';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private string $tempProjectExportDir
    ) {}

    public static function getComponentName(): string
    {
        return 'pages';
    }

    public function export(ProjectDescriptor $descriptor): void
    {
        $this->exportDir = sprintf(
            '%s/%s/pages',
            rtrim($this->tempProjectExportDir, '/'),
            $descriptor->getProjectId()->toString()
        );

        $folders = [
            sprintf('%s/pages', $this->exportDir),
            sprintf('%s/books', $this->exportDir),
        ];
        $filesystem = new Filesystem();
        foreach ($folders as $folder) {
            if (!$filesystem->exists($folder)) {
                $filesystem->mkdir($folder, 0700);
            }
        }

        $this->exportPages($descriptor);
        $this->exportBooks($descriptor);
    }

    public function cleanUp(): void
    {
        $filesystem = new Filesystem();
        if (!empty($this->exportDir)) {
            $filesystem->remove($this->exportDir);
            $this->exportDir = '';
        }
    }

    private function exportPages(ProjectDescriptor $descriptor)
    {
        /** @var Page[] $pages */
        $pages = $this->entityManager->getRepository(Page::class)->findBy([
            'project' => $descriptor->getProjectId()->toString()
        ]);

        foreach ($pages as $page) {
            $pageData = $page->toArray();
            $path = sprintf('pages/%s.md', $page->getId());
            $filePath = $this->exportDir . sprintf('/%s', $path);
            file_put_contents($filePath, $pageData['content']);
            $descriptor->addFile($filePath, $path,'pages/page', $page->getId(), [
                'name' => $pageData['name']
            ]);
        }
    }

    private function exportBooks(ProjectDescriptor $descriptor)
    {
        /** @var Book[] $books */
        $books = $this->entityManager->getRepository(Book::class)->findBy([
            'project' => $descriptor->getProjectId()->toString()
        ]);

        foreach ($books as $book) {
            $path = sprintf('books/book_%05d.json', $book->getId()->number());
            $filePath = $this->exportDir . sprintf('/%s', $path);
            $bookData = $book->toArray();
            $bookData['id'] = $book->getId()->number();
            file_put_contents($filePath, json_encode($bookData, JSON_UNESCAPED_UNICODE));
            $descriptor->addResource($book->getId(), $filePath, $path);
        }
    }
}
