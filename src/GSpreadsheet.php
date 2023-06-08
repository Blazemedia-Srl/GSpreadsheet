<?php 

namespace Blazemedia\GDrive;

use Google;
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



    function getReport( $spreadsheetId, $range = 'Sheet1!A:E' ) {

        $response = $this->service->spreadsheets_values->get($spreadsheetId, $range);
        
        return $response->getValues();
    }

    function currencyToFloat( string $currency ) : float {

        $currency = str_replace(',','.', str_replace( '.', '', $currency ));

        $currency = preg_replace('/.*?(\d+\.\d+|\d+).*$/', '$1', $currency );

        return floatval( $currency );
        
    }


    function getRecords( $spreadsheetId, $range = 'Sheet1!A:E', $startDate = '7daysAgo', $endDate = 'yesterday' ) {

        $values = $this->getReport( $spreadsheetId, $range );

        $fields = array_shift( $values );

        $dateField     = 'Data';
        $earningsField = 'Commissioni';
        $salesField    = 'Transato';
        
        $rows = array_map( function( $item ) use ( $fields, $dateField, $earningsField, $salesField ) {

            $row = [];

            foreach( $fields as $index => $field ) {

                switch( $field ) {

                    case $dateField: 

                        $date_ymd = implode( '-', array_reverse( explode('/', $item[$index] ) ) );

                        $row[ $field ] = date('Y-m-d', strtotime( $date_ymd ) );
                        break;

                    case $earningsField:
                    case $salesField:

                        $row[ $field ] = $this->currencyToFloat( $item[ $index ]);
                        break;

                    default:
                        $row[ $field ] = $item[ $index ];
                }
            }

            return (object) $row;

        }, $values );

        return array_filter( $rows, fn( $row ) => $this->isDateIntoRange( $row->$dateField, $startDate, $endDate ) );
        
    }
    
    
    function isDateIntoRange( $date, $startDate, $endDate ) : bool {

        // Create the DateRange object.
        $range = (object) [ 
            'start' => date( 'Y-m-d', strtotime( $startDate ) ), 
            'end'   => date( 'Y-m-d', strtotime( $endDate   ) )
        ];

        return strtotime( $date ) >= strtotime( $range->start ) && strtotime( $date ) <= strtotime( $range->end );
    }
    
}