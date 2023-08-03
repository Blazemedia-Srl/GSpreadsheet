<?php

namespace src\Test;

require './vendor/autoload.php';



use Blazemedia\GDrive\GSpreadsheetAppend;
use PHPUnit\Framework\TestCase;


final class testGsAppendData extends TestCase
{
    public function testAppendData(): void {

        $spreadsheet = new GSpreadsheetAppend(__DIR__.'/../../google_credentials/ga_fetcher_composed-slice-349709-ed3cff527c69.json');

        $dataRow = [
            'test_gclid',
            'test_name',
            'test_time',
            'test_value'
        ];

        $spreadsheet->insertData('1RpkHTGA9OdCDmp7v8syntwjYCJ0bfMlSUBuOOG1ZqHU', 'conversions', $dataRow);
        
        $this->assertTrue(true);
    }
}