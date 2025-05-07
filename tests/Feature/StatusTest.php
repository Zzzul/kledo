<?php

namespace Tests\Feature;

use App\Enums\StatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_statuses(): void
    {
        $this->createStatusThroughApi(data: ['name' => StatusEnum::MENUNGGU_PERSETUJUAN->value]);
        $this->createStatusThroughApi(data: ['name' => StatusEnum::DISETUJUI->value]);

        $response = $this->getJson(uri: '/api/statuses');
        $response->assertOk()
            ->assertJsonCount(count: 2, key: 'statuses');
    }

    public function test_can_get_a_status(): void
    {
        $status = $this->createStatusThroughApi();

        $response = $this->getJson(uri: "/api/statuses/{$status['id']}");
        $response->assertOk();
    }

    public function test_can_create_status(): void
    {
        $payload = [
            'name' => StatusEnum::MENUNGGU_PERSETUJUAN->value,
        ];

        $response = $this->postJson(uri: '/api/statuses', data: $payload);
        $response->assertCreated();

        $this->assertDatabaseHas(table: 'statuses', data: $payload);
    }

    public function test_can_update_status(): void
    {
        $status = $this->createStatusThroughApi();

        $payload = [
            'name' => StatusEnum::DISETUJUI->value,
        ];

        $response = $this->putJson(uri: "/api/statuses/{$status['id']}", data: $payload);
        $response->assertOk();

        $this->assertDatabaseHas(table: 'statuses', data: $payload);
    }

    public function test_can_delete_status(): void
    {
        $status = $this->createStatusThroughApi();

        $response = $this->deleteJson(uri: "/api/statuses/{$status['id']}");
        $response->assertOk()->assertJson(['status' => null, 'message' => 'Status deleted successfully']);

        $this->assertDatabaseMissing(table: 'statuses', data: ['id' => $status['id']]);
    }
}
