<?php

namespace Tests\Feature;

use Tests\TestCase;

class FilamentPanelRoutesRemovedTest extends TestCase
{
    public function test_admin_route_returns_not_found(): void
    {
        $this->get('/admin')->assertNotFound();
    }

    public function test_admin_login_route_returns_not_found(): void
    {
        $this->get('/admin/login')->assertNotFound();
    }

    public function test_admin_reports_route_returns_not_found(): void
    {
        $this->get('/admin/reports')->assertNotFound();
    }
}
