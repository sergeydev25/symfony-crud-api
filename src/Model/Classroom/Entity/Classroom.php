<?php

declare(strict_types=1);

namespace App\Model\Classroom\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Classroom
{
    const DEFAULT_PER_PAGE = 10;
    const DEFAULT_DATETIME_FORMAT = "Y-m-d H:i:s";

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private bool $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @param string $name
     * @param bool $isActive
     * @param \DateTimeImmutable $createdAt
     */
    private function __construct(string $name, bool $isActive, \DateTimeImmutable $createdAt)
    {
        $this->name = $name;
        $this->isActive = $isActive;
        $this->createdAt = $createdAt;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @param bool $isActive
     * @param \DateTimeImmutable $createdAt
     * @return Classroom
     */
    public function create(string $name, bool $isActive, \DateTimeImmutable $createdAt): Classroom
    {
        return new self($name, $isActive, $createdAt);
    }

    /**
     * @param string $name
     */
    public function update(string $name): void
    {
        $this->name = $name;
    }

    public function toggleIsActive(): void
    {
        $this->isActive = !$this->isActive;
    }
}
