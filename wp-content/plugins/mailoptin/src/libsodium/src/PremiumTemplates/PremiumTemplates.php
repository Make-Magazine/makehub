<?php

namespace MailOptin\Libsodium\PremiumTemplates;

use MailOptin\Core\Repositories\OptinThemesRepository;

if (strpos(__FILE__, 'mailoptin' . DIRECTORY_SEPARATOR . 'src') !== false) {
    // production url path to assets folder.
    define('MAILOPTIN_PREMIUMTEMPLATES_ASSETS_URL', MAILOPTIN_URL . 'src/libsodium/src/PremiumTemplates/assets/');
} else {
    // dev url path to assets folder.
    define('MAILOPTIN_PREMIUMTEMPLATES_ASSETS_URL', MAILOPTIN_URL . '../' . dirname(substr(__FILE__, strpos(__FILE__, 'mailoptin'))) . '/assets/');
}

class PremiumTemplates
{
    public function __construct()
    {
        add_filter('mailoptin_register_optin_class', [$this, 'register_optin_premium_classes'], 10, 4);
    }

    /**
     * Register premium optin themes classes for instantiation by OptinFormFactory.
     *
     * @param $optin_class
     * @param $optin_campaign_id
     * @param $db_optin_class
     * @param $optin_type
     *
     * @return string
     */
    public function register_optin_premium_classes($optin_class, $optin_campaign_id, $db_optin_class, $optin_type)
    {
        foreach (OptinThemesRepository::premium_themes() as $premium_optin_theme) {
            $optin_theme_type  = ucfirst($premium_optin_theme['optin_type']);
            $optin_theme_class = $premium_optin_theme['optin_class'];

            if ($db_optin_class === $optin_theme_class && $optin_type === $optin_theme_type) {
                $optin_class = "\\MailOptin\\Libsodium\\PremiumTemplates\\OptinForms\\$optin_type\\$db_optin_class";
            }
        }

        return $optin_class;
    }


    /**
     * Singleton poop.
     *
     * @return PremiumTemplates|null
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}