<?php

    define( 'SITEROOT', 'http://www.example.com' );
    define( 'FORCE_SSL', false );
    

    if( FORCE_SSL === true ) {
        if( empty( $_SERVER['HTTPS'] ) ) {
            $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: $redirect");
            exit;            
        }
    }
    
    if ( !defined('ABSPATH') ) {
        define('ABSPATH', dirname(__FILE__) . '/../');
    }

    if( file_exists( dirname(__FILE__) . '/.production' ) ) {
        define( 'ENVIRONMENT', 'PROD' );
    } elseif( file_exists( dirname(__FILE__) . '/.staging' ) ) {
        define( 'ENVIRONMENT', 'STAG' );
    } else {
        define( 'ENVIRONMENT', 'DEV' );
    }


    switch( ENVIRONMENT ) {
        case 'PROD':
            break;

        case 'STAG':
            break;

        case 'DEV':
            define( 'DB_NAME', 'test' );
            define( 'DB_HOST', 'mysql.example.com' );
            define( 'DB_USER', 'username' );
            define( 'DB_PASS', 'password' );
            break;


        default:
            print( "Invalid environment" );
            exit;
    }

    /**
     * Global Function Definitions
     */
        /**
     * include file
     * @param  string $fname  name of the file to route to
     * @param  array  $params any params you want to pass to the template
     * @return bool         include the file, return true if it works, false otherwise
     */
    function include_file( $fname, $params = [] ) {
        $path = sprintf( '%s%s/%s.php', ABSPATH, 'presenter', $fname );
        if( file_exists( $path ) ) {
            extract( $params );
            include_once( $path );
            return true;
        }
        return false;
    }


    function import( $libs = [] ) {
        if( empty( $libs ) ) {
            return;
        }

        foreach( $libs as $path ) {
            $path = sprintf( '%s%s/%s.php', ABSPATH, 'libs', $path );
            if( file_exists( $path ) ) {
                require_once( $path );
                return true;
            }

            die( 'Unable to load lib: ' . $path );
        }
    }