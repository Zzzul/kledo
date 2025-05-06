<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ApprovalStageTest extends TestCase
{
    use RefreshDatabase;

    protected function createApprovalStageThroughApi(array $data = []): array
    {
        $this->createApproverThroughApi(data: ['name' => 'Ani']);
        $this->createApproverThroughApi(data: ['name' => 'Ina']);

        $payload = array_merge([
            'approver_id' => 1,
        ], $data);

        $response = $this->postJson(uri: '/api/approval-stages', data: $payload);
        $response->assertCreated();

        return $response->json(key: 'approval_stage');
    }

    protected function createApproverThroughApi(array $data = []): array
    {
        $payload = array_merge([
            'name' => 'Ana',
        ], $data);

        $response = $this->postJson(uri: '/api/approvers', data: $payload);
        $response->assertCreated();

        return $response->json(key: 'approver');
    }

    public function test_can_get_all_approval_stages(): void
    {
        $this->createApprovalStageThroughApi(data: ['approver_id' => 1]);

        $response = $this->getJson(uri: '/api/approval-stages');
        $response->assertOk()->assertJsonCount(count: 1, key: 'approval_stages');

        $this->withOutExceptionHandling();
    }

    public function test_can_get_a_approval_stage(): void
    {
        $approvalStage = $this->createApprovalStageThroughApi();

        $response = $this->getJson(uri: "/api/approval-stages/{$approvalStage['id']}");
        $response->assertOk();
    }

    public function test_can_create_approval_stage(): void
    {
        $this->createApproverThroughApi();

        $payload = [
            'approver_id' => 1,
        ];

        $response = $this->postJson(uri: '/api/approval-stages', data: $payload);
        $response->assertCreated();

        $this->assertDatabaseHas(table: 'approval_stages', data: $payload);
    }

    public function test_can_update_approval_stage(): void
    {
        $approvalStage = $this->createApprovalStageThroughApi();

        $payload = [
            'approver_id' => 2,
        ];

        $response = $this->putJson(uri: "/api/approval-stages/{$approvalStage['id']}", data: $payload);
        $response->assertOk();

        $this->assertDatabaseHas(table: 'approval_stages', data: $payload);
    }

    public function test_can_delete_approval_stage(): void
    {
        $approvalStage = $this->createApprovalStageThroughApi();

        $response = $this->deleteJson(uri: "/api/approval-stages/{$approvalStage['id']}");
        $response->assertOk()->assertJson(value: ['approval_stage' => null, 'message' => 'Approval stage deleted successfully']);

        $this->assertDatabaseMissing(table: 'approval_stages', data: ['id' => $approvalStage['id']]);
    }

    public function test_cant_create_with_same_approver_id(): void
    {
        $this->createApprovalStageThroughApi(data: ['approver_id' => 1]);

        $response = $this->postJson(uri: '/api/approval-stages', data: ['approver_id' => 1]);
        $response->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_cant_update_with_same_approver_id(): void
    {
        $this->createApprovalStageThroughApi();

        $response = $this->putJson(uri: '/api/approval-stages/1', data: ['approver_id' => 1]);
        $response->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
