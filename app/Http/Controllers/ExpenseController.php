<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expenses\ApproveExpenseRequest;
use App\Http\Requests\Expenses\StoreExpenseRequest;
use App\Http\Requests\Expenses\UpdateExpenseRequest;
use App\Http\Resources\Expenses\ExpenseCollection;
use App\Http\Resources\Expenses\ExpenseResource;
use App\Interfaces\Expenses\ExpenseServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Expense",
 *     description="API Endpoints for managing expense"
 * )
 */
class ExpenseController extends Controller
{
    public function __construct(public ExpenseServiceInterface $expenseService)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/expenses",
     *     tags={"Expenses"},
     *     summary="Get list of expenses",
     *
     *     @OA\Response(response=200, description="List of expenses")
     * )
     */
    public function index(): ExpenseCollection
    {
        $expenses = $this->expenseService->findAll();

        return new ExpenseCollection(resource: $expenses);
    }

    /**
     * @OA\Post(
     *     path="/api/expenses",
     *     tags={"Expenses"},
     *     summary="Create a new expense",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"amount"},
     *
     *             @OA\Property(property="amount", type="number", example="5100")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Expense created",
     *
     *         @OA\JsonContent(
     *
     *          @OA\Schema(
     *              schema="Expense",
     *              required={"id", "amount"},
     *
     *              @OA\Property(property="id", type="integer", example="1"),
     *              @OA\Property(property="amount", type="number", example="6000"),
     *           )
     *        )
     *     ),
     *
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $expense = $this->expenseService->save(data: $request->validated());

        return (new ExpenseResource(resource: $expense))->response()->setStatusCode(code: Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/expenses/{id}",
     *     tags={"Expenses"},
     *     summary="Get expense by ID",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response=200, description="Expense found"),
     *     @OA\Response(response=404, description="Expense not found")
     * )
     */
    public function show(string|int $id): ExpenseResource
    {
        $expense = $this->expenseService->findById(id: $id);

        return new ExpenseResource(resource: $expense);
    }

    /**
     * @OA\Put(
     *     path="/api/expenses/{id}",
     *     tags={"Expenses"},
     *     summary="Update a expense",
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
     *             required={"amount"},
     *
     *             @OA\Property(property="amount", type="number", example="5000"),
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Expense updated"),
     *     @OA\Response(response=404, description="Expense not found")
     * )
     */
    public function update(UpdateExpenseRequest $request, string|int $id): ExpenseResource
    {
        $expense = $this->expenseService->update(id: $id, data: $request->validated());

        return new ExpenseResource(resource: $expense);
    }

    /**
     * @OA\Delete(
     *     path="/api/expenses/{id}",
     *     tags={"Expenses"},
     *     summary="Delete a expense",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response=200, description="Expense deleted"),
     *     @OA\Response(response=404, description="Expense not found")
     * )
     */
    public function destroy(string|int $id): ExpenseResource
    {
        $this->expenseService->delete(id: $id);

        return (new ExpenseResource(resource: null))->additional(['message' => 'Expense deleted successfully']);
    }

    /**
     * @OA\Delete(
     *     path="/api/expenses/{id}/approve",
     *     tags={"Expenses"},
     *     summary="Approve a expense",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(response=200, description="Expense deleted"),
     *     @OA\Response(response=404, description="Expense not found")
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function approve(string|int $id, ApproveExpenseRequest $request): ExpenseResource
    {
        $expense = $this->expenseService->approve(expenseId: $id, approverId: $request->approver_id);

        return new ExpenseResource(resource: $expense);
    }
}
