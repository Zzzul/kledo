<?php

namespace App\Http\Controllers;

use App\Http\Requests\Approvers\StoreApproverRequest;
use App\Http\Requests\Approvers\UpdateApproverRequest;
use App\Http\Resources\Approvers\ApproverCollection;
use App\Http\Resources\Approvers\ApproverResource;
use App\Interfaces\ApproverServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Approver",
 *     description="API Endpoints for managing approver"
 * )
 */
class ApproverController extends Controller
{
    public function __construct(public ApproverServiceInterface $approverService)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/approvers",
     *     tags={"Approvers"},
     *     summary="Get list of approvers",
     *     @OA\Response(response=200, description="List of approvers")
     * )
     */
    public function index(): ApproverCollection
    {
        $approvers = $this->approverService->findAll();

        return new ApproverCollection(resource: $approvers);
    }

    /**
     * @OA\Post(
     *     path="/api/approvers",
     *     tags={"Approvers"},
     *     summary="Create a new approver",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Ana")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Approver created",
     *         @OA\JsonContent(
     *          @OA\Schema(
     *              schema="Post",
     *              required={"id", "name"},
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="Ina"),
     *           )
     *        )
     *     ),
     *
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreApproverRequest $request): JsonResponse
    {
        $approver = $this->approverService->save(data: $request->validated());

        return (new ApproverResource(resource: $approver))->response()->setStatusCode(code: Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/approvers/{id}",
     *     tags={"Approvers"},
     *     summary="Get approver by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Approver found"),
     *     @OA\Response(response=404, description="Approver not found")
     * )
     */
    public function show(string|int $id): ApproverResource
    {
        $approver = $this->approverService->findById(id: $id);

        return new ApproverResource(resource: $approver);
    }

    /**
     * @OA\Put(
     *     path="/api/approvers/{id}",
     *     tags={"Approvers"},
     *     summary="Update a approver",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Updated Name"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Approver updated"),
     *     @OA\Response(response=404, description="Approver not found")
     * )
     */
    public function update(UpdateApproverRequest $request, string|int $id): ApproverResource
    {
        $approver = $this->approverService->update(id: $id, data: $request->validated());

        return new ApproverResource(resource: $approver);
    }

    /**
     * @OA\Delete(
     *     path="/api/approvers/{id}",
     *     tags={"Approvers"},
     *     summary="Delete a approver",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Approver deleted"),
     *     @OA\Response(response=404, description="Approver not found")
     * )
     */
    public function destroy(string|int $id): ApproverResource
    {
        $this->approverService->delete(id: $id);

        return (new ApproverResource(resource: null))->additional(['message' => 'Approver deleted successfully']);
    }
}
