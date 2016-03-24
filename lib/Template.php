<?php

    import( [ 'Template/libs/Autoloader' ] );


    class Template {
        public $twig;

        function __construct( $options = [] ) {
            $opts = [
                'cache'=> ABSPATH . 'cache',
                'auto_reload'=> true,
                'debug'=>true
            ];

            $loader = new Twig_Loader_Filesystem( ABSPATH . 'view' );
            $this->twig = new Twig_Environment( $loader, $opts );
            return $this;
        }


    }