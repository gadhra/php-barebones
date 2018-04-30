<?php
    
/**
 CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8_unicode_ci NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
*/


    class Session implements SessionHandlerInterface {
        private $conn;
        
        public function __construct( $cleanup = null ) {
            import( 'DB' );
            $this->conn = new MySQL;
        }
        

        public function open( $path, $name ) {
            return true;
        }
        
        public function close() {
            $this->conn = null;
            unset( $this->conn );
            return true;
        }
        
        public function read( $id ) {
            $sql = 'SELECT data FROM sessions WHERE id = ?';
            return $this->conn->fetchOne( $sql, [ $id ] );
        }
        
        public function write( $id, $data ) {
            $sql = 'REPLACE INTO sessions (id, data, ts) VALUES (?,?,?)';
            $this->conn->run( $sql, [ $id, $data, time() ] );
            return true;
        }
        
        public function destroy( $id ) {
            $sql = 'DELETE FROM sessions WHERE id = ?';
            $this->conn->run( $sql, [ $id ] );
            return true;
        }
        
        
        public function gc( $lifetime ) {
            $ts = time() - lifetime;
            $sql = 'DELETE FROM sessions WHERE ts < ?';
            $this->conn->run( $sql, [ $ts ] );
            return true;
        }
    }
    
    session_set_save_handler( new Session, true );
    session_start();