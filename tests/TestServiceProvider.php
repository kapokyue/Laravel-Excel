<?php

use Maatwebsite\Excel\ExcelServiceProvider;
use Mockery as m;

class TestServiceProvider extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        m::close();
    }

    public function testProviders()
    {
        $app = m::mock('Illuminate\Foundation\Application');
        $provider = new ExcelServiceProvider($app);

        $this->assertCount(6, $provider->provides());

        $this->assertContains('excel', $provider->provides());
        $this->assertContains('phpexcel', $provider->provides());
        $this->assertContains('excel.reader', $provider->provides());
        $this->assertContains('excel.readers.html', $provider->provides());
        $this->assertContains('excel.parsers.view', $provider->provides());
        $this->assertContains('excel.writer', $provider->provides());
    }
}