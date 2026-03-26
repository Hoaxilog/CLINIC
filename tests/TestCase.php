<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\View\Component as BladeComponent;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Use an isolated temp folder for compiled views to avoid Windows
        // rename/file-lock conflicts in storage/framework/views during tests.
        $compiledPath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR)
            .DIRECTORY_SEPARATOR.'clinic-test-views'
            .DIRECTORY_SEPARATOR.'p'.getmypid()
            .DIRECTORY_SEPARATOR.bin2hex(random_bytes(6));

        if (! is_dir($compiledPath)) {
            mkdir($compiledPath, 0777, true);
        }

        config()->set('view.compiled', $compiledPath);
        BladeComponent::flushCache();
        BladeComponent::forgetFactory();
    }
}
