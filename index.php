<?php
    /**
     * Main Router
     */
    import([ 'flight' ]);
    
    Flight::set( 'flight.log_errors', true );
    
    Flight::map('error', function( Throwable $ex ){
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


    Flight::route( 'GET /phpinfo', function() {
        if( ENVIRONMENT != 'DEV' ) {
            return true;
        }
        phpinfo();
    });

    Flight::route( 'GET /@name(/@id)', function( $name, $id ) {
        Flight::set( 'method', 'GET' );
        $data = Flight::request()->query;
        
        $params = [];
        foreach( $data as $k=>$v ) {
            $params[filter_var($k,FILTER_SANITIZE_STRING)] = filter_var($v,FILTER_SANITIZE_STRING);
        }
        
        /**
          * Override the id if passed RESTfully
          */
        if(! empty( $id ) ) {
            $params['id'] = filter_var($id,FILTER_SANITIZE_STRING);
        }


        if(! presenter( $name, $params ) ) {
            return true;
        }
    });
    
    Flight::route( 'POST /@name', function( $name ) {
        Flight::set( 'method', 'POST' );
        $data = Flight::request()->data;
        
        $params = [];
        foreach( $data as $k=>$v ) {
            $params[filter_var($k,FILTER_SANITIZE_STRING)] = filter_var($v,FILTER_SANITIZE_STRING);
        }
        
        if(! presenter( $name, $params ) ) {
            return true;
        }
    });

    Flight::route( 'PUT /@name/@id', function( $name, $id ) {
        Flight::set( 'method', 'PUT' );
        $data = Flight::request()->getBody();
        
        $params = [];
        foreach( $data as $k=>$v ) {
            $params[filter_var($k,FILTER_SANITIZE_STRING)] = filter_var($v,FILTER_SANITIZE_STRING);    
        }
        
        if(! presenter( $name, $params ) ) { 
            return true;
        }
    });
    
    Flight::route( 'DELETE /@name/@id', function( $name, $id ) {
        Flight::set( 'method', 'DELETE' );
        $data = Flight::request()->getBody();
        
        $params = [];
        foreach( $data as $k=>$v ) {
            $params[filter_var($k,FILTER_SANITIZE_STRING)] = filter_var($v,FILTER_SANITIZE_STRING);    
        }

        /**
          * Override the id if passed RESTfully
          */
        if(! empty( $id ) ) {
            $params['id'] = filter_var($id,FILTER_SANITIZE_STRING);
        }        
             
        if(! presenter( $name, $params ) ) {
            return true;
        }
    });


    Flight::route( '*', function() {
        presenter( 'main' );
    });
    


