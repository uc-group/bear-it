<?php

namespace App\Utils\ComponentExporter;

use App\Project\Model\Project\ProjectDescriptor;
use App\Project\Model\Project\ProjectId;

interface ComponentExporterInterface
{
    public static function getComponentName():string;
    public function export(ProjectDescriptor $descriptor): void;
    public function cleanUp(): void;
}
