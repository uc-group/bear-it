<?php

namespace App\Core\Repository;

class ListParams
{
    const LIMIT_DEFAULT = 20;

    public int $limit = self::LIMIT_DEFAULT;
    public int $offset = 0;
    public array $filters = [];
    public array $orderBy = [];
}