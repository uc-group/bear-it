<?php

namespace App\Component;

abstract class AbstractComponent
{
    public function configureProjectMenu(ProjectMenu $projectMenu): void {}
    public function configureProjectTabs(ProjectTabs $projectTabs): void {}
}
