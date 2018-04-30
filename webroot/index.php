<?php

if( version_compare( phpversion(), '7.0', '<' ) ) {
    die( 'Minimum version to run this application: PHP 7.0' );
}
require_once( dirname(__FILE__) . '/../config/load.php' );
require_once( dirname(__FILE__). '/../index.php' );
