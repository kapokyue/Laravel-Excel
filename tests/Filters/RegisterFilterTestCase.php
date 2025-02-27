<?php

use Mockery as m;

class RegisterFilterTestCase extends TestCase {

    protected function setUp(): void
    {
        parent::setUp();
        $this->excel = app('excel');
    }

    public function testRegisterThroughConfig()
    {
        $registered = config('excel.filters.registered');

        $filters = $this->excel->getFilters('registered');
        $this->assertEquals($registered, $filters);
    }

    public function testOnlyRegister()
    {
        $toRegister = [
            'chunk' =>  'ChunkFilter'
        ];

        $excel = $this->excel->registerFilters($toRegister);

        $filters = $this->excel->getFilters('registered');
        $this->assertEquals($toRegister, $filters);
    }

    public function testRegisterAndEnabled()
    {
        $toRegister = [
            'registered'    =>  [
                'chunk' =>  'ChunkFilter'
            ],
            'enabled'   =>  [
                'chunk'
            ]
        ];

        $excel = $this->excel->registerFilters($toRegister);

        $filters = $this->excel->getFilters();
        $this->assertEquals($toRegister, $filters);

    }

    public function testEnableOneFilter()
    {
        $excel = $this->excel->filter('chunk');

        $filters = $this->excel->getFilters('enabled');
        $this->assertContains('chunk', $filters);
    }

    public function testEnableMultipleFilter()
    {
        $excel = $this->excel->filter(['chunk', 'range']);

        $filters = $this->excel->getFilters('enabled');
        $this->assertContains('chunk', $filters);
        $this->assertContains('range', $filters);
    }

    public function testEnableFilterAndOverruleFilterClass()
    {
        $excel = $this->excel->filter('chunk', 'ChunkFilter');

        $registered = $this->excel->getFilters('registered');
        $this->assertEquals(['chunk' => 'ChunkFilter'], $registered);

        $enabled    = $this->excel->getFilters('enabled');
        $this->assertContains('chunk', $enabled);

    }
}