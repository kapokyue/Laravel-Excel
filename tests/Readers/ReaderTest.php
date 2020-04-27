<?php

use Mockery as m;
use Maatwebsite\Excel\Readers\LaravelExcelReader;
use Maatwebsite\Excel\Classes;
use Illuminate\Support\Facades\App;

class ReaderTest extends TestCase {

    /**
     * Setup
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set excel class
        $this->excel    = App::make('phpexcel');

        // Set writer class
        $this->reader   = App::make('excel.reader');
        $this->reader->injectExcel($this->excel);
    }

    /**
     * Test the excel injection
     * @return [type] [description]
     */
    public function testExcelInjection()
    {
        $this->assertEquals($this->excel, $this->reader->getExcel());
    }

}