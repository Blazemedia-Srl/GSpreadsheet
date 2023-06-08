<?php 

namespace App;

use Google;
use Google\Service\AnalyticsReporting;
use Google\Service\Sheets;

class GSpreadsheet {

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



    function getReport( $spreadsheetId, $range = 'Sheet1!A:E', $start_date = '7daysAgo', $end_date = 'yesterday' ) {

        $response = $this->service->spreadsheets_values->get($spreadsheetId, $range);
        
        $values = $response->getValues();


        // Create the DateRange object.
        $dateRange = (object) [ 
            'start' => date( 'Y-m-d', strtotime( $start_date ) ), 
            'end'   => date( 'Y-m-d', strtotime( $end_date   ) )
        ];

        var_dump($values); die;
              
        return $values;
    }
    
    
}