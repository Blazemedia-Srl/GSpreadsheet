<?php 

namespace Blazemedia\GDrive;

use Google;
use Google\Service\Sheets;

class GSpreadsheet {

    private $sheetService;  
    private $driveService;  
    private $client;   
    
    /// Service account credentials in JSON format
    const KEY_FILE_LOCATION = __DIR__ . '/google_credentials/ga_fetcher_composed-slice-349709-ed3cff527c69.json';

    function __construct( $credentials_path = '') {

        $credentials = $credentials_path != '' ? $credentials_path : GAReporting::KEY_FILE_LOCATION;

        $this->client = $this->setupClient($credentials);

        $this->sheetService = $this->setupSheetService();

        $this->driveService = $this->setupDriveService();

    }


    private function setupClient($credentials){
        $client = new Google\Client();
        $client->setAuthConfig( $credentials );

        $client->addScope('https://www.googleapis.com/auth/spreadsheets');
        $client->addScope('https://www.googleapis.com/auth/drive');

        return $client;
    }


    /**
     * Create and configure a new client object 
     *
     * @param string $credentials
     * @return object
     */
    private function setupSheetService() {
        $spreadsheet = new Sheets( $this->client );

        return $spreadsheet;
    }


    private function setupDriveService(){
        $service = new \Google_Service_Drive( $this->client );

        return $service;
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

            return $row;

        }, $values );

        return array_filter( $rows, fn( $row ) => $this->isDateIntoRange( $row[$dateField], $startDate, $endDate ) );
        
    }
    
    
    function isDateIntoRange( $date, $startDate, $endDate ) : bool {

        // Create the DateRange object.
        $range = (object) [ 
            'start' => date( 'Y-m-d', strtotime( $startDate ) ), 
            'end'   => date( 'Y-m-d', strtotime( $endDate   ) )
        ];

        return strtotime( $date ) >= strtotime( $range->start ) && strtotime( $date ) <= strtotime( $range->end );
    }


    public function checkOrCreate($spreadsheetTitle){
        if(empty($spreadsheetTitle)){
            return false;
        }
        
        $checkSpreadSheet = $this->checkExist($spreadsheetTitle);

        if(!$checkSpreadSheet){
            return $this->createSpreadSheet($spreadsheetTitle);
        }

        return $checkSpreadSheet;
    }


    protected function checkExist($spreadsheetTitle):array|false{
        $optParams = [
            'fields' => 'files(id, name)',
            'q' => "trashed=false"
        ];
        $results = $this->driveService->files->listFiles($optParams)->files;
        
        // Check if the spreadsheet exists
        foreach ($results as $file) {


            if ($file->name == $spreadsheetTitle) {
                // Spreadsheet exists, return its ID and title
                return ['id' => $file->id, 'title' => $file->name];
            }
        }
        return false;
    }


    protected function createSpreadSheet($spreadsheetTitle):array{
        $spreadsheet = new \Google_Service_Sheets_Spreadsheet([
            'properties' => [
                'title' => $spreadsheetTitle
            ]
        ]);

        $spreadsheet = $this->sheetService->spreadsheets->create($spreadsheet, [
            'fields' => 'spreadsheetId'
        ]);

        $this->setPermission('g.zerino@f2innovation.com','writer',$spreadsheet->spreadsheetId);
        return ['id' => $spreadsheet->spreadsheetId, 'title' => $spreadsheetTitle];
    }


    protected function setPermission($email,$role,$spreadsheetID){

        $newPermission = new \Google_Service_Drive_Permission();
        $newPermission->setType('user');
        $newPermission->setRole($role);
        $newPermission->setEmailAddress($email);
        
        return $this->driveService->permissions->create($spreadsheetID, $newPermission);
    }


    public function deleteByName($spreadsheetTitle):string {    
        // Print the names and IDs for up to 10 files.
        $optParams = array(
            'pageSize' => 10,
            'fields' => 'nextPageToken, files(id, name)'
        );
        $results = $this->driveService->files->listFiles($optParams)->files;
    
        // Check if the spreadsheet exists
        foreach ($results as $file) {
            if ($file->name == $spreadsheetTitle) {

                // Spreadsheet exists, delete it
                $this->driveService->files->delete($file->id);
                return "Spreadsheet deleted successfully";
            }
        }
    
        // If the code reaches here, it means the spreadsheet does not exist.
        return "Spreadsheet not found";
    }
}