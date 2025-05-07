<?php

namespace Tests;

use App\Enums\StatusEnum;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApprovalStageThroughApi(array $data = []): array
    {
        $this->createApproverThroughApi(data: ['name' => 'Ani']);
        $this->createApproverThroughApi(data: ['name' => 'Ina']);
        $this->createApproverThroughApi(data: ['name' => 'Ana']);

        $payload = array_merge([
            'approver_id' => 1,
        ], $data);

        $response = $this->postJson(uri: '/api/approval-stages', data: $payload);
        $response->assertCreated();

        return $response->json(key: 'approval_stage');
    }

    public function createApprovalStageWithoutApproverThroughApi(array $data = []): array
    {
        $payload = array_merge([
            'approver_id' => 1,
        ], $data);

        $response = $this->postJson(uri: '/api/approval-stages', data: $payload);
        $response->assertCreated();

        return $response->json(key: 'approval_stage');
    }

    public function createApproverThroughApi(array $data = []): array
    {
        $payload = array_merge([
            'name' => 'Ana',
        ], $data);

        $response = $this->postJson(uri: '/api/approvers', data: $payload);
        $response->assertCreated();

        return $response->json(key: 'approver');
    }

    public function createStatusThroughApi(array $data = []): array
    {
        $payload = array_merge([
            'name' => StatusEnum::MENUNGGU_PERSETUJUAN->value,
        ], $data);

        $response = $this->postJson(uri: '/api/statuses', data: $payload);
        $response->assertCreated();

        return $response->json(key: 'status');
    }
}
