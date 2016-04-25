<?php
    require_once( ABSPATH . 'lib/Exceptions.php' );
    class MyDatabaseException extends CustomException {}
    
    class MySQL {
        
        private $conn = null;
        
        function _construct() {}
        
        
        
        public function run( $query, $values = [] ) {            
            try {
                return $this->execute( $query, $values );
            } catch( MyDatabaseException $e ) {
                return $e->handleError();
            }
        }
             
        
        public function fetch( $query, $values = [], $type = 'assoc' ) {
            try {
                $stmt = $this->execute( $query, $values );
            } catch( MyDatabaseException $e ) {
                return $e->handleError();
            }
            
            try {
                switch( $type ) {
                    case 'assoc':
                        return $stmt->fetchAll( PDO::FETCH_ASSOC );
                        break;
                    case 'single':
                        return $stmt->fetchAll( PDO::FETCH_ASSOC );
                        break;
                    default:
                        return $stmt->fetchAll( PDO::FETCH_BOTH );
                        break;
                } 
            } catch( MyDatabaseException $e ) {
                return $e->handleError();
            }
        }
        
        private function execute( $query, $values ) {
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
                $connStr = sprintf( 'mysql:host=%s;dbname=%s', DB_HOST, DB_NAME );
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