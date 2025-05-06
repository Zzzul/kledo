<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalStages\StoreApprovalStageRequest;
use App\Http\Requests\ApprovalStages\UpdateApprovalStageRequest;
use App\Http\Resources\ApprovalStages\ApprovalStageCollection;
use App\Http\Resources\ApprovalStages\ApprovalStageResource;
use App\Interfaces\ApprovalStages\ApprovalStageServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Approval Stage",
 *     description="API Endpoints for managing approval stage"
 * )
 */
class ApprovalStageController extends Controller
{
    public function __construct(public ApprovalStageServiceInterface $approvalStageService)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/approval-stages",
     *     tags={"Approval Stages"},
     *     summary="Get list of approval stages",
     *
     *     @OA\Response(response=200, description="List of approval stages")
     * )
     */
    public function index(): ApprovalStageCollection
    {
        $approvalStageStages = $this->approvalStageService->findAll();

        return new ApprovalStageCollection(resource: $approvalStageStages);
    }

    /**
     * @OA\Post(
     *     path="/api/approval-stages",
     *     tags={"Approval Stages"},
     *     summary="Create a new approval stage",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"approver_id"},
     *
     *             @OA\Property(property="approver_id", type="integer", example="1")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Approval stage created",
     *
     *         @OA\JsonContent(
     *
     *          @OA\Schema(
     *              schema="Post",
     *              required={"id", "name"},
     *
     *              @OA\Property(property="id", type="integer", example="1"),
     *              @OA\Property(property="approver_id", type="integer", example="2"),
     *           )
     *        )
     *     ),
     *
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreApprovalStageRequest $request): JsonResponse
    {
        $approvalStage = $this->approvalStageService->save(data: $request->validated());

        return (new ApprovalStageResource(resource: $approvalStage))->response()->setStatusCode(code: Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/approval-stages/{id}",
     *     tags={"Approval Stages"},
     *     summary="Get approval stage by ID",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response=200, description="Approval stage found"),
     *     @OA\Response(response=404, description="Approval stage not found")
     * )
     */
    public function show(string|int $id): ApprovalStageResource
    {
        $approvalStage = $this->approvalStageService->findById(id: $id);

        return new ApprovalStageResource(resource: $approvalStage);
    }

    /**
     * @OA\Put(
     *     path="/api/approval-stages/{id}",
     *     tags={"Approval Stages"},
     *     summary="Update a approval stage",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"approver_id"},
     *
     *             @OA\Property(property="approver_id", type="integer", example="1"),
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Approval stage updated"),
     *     @OA\Response(response=404, description="Approval stage not found")
     * )
     */
    public function update(UpdateApprovalStageRequest $request, string|int $id): ApprovalStageResource
    {
        $approvalStage = $this->approvalStageService->update(id: $id, data: $request->validated());

        return new ApprovalStageResource(resource: $approvalStage);
    }

    /**
     * @OA\Delete(
     *     path="/api/approval-stages/{id}",
     *     tags={"Approval Stages"},
     *     summary="Delete a approval stage",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response=200, description="Approval stage deleted"),
     *     @OA\Response(response=404, description="Approval stage not found")
     * )
     */
    public function destroy(string|int $id): ApprovalStageResource
    {
        $this->approvalStageService->delete(id: $id);

        return (new ApprovalStageResource(resource: null))->additional(data: ['message' => 'Approval stage deleted successfully']);
    }
}
