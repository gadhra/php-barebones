<?php
    
    /**
      * GLOBAL function definitions, included from load
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
            $params = toArray( $params );
            extract( $params );
            include_once( $path );
            return true;
        }
        return false;
    }
    
    /**
      * taking advantage of PHP object handling with this hack
      * and I feel a bit bad about it
      */
    function toArray( $obj ) {
        if( is_array( $obj ) ) {
            return $obj;
        }
        
        if( is_object( $obj ) ) {
            return json_decode( json_encode( $obj ), true );
        }
        
        return [];
    }


    function import( $libs = [] ) {
        if( empty( $libs ) ) {
            return;
        }
        
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
