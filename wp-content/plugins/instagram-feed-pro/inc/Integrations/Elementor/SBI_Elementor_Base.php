<?php

namespace InstagramFeed\Integrations\Elementor;

use InstagramFeed\Builder\SBI_Feed_Builder;

class SBI_Elementor_Base{
	const VERSION = SBIVER;
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
	const MINIMUM_PHP_VERSION = '5.6';
    private static $instance;
    const NAME_SPACE = 'InstagramFeed.Integrations.Elementor.';


	public static function instance() {
		if ( !isset( self::$instance ) && !self::$instance instanceof SBI_Elementor_Base ) {
			self::$instance = new SBI_Elementor_Base();
            self::$instance->apply_hooks();
		}
		return self::$instance;
	}
	 private function apply_hooks(){
        add_action( 'elementor/frontend/after_register_scripts', [$this, 'register_frontend_scripts'] );
        add_action( 'elementor/frontend/after_register_styles', [$this, 'register_frontend_styles'], 10 );
        add_action( 'elementor/frontend/after_enqueue_styles', [$this, 'enqueue_frontend_styles'], 10 );
        add_action( 'elementor/controls/controls_registered', [$this, 'register_controls']);
        add_action( 'elementor/widgets/widgets_registered', [$this,'register_widgets']);
        add_action( 'elementor/init', [$this, 'add_smashballon_categories']);

    }

    public function register_controls() {
        $controls_manager = \Elementor\Plugin::$instance->controls_manager;
        $controls_manager->register_control('sbi_feed_control', new SBI_Feed_Elementor_Control());

    }


	public function register_widgets() {
        $instance_manager = \Elementor\Plugin::instance()->widgets_manager;
        $instance_manager->register_widget_type(new SBI_Elementor_Widget());

        $installed_plugins = SBI_Feed_Builder::get_smashballoon_plugins_info();
        unset($installed_plugins['instagram']);

        foreach ($installed_plugins as $plugin) {
            if( !$plugin['installed'] ){
                $plugin_class = str_replace('.','\\', self::NAME_SPACE) . $plugin['class'];
                $instance_manager->register_widget_type( new $plugin_class() );
            }
        }

    }


    public function register_frontend_scripts(){
        $upload = wp_upload_dir();
        $resized_url = trailingslashit( $upload['baseurl'] ) . trailingslashit( SBI_UPLOADS_NAME );

        $js_options = array(
            'font_method' => 'svg',
            'placeholder' => trailingslashit( SBI_PLUGIN_URL ) . 'img/placeholder.png',
            'resized_url' => $resized_url,
            'ajax_url'  => admin_url( 'admin-ajax.php' ),
        );

    	wp_register_script(
			'sbiscripts',
			SBI_PLUGIN_URL . 'js/sbi-scripts.min.js' ,
			array('jquery'),
			SBIVER,
			true
		);
		wp_localize_script( 'sbiscripts', 'sb_instagram_js_options', $js_options );

        $data_handler = array(
            'smashPlugins' => SBI_Feed_Builder::get_smashballoon_plugins_info(),
            'nonce'         => wp_create_nonce( 'sbi-admin' ),
            'ajax_handler'      =>  admin_url( 'admin-ajax.php' ),
        );

        wp_register_script(
            'elementor-handler',
            SBI_PLUGIN_URL . 'admin/assets/js/elementor-handler.js' ,
            array('jquery'),
            SBIVER,
            true
        );

        wp_localize_script( 'elementor-handler', 'sbHandler', $data_handler );


        wp_register_script(
            'elementor-preview',
            SBI_PLUGIN_URL . 'admin/assets/js/elementor-preview.js' ,
            array('jquery'),
            SBIVER,
            true
        );
    }

    public function register_frontend_styles(){
        wp_register_style(
        	'sbistyles',
			SBI_PLUGIN_URL . 'css/sbi-style.min.css' ,
			array(),
			SBIVER
        );
    }

    public function enqueue_frontend_styles(){
        wp_enqueue_style( 'sbistyles' );
    }

     public function add_smashballon_categories() {
        \Elementor\Plugin::instance()->elements_manager->add_category('smash-balloon',[
            'title' => __( 'Smash Balloon', 'instagram-feed' ),
            'icon' => 'fa fa-plug',
        ]);
    }

}

