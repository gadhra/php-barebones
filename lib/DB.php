<?php
    require_once( ABSPATH . 'lib/Exceptions.php' );
    class MyDatabaseException extends CustomException {}
    
    class MySQL {
        
        private $conn = null;
        
        function _construct() {}
        
        
        
        public function run( $query, $values = [], $debug = false ) {            
            try {
                return $this->execute( $query, $values, $debug );
            } catch( MyDatabaseException $e ) {
                return $e->handleError();
            }
        }
        
        /**
          * Syntactic Sugar methods
          */
        
        // fetch the contents of a single field
        public function fetchOne( $query, $values = [], $debug = false ) {
            return $this->fetch( $query, $values, 'single', $debug );
        }
        
        //fetch one row
        public function fetchRow( $query, $values = [], $debug=false ) {
            return $this->fetch( $query, $values, 'row', $debug );
            
        }
        
        //fetch all records, optionally index them by a best 
        // guess at the primary key
        public function fetchAll( $query, $values = [], $index = false, $debug=false ) {
            if(! $index ) {
                return $this->fetch( $query, $values, $debug );
            }
            $id = $this->getIndex( $query );
            if( empty( $id ) ) {
                $id = 'id';
            }
            
            $data = $this->fetch( $query, $values, $debug );
            $response = [];
            foreach( $data as $array ) {
                $response[$array[$id]] = $array;   
            }
            
            return $response;
        }
        
        /**
         * Sometimes I just want to look at the raw query being passed to
         * MySQL
         * @param $query - query to look at
         * @param $values - values I'm substituting
         */
        public function debug( $query, $values ) {
            foreach ($values as $k => $v) {
                if( is_string( $v ) ) {
                    $v = "'$v'";
                }
                $query = preg_replace('/\?/', $v, $query, 1 );
            }

            return $query;
        }
        
        
        
        private function getIndex( $query ) {
            preg_match( '/from\s(.*?)\s/is', strtolower( $query ), $matches );
            if( empty( $matches[1] ) ) {
                return false;
            }
            
            $query = "SELECT `COLUMN_NAME`
                        FROM `information_schema`.`COLUMNS`
                        WHERE (`TABLE_SCHEMA` = ?)
                        AND (`TABLE_NAME` = ?)
                        AND (`COLUMN_KEY` = ? )";
            
            return $this->fetch( $query, [ DB_NAME, $matches[1], 'PRI' ], 'single' );
        }
        
        private function fetch( $query, $values = [], $type = 'all', $debug = false ) {
            try {
                $stmt = $this->execute( $query, $values, $debug );
            } catch( MyDatabaseException $e ) {
                return $e->handleError();
            }
            
            try {
                switch( $type ) {
                    case 'all':
                        return $stmt->fetchAll( PDO::FETCH_ASSOC );
                        break;
                        
                    case 'single':
                        $result = $stmt->fetchAll( PDO::FETCH_NUM );
                        if( empty( $result[0][0] ) ) {
                            return null;
                        }
                        
                        return $result[0][0];
                        break;
                    case 'row':
                        $result = $stmt->fetchAll( PDO::FETCH_ASSOC );
                        if( empty( $result[0] ) ) {
                            return [];
                        }
                        return $result[0];
                        
                    default:
                        return $stmt->fetchAll( PDO::FETCH_BOTH );
                        break;
                } 
            } catch( MyDatabaseException $e ) {
                return $e->handleError();
            }
        }
        
        private function execute( $query, $values, $debug = false ) {
            if( $debug === true ) {
                echo $this->debug( $query, $values );
            }
        
            try {
                $stmt = $this->prep( $query );
            } catch ( MyDatabaseException $e ) {
                throw new MyDatabaseException( $e->getMessage(), ( int ) $e->getCode() );
            }
            
            $stmt->execute( $values );
            return $stmt;     
        }
        
        
        
        private function connect() {
            if(! is_resource( $this->conn ) ) {
                $host = explode( ':', DB_HOST );
                $connStr = sprintf( 'mysql:host=%s;dbname=%s', $host[0], DB_NAME );
                if(! empty( $host[1] ) ) {
                    $connStr .= sprintf( ';port=%s', $host[1] );
                }
		
		        try {
		            $this->conn = new PDO( $connStr, DB_USER, DB_PASS,
                        [
                            PDO::ATTR_EMULATE_PREPARES => false,
                            PDO::ATTR_PERSISTENT => false
                        ]);
                } catch (PDOException $e) {
                    throw new MyDatabaseException( $e->getMessage(), ( int ) $e->getCode() );
                }
            }
            return $this->conn;
        }
        
        
        
        /**
         * Prepare a mysql statement, throw exception when invalid
         * @param  string $query    query to prepare
         * @return resource           prepared PDO sql statement
         * @see  lib/exceptionClasses.php
         */
        private function prep( $query ) {
            try {
                $conn = $this->connect();
                $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                return $conn->prepare( $query );
            } catch( PDOException $e ) {
                throw new MyDatabaseException( $e->getMessage(), (int) $e->getCode() );
            }
        }
    }
