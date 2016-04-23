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
     * include presenter file
     * @param  string $fname  name of the file to route to
     * @param  array  $params any params you want to pass to the template
     * @return bool         include the file, return true if it works, false otherwise
     */
    function presenter( $fname, $params = [] ) {
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

        /**
          * little bit of sugar in case you pass in a string
          */
        if( is_string( $libs ) ) {
            $tmp = $libs;
            $libs = [];
            $libs[] = $tmp;
            unset( $tmp );
        }

        foreach( $libs as $lib ) {
            $path = sprintf( '%s%s/%s.php', ABSPATH, 'lib', $lib );
            //check local first
            if( file_exists( $path ) ) {
                require_once( $path );
                return $path;
            }
            
            // check for the vendor file
            $path = sprintf( '%s%s/vendor/%s.php', ABSPATH, 'lib', $lib );
            if( file_exists( $path ) ) {
                require_once( $path );
                return $path;
            }

            //check for a directory, and check for an autoloader first
            $path = sprintf( '%s%s/vendor/%s', ABSPATH, 'lib', $lib );
            if( is_dir( $path ) ) {
                $files = glob( $path . '/*', GLOB_NOSORT );
                foreach( $files as $file ) {
                    $fname = basename( $file );
                    if( strtolower( $fname ) === 'autoload.php' ) {
                        require_once( $file );
                        return $file;
                    }
                    if( strtolower( $fname ) === 'autoloader.php' ) {
                        require_once( $file );
                        return $file;
                    }
                    if( strtolower( $fname ) === sprintf( "%s.php", strtolower( $lib ) ) ) {
                        require_once( $file );
                        return $file;
                    }
                }
            }

            die( 'Unable to load lib: ' . $lib );
        }
    }