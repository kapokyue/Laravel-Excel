<?php

use Mockery as m;

class ChunkReadFilterTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->excel = app('excel');
    }

    public function testCanChunkXls()
    {
        $this->assertCanChunkIntoGroups(1, "sample.xls", 20);
        $this->assertCanChunkIntoGroups(1, "sample.xls", 15);
        $this->assertCanChunkIntoGroups(2, "sample.xls", 10);
        $this->assertCanChunkIntoGroups(3, "sample.xls", 5);
        $this->assertCanChunkIntoGroups(15, "sample.xls", 1);
    }

    public function testCanChunkXlsx()
    {
        $this->assertCanChunkIntoGroups(1, "sample.xlsx", 20);
        $this->assertCanChunkIntoGroups(1, "sample.xlsx", 15);
        $this->assertCanChunkIntoGroups(2, "sample.xlsx", 10);
        $this->assertCanChunkIntoGroups(3, "sample.xlsx", 5);
        $this->assertCanChunkIntoGroups(15, "sample.xlsx", 1);
    }

    public function testCanChunkCsv()
    {
        $this->assertCanChunkIntoGroups(1, "sample.csv", 20);
        $this->assertCanChunkIntoGroups(1, "sample.csv", 15);
        $this->assertCanChunkIntoGroups(2, "sample.csv", 10);
        $this->assertCanChunkIntoGroups(3, "sample.csv", 5);
        $this->assertCanChunkIntoGroups(15, "sample.csv", 1);
    }

    public function testCanChunkMultipleSheets()
    {
        file_put_contents(__DIR__ . '/log.txt', '');
        file_put_contents(__DIR__ . '/rounds.txt', '');

        // test with small chunks
        $chunk_size      = 2;
        $expected        = "1,3,5,7,9,11,13,15,17,19,1,3,5,7,9,11,13,15,17,19";
        $expected_chunks = 10;

        // Sheet2 has more rows than sheet 1
        $this->excel->filter('chunk')
                    ->selectSheets('Sheet2')
                    ->load(__DIR__ . "/files/multi.xls")
                    ->chunk($chunk_size, function ($results) {
                        foreach ($results as $row) {
                            $output[] = (int)$row->header;
                        }

                        $previous = file_get_contents(__DIR__ . '/log.txt');
                        $previous = !empty($previous) ? $previous . ',' : $previous;
                        $rounds   = file_get_contents(__DIR__ . '/rounds.txt');

                        file_put_contents(__DIR__ . '/log.txt', $previous . implode(',', $output));
                        file_put_contents(__DIR__ . '/rounds.txt', $rounds . '+');
                    }, false);

        $output = file_get_contents(__DIR__ . '/log.txt');
        $rounds = strlen(file_get_contents(__DIR__ . '/rounds.txt'));

        $this->assertEquals($expected, $output, "Chunked ($chunk_size) value not equal with source data.");
        $this->assertEquals($expected_chunks, $rounds,
            "Expecting total chunks is $expected_chunks when chunk with size $chunk_size");
    }

    private function assertCanChunkIntoGroups($expected_chunks, $file, $chunk_size)
    {
        file_put_contents(__DIR__ . '/log.txt', '');
        file_put_contents(__DIR__ . '/rounds.txt', '');

        $this->excel->filter('chunk')->load(__DIR__ . "/files/{$file}")->chunk($chunk_size, function ($results) {

            foreach ($results as $row) {
                $output[] = (int)$row->header;
            }

            $previous = file_get_contents(__DIR__ . '/log.txt');
            $previous = !empty($previous) ? $previous . ',' : $previous;
            $rounds   = file_get_contents(__DIR__ . '/rounds.txt');

            file_put_contents(__DIR__ . '/log.txt', $previous . implode(',', $output));
            file_put_contents(__DIR__ . '/rounds.txt', $rounds . '+');
        }, false);

        $output   = file_get_contents(__DIR__ . '/log.txt');
        $rounds   = strlen(file_get_contents(__DIR__ . '/rounds.txt'));
        $expected = "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15";

        $this->assertEquals($expected, $output, "Chunked ($chunk_size) value not equal with source data.");
        $this->assertEquals($expected_chunks, $rounds,
            "Expecting total chunks is $expected_chunks when chunk with size $chunk_size");
    }
}
