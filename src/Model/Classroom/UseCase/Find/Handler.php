<?php

declare(strict_types=1);

namespace App\Model\Classroom\UseCase\Find;

use App\Model\Classroom\Repository\ClassroomRepository;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Knp\Component\Pager\PaginatorInterface;

class Handler
{
    private ClassroomRepository $classroomRepository;
    private PaginatorInterface $paginator;

    public function __construct(ClassroomRepository $classroomRepository, PaginatorInterface $paginator)
    {
        $this->classroomRepository = $classroomRepository;
        $this->paginator = $paginator;
    }

    public function handle(Command $command): array
    {
        /** @var SlidingPagination $pagination */
        $pagination = $this->paginator->paginate(
            $this->classroomRepository->findAllPaginate($command->isActive, $command->isAll),
            $command->page,
            $command->perPage
        );

        return [
            'items' => $pagination->getItems(),
            'pagination' => $pagination->getPaginationData(),
        ];
    }
}
