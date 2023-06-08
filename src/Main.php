<?php 

namespace App;

use Google;
use App\GAReporting;


class Main {

    function __construct() {


        //$response = $GA->getReport('UA-215879426-2');
        
       


        $gaReporting = new GAReporting();
        $response = $gaReporting->getReport('258391480');  /// <<-- qui gli va passata la vista
        $gaReporting->printResults($response);


        

    }


}