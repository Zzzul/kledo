<?php

namespace App\Http\Controllers;

use App\Http\Requests\Statuses\StoreStatusRequest;
use App\Http\Requests\Statuses\UpdateStatusRequest;
use App\Http\Resources\Statuses\StatusCollection;
use App\Http\Resources\Statuses\StatusResource;
use App\Interfaces\StatusServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Status",
 *     description="API Endpoints for managing status"
 * )
 */
class StatusController extends Controller
{
    public function __construct(public StatusServiceInterface $statusService)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/statuses",
     *     tags={"Statuses"},
     *     summary="Get list of statuses",
     *     @OA\Response(response=200, description="List of statuses")
     * )
     */
    public function index(): StatusCollection
    {
        $statuses = $this->statusService->findAll();

        return new StatusCollection(resource: $statuses);
    }

    /**
     * @OA\Post(
     *     path="/api/statuses",
     *     tags={"Statuses"},
     *     summary="Create a new status",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="MENUNGGU PERSETUJUAN"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Status created",
     *         @OA\JsonContent(
     *          @OA\Schema(
     *              schema="Post",
     *              required={"id", "name"},
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="DISETUJUI"),
     *           )
     *        )
     *     ),
     *
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreStatusRequest $request): JsonResponse
    {
        $status = $this->statusService->save(data: $request->validated());

        return (new StatusResource(resource: $status))->response()->setStatusCode(code: Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/statuses/{id}",
     *     tags={"Statuses"},
     *     summary="Get status by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Status found"),
     *     @OA\Response(response=404, description="Status not found")
     * )
     */
    public function show(string|int $id): StatusResource
    {
        $status = $this->statusService->findById(id: $id);

        return new StatusResource(resource: $status);
    }

    /**
     * @OA\Put(
     *     path="/api/statuses/{id}",
     *     tags={"Statuses"},
     *     summary="Update a status",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Updated Title"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status updated"),
     *     @OA\Response(response=404, description="Status not found")
     * )
     */
    public function update(UpdateStatusRequest $request, string|int $id): StatusResource
    {
        $status = $this->statusService->update(id: $id, data: $request->validated());

        return new StatusResource(resource: $status);
    }

    /**
     * @OA\Delete(
     *     path="/api/statuses/{id}",
     *     tags={"Statuses"},
     *     summary="Delete a status",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Status deleted"),
     *     @OA\Response(response=404, description="Status not found")
     * )
     */
    public function destroy(string|int $id): StatusResource
    {
        $this->statusService->delete(id: $id);

        return (new StatusResource(resource: null))->additional(['message' => 'Status deleted successfully']);
    }
}
