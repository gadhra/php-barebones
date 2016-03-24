<?php
    
    require_once( ABSPATH . 'lib/flight/Flight.php' );
    
    function include_file( $fname, $params = [] ) {
        $path = sprintf( '%s%s/%s.php', ABSPATH, 'presenter', $fname );
        if( file_exists( $path ) ) {
            extract( $params );
            include_once( $path );
            return true;
        }
        return false;
    }    
    
    Flight::set( 'flight.log_errors', true );
        
    Flight::map('error', function( Exception $ex ){
        // Handle error
        if( ENVIRONMENT == 'DEV' ) {
            if(! empty( $ex->xdebug_message ) ) {
                echo '<pre>' . $ex->xdebug_message . '</pre>';
            } else {
                echo '<h2>' . $ex->getMessage() . ' ' . $ex->getFile() . ':' . $ex->getLine() . '</h2>';
                echo '<pre>' . $ex->getTraceAsString() . '</pre>';
                exit;
            }
        } else {
            syslog( LOG_NOTICE, $ex->getTraceAsString() );
        }
    });   

    Flight::route('DELETE /', function(){
        return true;
    });

    Flight::route('PUT /', function(){
        return true;
    });

    Flight::route('POST /', function(){
        return true;
    });

    Flight::route('GET /', function(){
        return true;
    });
    
    Flight::route( '*', function() {
        include_file( 'main' );
    });
    
    Flight::start();