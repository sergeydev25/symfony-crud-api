<?php

declare(strict_types=1);

namespace App\Model\Classroom\UseCase\Create;

use App\Model\Classroom\Entity\Classroom;
use App\Model\Classroom\Repository\ClassroomRepository;
use App\Model\Flusher;

class Handler
{
    private ClassroomRepository $classroomRepository;
    private Flusher $flusher;

    public function __construct(ClassroomRepository $classroomRepository, Flusher $flusher)
    {
        $this->classroomRepository = $classroomRepository;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): Classroom
    {
        $classroom = Classroom::create($command->name, $command->isActive, new \DateTimeImmutable());
        $this->classroomRepository->add($classroom);
        $this->flusher->flush();

        return $classroom;
    }
}
