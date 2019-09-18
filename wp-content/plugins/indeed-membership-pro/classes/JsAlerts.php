<?php
namespace Indeed\Ihc;

class JsAlerts
{
    private static $error       = '';
    private static $warning     = '';
    private static $info        = '';

    public function __construct()
    {
        self::sessionStart();
        add_action( 'the_content', [ $this, 'output' ], 9999, 1 );
    }

    private static function sessionStart()
    {
        if ( session_id() == '' ) {
            session_start();
        }
    }

    public static function setError( $text = '' )
    {
        self::sessionStart();
        $_SESSION["ihc"]["error"] = $text;
    }

    public static function setWarning( $text = '' )
    {
        self::sessionStart();
        $_SESSION["ihc"]["warning"] = $text;
    }

    public static function setInfo( $text = '' )
    {
        self::sessionStart();
        $_SESSION["ihc"]["info"] = $text;
    }

    public function output( $content = '' )
    {
        if ( !empty( $_SESSION["ihc"]['error'] ) ){
            $data['error'] = $_SESSION["ihc"]['error'];
            unset( $_SESSION["ihc"]['error'] );
        }
        if ( !empty( $_SESSION["ihc"]['warning'] ) ){
            $data['error'] = $_SESSION["ihc"]['warning'];
            unset( $_SESSION["ihc"]['warning'] );
        }
        if ( !empty( $_SESSION["ihc"]['info'] ) ){
            $data['error'] = $_SESSION["ihc"]['info'];
            unset( $_SESSION["ihc"]['info'] );
        }
        if ( empty( $data ) ){
            return $content;
        }
        $view = new \Indeed\Ihc\IndeedView();
        echo $view->setTemplate( IHC_PATH . 'public/views/js_alerts.php' )->setContentData( $data, true )->getOutput();
        return $content;
    }

}
