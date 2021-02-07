<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Model\Classroom\Entity\Classroom;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Model\Classroom\UseCase\Find;
use App\Model\Classroom\UseCase\Create;
use App\Model\Classroom\UseCase\Update;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API",
 *     description="HTTP JSON API",
 * ),
 * @OA\Server(
 *     url="/"
 * ),
 * @Route("/api/classroom")
 **/
class ClassroomController extends AbstractController
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private LoggerInterface $logger;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @OA\Get(
     *     path="/api/classroom",
     *     tags={"Classroom"},
     *     summary="Classroom list",
     *     description="Return list classroom with paginate",
     *     @OA\Parameter(
     *         description="Page",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         description="Per page",
     *         in="query",
     *         name="per_page",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *         description="Find active or not active. Without this parameter find all items",
     *         in="query",
     *         name="is_active",
     *         required=false,
     *         @OA\Schema(type="boolean"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="successful operation",
     *         @OA\JsonContent(
     *            type="object",
     *            @OA\Property(property="items", type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="is_active", type="boolean"),
     *                  @OA\Property(property="created_at", type="string"),
     *            ),
     *            @OA\Property(property="pagination", type="object")
     *         ),
     *     ),
     * )
     * @Route("", name="classroom_index", methods={"GET"})
     * @param Request $request
     * @param Find\Handler $handler
     * @return JsonResponse
     */
    public function index(Request $request, Find\Handler $handler): JsonResponse
    {
        $command = new Find\Command(
            null === $request->query->get('is_active'),
            $request->query->getBoolean('is_active'),
            $request->query->getInt('page', 1),
            $request->query->getInt('per_page', Classroom::DEFAULT_PER_PAGE)
        );

        return $this->json($handler->handle($command));
    }

    /**
     * @OA\Post(
     *     path="/api/classroom",
     *     tags={"Classroom"},
     *     summary="Create Classroom",
     *     description="Create new Classroom and return it",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="isActive", type="boolean"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="successful operation",
     *         @OA\JsonContent(
     *            type="object",
     *            @OA\Property(property="id", type="integer"),
     *            @OA\Property(property="name", type="string"),
     *            @OA\Property(property="is_active", type="boolean"),
     *            @OA\Property(property="created_at", type="string"),
     *         ),
     *     ),
     * )
     * @Route("", name="classroom_create", methods={"POST"})
     * @param Request $request
     * @param Create\Handler $handler
     * @return JsonResponse
     */
    public function create(Request $request, Create\Handler $handler): JsonResponse
    {
        try {
            $content = $request->getContent();
            /** @var Create\Command $command */
            $command = $this->serializer->deserialize($content, Create\Command::class, 'json');

            if (\count($violations = $this->validator->validate($command))) {
                return $this->json($violations, 400);
            }

            $entity = $handler->handle($command);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);

            return $this->json($exception, 500);
        }

        return $this->json($entity);
    }

    /**
     * @OA\Put (
     *     path="/api/classroom/{classroom}",
     *     tags={"Classroom"},
     *     summary="Update Classroom",
     *     description="Update Classroom and return it",
     *     @OA\Parameter(
     *         description="ClassroomId",
     *         in="path",
     *         name="classroom",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="successful operation",
     *         @OA\JsonContent(
     *            type="object",
     *            @OA\Property(property="id", type="integer"),
     *            @OA\Property(property="name", type="string"),
     *            @OA\Property(property="is_active", type="boolean"),
     *            @OA\Property(property="created_at", type="string"),
     *         ),
     *     ),
     * )
     * @Route("/{classroom}", name="classroom_update", methods={"PUT"})
     * @param Classroom $classroom
     * @param Request $request
     * @param Update\Handler $handler
     * @return JsonResponse
     */
    public function update(Classroom $classroom, Request $request, Update\Handler $handler): JsonResponse
    {
        try {
            $content = $request->getContent();
            /** @var Update\Command $command */
            $command = $this->serializer->deserialize($content, Update\Command::class, 'json');

            if (\count($violations = $this->validator->validate($command))) {
                return $this->json($violations, 400);
            }

            $handler->handle($command, $classroom);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);

            return $this->json($exception, 500);
        }

        return $this->json($classroom);
    }

    /**
     * @OA\Get(
     *     path="/api/classroom/{classroom}",
     *     tags={"Classroom"},
     *     summary="Show Classroom by id",
     *     description="Return Classroom",
     *     @OA\Parameter(
     *         description="ClassroomId",
     *         in="path",
     *         name="classroom",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="successful operation",
     *         @OA\JsonContent(
     *            type="object",
     *            @OA\Property(property="id", type="integer"),
     *            @OA\Property(property="name", type="string"),
     *            @OA\Property(property="is_active", type="boolean"),
     *            @OA\Property(property="created_at", type="string"),
     *         ),
     *     ),
     * )
     * @Route("/{classroom}", name="classroom_show", methods={"GET"})
     * @param Classroom $classroom
     * @return JsonResponse
     */
    public function show(Classroom $classroom): JsonResponse
    {
        return $this->json($classroom);
    }

    /**
     * @OA\Patch (
     *     path="/api/classroom/{classroom}",
     *     tags={"Classroom"},
     *     summary="Toggle is_active Classroom by id",
     *     description="Return Classroom",
     *     @OA\Parameter(
     *         description="ClassroomId",
     *         in="path",
     *         name="classroom",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="successful operation",
     *         @OA\JsonContent(
     *            type="object",
     *            @OA\Property(property="id", type="integer"),
     *            @OA\Property(property="name", type="string"),
     *            @OA\Property(property="is_active", type="boolean"),
     *            @OA\Property(property="created_at", type="string"),
     *         ),
     *     ),
     * )
     * @Route("/{classroom}", name="classroom_toggle_active", methods={"PATCH"})
     * @param Classroom $classroom
     * @return JsonResponse
     */
    public function toggle(Classroom $classroom): JsonResponse
    {
        $classroom->toggleIsActive();
        $this->getDoctrine()->getManager()->persist($classroom);
        $this->getDoctrine()->getManager()->flush();

        return $this->json($classroom);
    }

    /**
     * @OA\Delete  (
     *     path="/api/classroom/{classroom}",
     *     tags={"Classroom"},
     *     summary="Delete Classroom",
     *     description="Delete Classroom by id",
     *     @OA\Parameter(
     *         description="ClassroomId",
     *         in="path",
     *         name="classroom",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="successful operation",
     *         @OA\JsonContent(
     *            type="object",
     *            @OA\Property(property="message", type="string"),
     *         ),
     *     ),
     * )
     * @Route("/{classroom}", name="classroom_delete", methods={"DELETE"})
     * @param Classroom $classroom
     * @return JsonResponse
     */
    public function delete(Classroom $classroom): JsonResponse
    {
        $this->getDoctrine()->getManager()->remove($classroom);
        $this->getDoctrine()->getManager()->flush();

        return $this->json(['message' => 'Deleted successfully']);
    }
}
