<?php
// ============================================================
// FILE: tests/Unit/DsrServiceTest.php
// ============================================================
namespace Tests\Unit;

use App\Services\DsrService;
use Tests\TestCase;

class DsrServiceTest extends TestCase
{
    public function test_dsr_label_returns_correct_levels(): void
    {
        $service = app(DsrService::class);

        $this->assertEquals('Beginner',     $service->getDsrLabel(30.0));
        $this->assertEquals('Intermediate', $service->getDsrLabel(55.0));
        $this->assertEquals('Advanced',     $service->getDsrLabel(75.0));
        $this->assertEquals('Elite',        $service->getDsrLabel(90.0));
    }
}
