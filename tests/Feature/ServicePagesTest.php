<?php

namespace Tests\Feature;

use Tests\TestCase;

class ServicePagesTest extends TestCase
{
    public function test_home_page_service_cards_link_to_dynamic_service_pages(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('services.show', 'general-checkup'), false);
        $response->assertSee(route('services.show', 'orthodontics'), false);
    }

    public function test_service_detail_page_renders_unique_content_for_the_selected_service(): void
    {
        $response = $this->get(route('services.show', 'orthodontics'));

        $response->assertOk();
        $response->assertSee('Orthodontics');
        $response->assertSee('Treatment planning for braces or aligners');
        $response->assertSee('Results you can expect');
        $response->assertDontSee('Professional whitening application');
    }

    public function test_unknown_service_slug_returns_not_found(): void
    {
        $this->get(route('services.show', 'non-existent-service'))
            ->assertNotFound();
    }
}
