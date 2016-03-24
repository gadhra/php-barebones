<?php
    /**
     * Main Router
     */
    import([ 'flight' ]);
    
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

    Flight::route( 'GET /@name(/@id)', function( $name, $id ) {
        if(! presenter( $name ) ) {
            return true;
        }
    });
    
    Flight::route( 'POST /@name', function( $name ) {
        if(! presenter( $name ) ) {
            return true;
        }
    });

    Flight::route( 'PUT /@name/@id', function( $name, $id ) {
        if(! presenter( $name ) ) { 
            return true;
        }
    });
    
    Flight::route( 'DELETE /@name/@id', function( $name, $id ) {
        if(! presenter( $name ) ) {
            return true;
        }
    });


    Flight::route( '*', function() {
        presenter( 'main' );
    });
    
    Flight::start();