<?php
    /**
     * Main Router
     */
    import([ 'Flight' ]);
    
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
        presenter( 'main' );
    });
    
    Flight::start();