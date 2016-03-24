<?php
    /**
     * Main::
     * 
     * @author gadhra <iam@stefans.computer>
     * @package PHP-Barebones
    */
    
    $method = Flight::get( 'method' );
    $messages = [ 'success'=>[], 'error'=> [] ];
    $view = 'main.html';


    /**
     * View
     */
    view:
        import( [ 'Template' ] );
        $vars = [];
        $vars['messages'] = $messages;
        $vars['presenter'] = 'main';

        $tpl = new Template();
        echo $tpl->twig->render( $view, $vars );
        exit;