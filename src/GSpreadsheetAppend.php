<?php 

namespace Blazemedia\GDrive;

use Google;
use Google\Service\Sheets;

class GSpreadsheetAppend {

    private $service;  
    
    /// Service account credentials in JSON format
    const KEY_FILE_LOCATION = __DIR__ . '/google_credentials/ga_fetcher_composed-slice-349709-ed3cff527c69.json';

    function __construct( $credentials_path = '') {

        $credentials = $credentials_path != '' ? $credentials_path : GAReporting::KEY_FILE_LOCATION;

        

        $this->service = $this->init( $credentials );
    }


    /**
     * Create and configure a new client object 
     *
     * @param string $credentials
     * @return object
     */
    private function init( $credentials ) {

        $client = new Google\Client();
        $client->setAuthConfig( $credentials );
        $client->addScope( 'https://www.googleapis.com/auth/spreadsheets' );
        
        $spreadsheet = new Sheets( $client );

        return $spreadsheet;
    }


    function insertData( $spreadsheetId, $range = 'Sheet1!A:E', $dataRow ) {

        $rows = [$dataRow];
        $valueRange = new \Google_Service_Sheets_ValueRange();
        $valueRange->setValues($rows);
        $options = ['valueInputOption' => 'RAW']; // or USER_ENTERED
        $setdata = $this->service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);
        
        return $setdata;
    }


}