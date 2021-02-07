<?php

declare(strict_types=1);

namespace App\Model\Classroom\UseCase\Find;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    public bool $isAll;
    public bool $isActive;
    public int $page;
    public int $perPage;

    public function __construct(bool $isAll, bool $isActive, int $page, int $perPage)
    {
        $this->isAll = $isAll;
        $this->isActive = $isActive;
        $this->page = $page;
        $this->perPage = $perPage;
    }
}
