<?php

namespace src\Test;

use App\GSpreadsheet;
use PHPUnit\Framework\TestCase;


final class GspreadsheetTest extends TestCase
{
    public function testDownloadData(): void
    {
        $spreadsheet = new GSpreadsheet(__DIR__.'/google_credentials/ga_fetcher_composed-slice-349709-ed3cff527c69.json');

        $greeting = $greeter->greet('Alice');

        $this->assertSame('Hello, Alice!', $greeting);
    }
}