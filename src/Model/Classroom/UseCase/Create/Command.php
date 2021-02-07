<?php

declare(strict_types=1);

namespace App\Model\Classroom\UseCase\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    public string $name;

    /**
     * @Assert\Type("bool")
     */
    public bool $isActive = true;
}
