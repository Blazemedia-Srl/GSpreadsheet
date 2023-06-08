<?php

namespace src\Test;

use Bleazemedia\GDrive\GSpreadsheet;
use PHPUnit\Framework\TestCase;


final class GSpreadsheetTest extends TestCase
{
    public function testDownloadData(): void {

        $spreadsheet = new GSpreadsheet(__DIR__.'/../google_credentials/ga_fetcher_composed-slice-349709-ed3cff527c69.json');

        $values = $spreadsheet->getReport('19tR7IuyFft01dUIi2tyqRnIWe9KRdAC-5-mAqdmjScc');

        $records = $spreadsheet->getRecords( '19tR7IuyFft01dUIi2tyqRnIWe9KRdAC-5-mAqdmjScc', 'Sheet1!A:E', '2023-06-05', 'yesterday' );

        var_dump($records);

        $this->assertTrue(true);
    }
}