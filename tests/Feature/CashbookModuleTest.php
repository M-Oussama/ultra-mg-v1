<?php

namespace Tests\Feature;

use App\Models\Cashbook;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleHasPermissions;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CashbookModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_admin_role_receives_cashbook_permissions_and_can_list_cashbooks(): void
    {
        $admin = $this->seedAdminUser();
        $adminRole = Role::query()->where('role', 'admin')->firstOrFail();

        $this->assertTrue(Permission::query()->where(['action' => 'add', 'subject' => 'cashbooks'])->exists());
        $this->assertTrue(Permission::query()->where(['action' => 'edit', 'subject' => 'cashbooks'])->exists());
        $this->assertTrue(Permission::query()->where(['action' => 'view', 'subject' => 'cashbooks'])->exists());
        $this->assertTrue(Permission::query()->where(['action' => 'download', 'subject' => 'cashbooks'])->exists());
        $this->assertTrue(Permission::query()->where(['action' => 'sync', 'subject' => 'cashbooks'])->exists());
        $this->assertTrue(Permission::query()->where(['action' => 'add', 'subject' => 'cashbook_settings'])->exists());
        $this->assertTrue(Permission::query()->where(['action' => 'list', 'subject' => 'cashbook_settings'])->exists());
        $this->assertTrue(Permission::query()->where(['action' => 'edit', 'subject' => 'cashbook_settings'])->exists());
        $this->assertTrue(Permission::query()->where(['action' => 'delete', 'subject' => 'cashbook_settings'])->exists());

        foreach ([
            ['action' => 'add', 'subject' => 'cashbooks'],
            ['action' => 'edit', 'subject' => 'cashbooks'],
            ['action' => 'view', 'subject' => 'cashbooks'],
            ['action' => 'download', 'subject' => 'cashbooks'],
            ['action' => 'sync', 'subject' => 'cashbooks'],
            ['action' => 'add', 'subject' => 'cashbook_settings'],
            ['action' => 'list', 'subject' => 'cashbook_settings'],
            ['action' => 'edit', 'subject' => 'cashbook_settings'],
            ['action' => 'delete', 'subject' => 'cashbook_settings'],
        ] as $permission) {
            $permissionId = Permission::query()->where($permission)->value('id');
            $this->assertNotNull($permissionId);
            $this->assertTrue(RoleHasPermissions::query()->where([
                'role_id' => $adminRole->id,
                'permission_id' => $permissionId,
            ])->exists());
        }

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/cashbooks');

        $response->assertOk();
        $response->assertJsonPath('meta.count', 0);
    }

    public function test_cashbook_lookup_transaction_and_pdf_flow_works_end_to_end(): void
    {
        $admin = $this->seedAdminUser();
        Sanctum::actingAs($admin);

        $cashbookId = $this->createCashbook('Main Cashbook', 'Daily ledger for operations');
        $this->updateCashbook($cashbookId, 'Main Cashbook Updated', 'Updated description');

        $contactId = $this->createLookup($cashbookId, 'contacts', [
            'name' => 'Acme Logistics',
            'phone' => '+2135550101',
            'email' => 'ops@example.test',
            'notes' => 'Primary dispatch contact',
        ]);

        $categoryId = $this->createLookup($cashbookId, 'categories', [
            'name' => 'Dispatch income',
            'kind' => 'income',
            'notes' => 'Revenue from deliveries',
        ]);

        $paymentModeId = $this->createLookup($cashbookId, 'payment-modes', [
            'name' => 'Cash counter',
            'notes' => 'Physical cash received at the counter',
        ]);

        $incomeTransactionId = $this->createTransaction($cashbookId, [
            'type' => 'income',
            'amount' => '120.00',
            'note' => 'Morning dispatch',
            'transaction_date' => '2026-06-13',
            'contact_id' => $contactId,
            'category_id' => $categoryId,
            'payment_mode_id' => $paymentModeId,
        ])->json('transaction.id');

        $expenseResponse = $this->createTransaction($cashbookId, [
            'type' => 'expense',
            'amount' => '32.50',
            'note' => 'Fuel refill',
            'transaction_date' => '2026-06-13',
            'transaction_time' => '08:55:00',
            'contact_id' => $contactId,
            'category_id' => $categoryId,
            'payment_mode_id' => $paymentModeId,
            'attachment' => UploadedFile::fake()->image('fuel.jpg'),
        ]);

        $expenseTransactionId = $expenseResponse->json('transaction.id');

        $expenseResponse->assertCreated();
        $expenseResponse->assertJsonPath('transaction.attachments.0.file_name', 'fuel.jpg');

        $updateResponse = $this->updateTransaction($expenseTransactionId, [
            'type' => 'expense',
            'amount' => '35.25',
            'note' => 'Fuel refill updated',
            'transaction_date' => '2026-06-13',
            'transaction_time' => '08:59:00',
            'contact_id' => $contactId,
            'category_id' => $categoryId,
            'payment_mode_id' => $paymentModeId,
            'replace_attachments' => true,
            'attachment' => UploadedFile::fake()->image('fuel-updated.jpg'),
        ]);

        $updateResponse->assertOk();
        $updateResponse->assertJsonPath('transaction.amount', 35.25);
        $updateResponse->assertJsonPath('transaction.attachments.0.file_name', 'fuel-updated.jpg');

        $this->deleteJson("/api/cashbooks/{$cashbookId}/lookups/contacts/{$contactId}")
            ->assertOk();

        $this->assertSoftDeleted('cashbook_contacts', ['id' => $contactId]);

        $transactionsResponse = $this->getJson("/api/cashbooks/{$cashbookId}/transactions");
        $transactionsResponse->assertOk();
        $transactionsResponse->assertJsonFragment(['contact_name' => 'Acme Logistics']);
        $transactionsResponse->assertJsonFragment(['user_name' => 'ADMIN']);
        $transactionsResponse->assertJsonPath('data.0.transaction_time', '08:59:00');
        $this->assertCount(2, $transactionsResponse->json('data'));

        $cashbooksResponse = $this->getJson('/api/cashbooks');
        $cashbooksResponse->assertOk();
        $cashbooksResponse->assertJsonPath('data.0.balance', 84.75);
        $cashbooksResponse->assertJsonPath('data.0.transaction_count', 2);

        $this->deleteJson("/api/transactions/{$incomeTransactionId}")
            ->assertOk();

        $transactionsAfterDelete = $this->getJson("/api/cashbooks/{$cashbookId}/transactions");
        $transactionsAfterDelete->assertOk();
        $this->assertCount(1, $transactionsAfterDelete->json('data'));
        $transactionsAfterDelete->assertJsonPath('data.0.amount', 35.25);

        $importResponse = $this->post("/api/cashbooks/{$cashbookId}/import-excel", [
            'file' => UploadedFile::fake()->createWithContent(
                'legacy-cashbook.csv',
                implode("\n", [
                    'Date,Time,Remark,Party,Category,Mode,Entry By,Cash In,Cash Out,Balance',
                    '8-Sep-25,8:55 PM,Legacy income row,Acme Logistics,Dispatch income,Cash,ADMIN,150.00,,150.00',
                    '8-Sep-25,9:00 PM,Legacy expense row,,Fuel,Cash,ADMIN,,25.00,125.00',
                ])
            ),
        ]);

        $importResponse->assertOk();
        $importResponse->assertJsonPath('stats.imported', 2);

        $legacyTransactions = $this->getJson("/api/cashbooks/{$cashbookId}/transactions");
        $legacyTransactions->assertOk();
        $this->assertCount(3, $legacyTransactions->json('data'));
        $legacyTransactions->assertJsonPath('data.1.transaction_time', '21:00:00');
        $legacyTransactions->assertJsonPath('data.2.transaction_time', '20:55:00');

        $pdfResponse = $this->get("/api/cashbooks/{$cashbookId}/export-pdf");
        $pdfResponse->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $pdfResponse->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $pdfResponse->getContent());

        $this->deleteJson("/api/cashbooks/{$cashbookId}")
            ->assertOk();

        $this->getJson("/api/cashbooks/{$cashbookId}")
            ->assertStatus(404);
    }

    public function test_transactions_accept_attachment_arrays_on_create_and_update(): void
    {
        $admin = $this->seedAdminUser();
        Sanctum::actingAs($admin);

        $cashbookId = $this->createCashbook('Attachment Array Cashbook', 'Validates multi-file uploads');

        $createResponse = $this->createTransaction($cashbookId, [
            'type' => 'income',
            'amount' => '44.00',
            'note' => 'Batch receipt import',
            'transaction_date' => '2026-06-14',
            'attachments' => [
                UploadedFile::fake()->image('receipt-a.jpg'),
                UploadedFile::fake()->image('receipt-b.jpg'),
            ],
        ]);

        $createResponse->assertCreated();
        $createResponse->assertJsonPath('transaction.attachments.0.file_name', 'receipt-a.jpg');
        $createResponse->assertJsonPath('transaction.attachments.1.file_name', 'receipt-b.jpg');

        $transactionId = (int) $createResponse->json('transaction.id');

        $updateResponse = $this->updateTransaction($transactionId, [
            'type' => 'income',
            'amount' => '55.00',
            'note' => 'Batch receipt updated',
            'transaction_date' => '2026-06-14',
            'transaction_time' => '09:15:00',
            'replace_attachments' => true,
            'attachments' => [
                UploadedFile::fake()->image('receipt-c.jpg'),
            ],
        ]);

        $updateResponse->assertOk();
        $updateResponse->assertJsonPath('transaction.amount', 55);
        $updateResponse->assertJsonPath('transaction.attachments.0.file_name', 'receipt-c.jpg');
        $updateResponse->assertJsonCount(1, 'transaction.attachments');

        $detailResponse = $this->getJson("/api/transactions/{$transactionId}");
        $detailResponse->assertOk();
        $detailResponse->assertJsonPath('transaction.id', $transactionId);
        $detailResponse->assertJsonPath('transaction.attachments.0.file_name', 'receipt-c.jpg');
        $detailResponse->assertJsonCount(1, 'transaction.attachments');
    }

    public function test_business_creation_endpoint_updates_the_current_business(): void
    {
        $admin = $this->seedAdminUser();
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/businesses/store', [
            'name' => 'North Star Trading',
            'description' => 'Primary business profile',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('business_name', 'North Star Trading');

        $businessId = (int) $response->json('business_id');

        $this->assertDatabaseHas('organizations', [
            'id' => $businessId,
            'name' => 'North Star Trading',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'organization_id' => $businessId,
        ]);

        $this->getJson('/api/businesses/current')
            ->assertOk()
            ->assertJsonPath('business.id', $businessId)
            ->assertJsonPath('business.name', 'North Star Trading');
    }

    public function test_cashbook_member_crud_flow_works(): void
    {
        $admin = $this->seedAdminUser();
        Sanctum::actingAs($admin);

        $businessResponse = $this->postJson('/api/businesses/store', [
            'name' => 'Member Ops',
            'description' => 'Business used for member testing',
        ]);
        $businessResponse->assertCreated();

        $businessId = (int) $businessResponse->json('business_id');
        $cashbookId = $this->createCashbook('Team Ledger', 'Collaborative book');

        $member = User::factory()->create([
            'name' => 'Book Editor',
            'email' => 'editor@example.test',
            'role_id' => null,
            'organization_id' => $businessId,
        ]);

        $addResponse = $this->postJson("/api/cashbooks/{$cashbookId}/members", [
            'user_id' => $member->id,
            'role' => 'editor',
        ]);

        $addResponse->assertCreated();
        $addResponse->assertJsonPath('member.user_id', $member->id);
        $addResponse->assertJsonPath('member.role', 'editor');

        $memberId = (int) $addResponse->json('member.id');

        $this->assertDatabaseHas('cashbook_members', [
            'id' => $memberId,
            'cashbook_id' => $cashbookId,
            'user_id' => $member->id,
            'role' => 'editor',
        ]);

        $showResponse = $this->getJson("/api/cashbooks/{$cashbookId}");
        $showResponse->assertOk();
        $showResponse->assertJsonPath('cashbook.member_count', 2);
        $showResponse->assertJsonCount(2, 'members');

        $updateResponse = $this->putJson("/api/cashbooks/{$cashbookId}/members/{$memberId}", [
            'role' => 'manager',
        ]);
        $updateResponse->assertOk();
        $updateResponse->assertJsonPath('member.role', 'manager');

        $this->assertDatabaseHas('cashbook_members', [
            'id' => $memberId,
            'role' => 'manager',
        ]);

        $deleteResponse = $this->deleteJson("/api/cashbooks/{$cashbookId}/members/{$memberId}");
        $deleteResponse->assertOk();
        $deleteResponse->assertJsonCount(1, 'members');

        $this->assertDatabaseMissing('cashbook_members', [
            'id' => $memberId,
        ]);

        $finalShowResponse = $this->getJson("/api/cashbooks/{$cashbookId}");
        $finalShowResponse->assertOk();
        $finalShowResponse->assertJsonPath('cashbook.member_count', 1);
        $finalShowResponse->assertJsonCount(1, 'members');
    }

    public function test_non_admin_users_are_denied_cashbook_access(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create([
            'name' => 'Cashbook Viewer',
            'email' => 'viewer@example.test',
            'role_id' => null,
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/cashbooks')
            ->assertForbidden();

        $this->postJson('/api/cashbooks', [
            'name' => 'Denied cashbook',
            'description' => 'Should not be created',
        ])->assertForbidden();
    }

    public function test_push_token_endpoint_stores_and_clears_device_token(): void
    {
        $user = User::factory()->create([
            'name' => 'Push Token User',
            'email' => 'push@example.test',
            'role_id' => null,
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/push-token', [
            'token' => 'fcm-test-token',
            'platform' => 'android',
            'device_name' => 'Pixel 8',
        ])->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'fcm_token' => 'fcm-test-token',
            'fcm_platform' => 'android',
            'fcm_device_name' => 'Pixel 8',
        ]);

        $this->postJson('/api/push-token/clear')
            ->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'fcm_token' => null,
            'fcm_platform' => null,
            'fcm_device_name' => null,
        ]);
    }

    private function createCashbook(string $name, ?string $description = null): int
    {
        $response = $this->postJson('/api/cashbooks', [
            'name' => $name,
            'description' => $description,
        ]);

        $response->assertCreated();

        return (int) $response->json('cashbook.id');
    }

    private function updateCashbook(int $cashbookId, string $name, ?string $description = null): void
    {
        $this->putJson("/api/cashbooks/{$cashbookId}", [
            'name' => $name,
            'description' => $description,
        ])->assertOk();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function createLookup(int $cashbookId, string $type, array $payload): int
    {
        $response = $this->postJson("/api/cashbooks/{$cashbookId}/lookups/{$type}", $payload);
        $response->assertCreated();

        return (int) $response->json('lookup.id');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function createTransaction(int $cashbookId, array $payload)
    {
        $data = array_filter($payload, static fn ($value) => $value !== null);

        return $this->payloadContainsFile($data)
            ? $this->post("/api/cashbooks/{$cashbookId}/transactions", $data)
            : $this->postJson("/api/cashbooks/{$cashbookId}/transactions", $data);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function updateTransaction(int $transactionId, array $payload)
    {
        $data = array_filter($payload, static fn ($value) => $value !== null);

        return $this->payloadContainsFile($data)
            ? $this->post("/api/transactions/{$transactionId}", array_merge(['_method' => 'PUT'], $data))
            : $this->postJson("/api/transactions/{$transactionId}", array_merge(['_method' => 'PUT'], $data));
    }

    private function payloadContainsFile(array $payload): bool
    {
        foreach ($payload as $value) {
            if ($value instanceof UploadedFile) {
                return true;
            }

            if (is_array($value) && $this->payloadContainsFile($value)) {
                return true;
            }
        }

        return false;
    }

    private function seedAdminUser(): User
    {
        $this->seed(PermissionSeeder::class);

        return User::query()->where('email', 'admin@gmail.com')->firstOrFail();
    }
}
