<?php

namespace src\Test;

require './vendor/autoload.php';



use Blazemedia\GDrive\GSpreadsheetAppend;
use PHPUnit\Framework\TestCase;


final class testGsAppendData extends TestCase
{
    public function testAppendData(): void {

        $spreadsheet = new GSpreadsheetAppend(__DIR__.'/../../google_credentials/ga_fetcher_composed-slice-349709-ed3cff527c69.json');

        $dataRows = [
            [
                'test_gclid',
                'test_name' ,
                'test_time' ,
                'test_value'
            ],
            [
                'test_gclid_2',
                'test_name_2' ,
                'test_time_2' ,
                'test_value_2'
            ]
        ];

        foreach($dataRows as $dataRow) {
            $spreadsheet->insertData('1RpkHTGA9OdCDmp7v8syntwjYCJ0bfMlSUBuOOG1ZqHU', 'conversions', $dataRow);
        }

        $this->assertTrue(true);
    }
}