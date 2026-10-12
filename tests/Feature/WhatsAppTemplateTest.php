<?php

namespace Tests\Feature;

use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppTemplateTest extends TestCase
{
    public function test_it_collects_approved_templates_from_every_meta_page(): void
    {
        config([
            'services.whatsapp.waba_id' => '123456789',
            'services.whatsapp.access_token' => 'test-token',
            'services.whatsapp.graph_version' => 'v25.0',
        ]);

        Http::fakeSequence()
            ->push([
                'data' => [
                    ['name' => 'pending_offer', 'language' => 'en_US', 'status' => 'PENDING', 'category' => 'MARKETING'],
                    ['name' => 'approved_lowercase', 'language' => 'fr', 'status' => 'approved', 'category' => 'UTILITY'],
                ],
                'paging' => [
                    'cursors' => ['after' => 'next-page'],
                    'next' => 'https://graph.facebook.com/v25.0/123456789/message_templates?after=next-page',
                ],
            ])
            ->push([
                'data' => [
                    ['name' => 'approved_second_page', 'language' => 'ar', 'status' => 'APPROVED', 'category' => 'UTILITY'],
                    ['name' => 'approved_lowercase', 'language' => 'fr', 'status' => 'APPROVED', 'category' => 'UTILITY'],
                ],
            ]);

        $response = app(WhatsAppController::class)->templates();
        $payload = $response->getData(true);

        $this->assertSame(200, $response->status());
        $this->assertSame(
            ['approved_lowercase', 'approved_second_page'],
            array_column($payload['data'], 'name'),
        );
        $this->assertSame(2, $payload['meta']['approved_count']);
        $this->assertSame(['PENDING' => 1, 'APPROVED' => 3], $payload['meta']['status_counts']);
        Http::assertSentCount(2);
        Http::assertSent(fn ($request) => ($request->data()['after'] ?? null) === 'next-page');
    }
}
