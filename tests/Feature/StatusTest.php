<?php

namespace Tests\Feature;

use App\Enums\StatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
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

    public function test_status_name_is_required(): void
    {
        $payload = [
            'name' => null,
        ];

        $this->postJson(uri: '/api/statuses', data: $payload)
            ->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_status_name_cant_more_than_255(): void
    {
        $payload = [
            'name' => str()->random(256),
        ];

        $this->postJson(uri: '/api/statuses', data: $payload)
            ->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_status_name_must_be_in_enum_status(): void
    {
        $payload = [
            'name' => str()->random(),
        ];

        $this->postJson(uri: '/api/statuses', data: $payload)
            ->assertStatus(status: Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['name']);
    }
}
