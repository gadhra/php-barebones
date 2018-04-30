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


    //@todo let all of this get handled by composer
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
            }
        }

        // load up the composer autoloader
        require_once( sprintf( '%s%s/vendor/autoload.php', ABSPATH, 'lib' ) );
        return;
    }
