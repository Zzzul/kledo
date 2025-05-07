<?php

namespace Tests\Feature;

use App\Enums\StatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createStatusThroughApi(data: ['name' => StatusEnum::MENUNGGU_PERSETUJUAN->value]);
        $this->createStatusThroughApi(data: ['name' => StatusEnum::DISETUJUI->value]);

        $this->createApproverThroughApi(data: ['name' => 'Ani']);
        $this->createApproverThroughApi(data: ['name' => 'Ana']);
        $this->createApproverThroughApi(data: ['name' => 'Ina']);

        $this->createApprovalStageWithoutApproverThroughApi(data: ['approver_id' => 1]);
        $this->createApprovalStageWithoutApproverThroughApi(data: ['approver_id' => 2]);
        $this->createApprovalStageWithoutApproverThroughApi(data: ['approver_id' => 3]);
    }

    public function createExpenseThroughApi(array $data = []): array
    {
        $payload = array_merge([
            'amount' => 5_000,
        ], $data);

        $response = $this->postJson(uri: '/api/expenses', data: $payload);
        $response->assertCreated();

        return $response->json(key: 'expense');
    }

    public function test_can_get_all_expenses(): void
    {
        $this->createExpenseThroughApi(data: ['amount' => 1]);

        $response = $this->getJson(uri: '/api/expenses');
        $response->assertOk()->assertJsonCount(count: 1, key: 'expenses');

        $this->withOutExceptionHandling();
    }

    public function test_can_get_a_expense(): void
    {
        $expense = $this->createExpenseThroughApi();

        $response = $this->getJson(uri: "/api/expenses/{$expense['id']}");
        $response->assertOk();
    }

    public function test_can_create_expense(): void
    {
        $payload = [
            'amount' => 1_000,
        ];

        $response = $this->postJson(uri: '/api/expenses', data: $payload);
        $response->assertCreated();

        $this->assertDatabaseHas(table: 'expenses', data: $payload);
    }

    public function test_can_update_expense(): void
    {
        $expense = $this->createExpenseThroughApi();

        $payload = [
            'amount' => 2_000,
        ];

        $response = $this->putJson(uri: "/api/expenses/{$expense['id']}", data: $payload);
        $response->assertOk();

        $this->assertDatabaseHas(table: 'expenses', data: $payload);
    }

    public function test_can_delete_expense(): void
    {
        $expense = $this->createExpenseThroughApi();

        $response = $this->deleteJson(uri: "/api/expenses/{$expense['id']}");
        $response->assertOk()->assertJson(value: ['expense' => null, 'message' => 'Expense deleted successfully']);

        $this->assertDatabaseMissing(table: 'expenses', data: ['id' => $expense['id']]);
    }

    public function test_amount_minimum_is_1()
    {
        $payload = [
            'amount' => 1,
        ];

        $response = $this->postJson(uri: '/api/expenses', data: $payload);
        $response->assertCreated();

        $this->assertDatabaseHas(table: 'expenses', data: $payload);
    }

    public function test_amount_cant_be_0_or_less()
    {
        $payload = [
            'amount' => 0,
        ];

        $payload2 = [
            'amount' => -1,
        ];

        $response = $this->postJson(uri: '/api/expenses', data: $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonValidationErrors(['amount']);

        $response = $this->postJson(uri: '/api/expenses', data: $payload2);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonValidationErrors(['amount']);
    }

    public function test_amount_cant_be_string()
    {
        $payload = [
            'amount' => 'string',
        ];

        $response = $this->postJson(uri: '/api/expenses', data: $payload);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonValidationErrors(['amount']);
    }

    public function test_all_approver_approved_from_three_approvals(): void {}

    public function test_only_two_approver_approved_from_three_approvals(): void {}

    public function test_only_one_approver_approved_from_three_approvals(): void {}

    public function test_all_approver_not_approved_from_three_approvals(): void {}
}
