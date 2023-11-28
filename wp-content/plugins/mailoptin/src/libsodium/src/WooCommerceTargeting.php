<?php

namespace MailOptin\Libsodium;


use MailOptin\Core\Admin\Customizer\CustomControls\ControlsHelpers;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Chosen_Select_Control;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Custom_Content;
use MailOptin\Core\Admin\Customizer\CustomControls\WP_Customize_Toggle_Control;
use MailOptin\Core\Admin\Customizer\OptinForm\AbstractCustomizer;
use MailOptin\Core\Admin\Customizer\OptinForm\Customizer;
use MailOptin\Core\Admin\Customizer\OptinForm\CustomizerSettings;
use MailOptin\Core\OptinForms\AbstractOptinForm;
use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;

class WooCommerceTargeting
{
    public $customizer_section_id = 'mo_wp_woocommerce_display_rule_section';
    public $customizer_wc_conditions_section_id = 'mo_wp_woocommerce_wc_conditions_section';
    public $customizer_atc_section_id = 'mo_wp_woocommerce_atc_display_rule_section';

    public $cart_basket = false;

    public function __construct()
    {
        add_action('woocommerce_loaded', function () {
            add_filter('mo_optin_customizer_sections_ids', [$this, 'active_sections'], 10, 2);
            add_action('mo_optin_after_page_user_targeting_display_rule_section', [$this, 'woocommerce_section'], 2, 2);

            add_filter('mo_optin_form_customizer_output_settings', [$this, 'woocommerce_settings'], 10, 2);
            add_action('mo_optin_after_customizer_controls', [$this, 'woocommerce_controls'], 10, 4);
            add_action('mo_optin_after_customizer_controls', [$this, 'woocommerce_conditions_controls'], 10, 4);

            add_filter('mailoptin_page_targeting_optin_rule', [$this, 'page_targeting_rule'], 10, 2);
            // we need to run this check very late so that if any wc condition is false optin won't show up
            add_filter('mailoptin_page_targeting_optin_rule_short_circuit', [$this, 'wc_conditions_targeting_rule'], PHP_INT_MAX - 1, 3);

            add_filter('mo_optin_js_config', [$this, 'optin_js_config'], 10, 2);

            add_filter('mo_mailoptin_js_globals', [$this, 'global_vars']);
            add_filter('wc_add_to_cart_message_html', [$this, 'wc_add_to_cart_message_html'], 10, 2);
        });
    }

    /**
     * @param $data
     * @param AbstractOptinForm $abstractOptinFormClass
     *
     * @return mixed
     */
    public function optin_js_config($data, $abstractOptinFormClass)
    {
        $atc_status        = $abstractOptinFormClass->get_customizer_value('wc_atc_activate_rule');
        $required_products = $abstractOptinFormClass->get_customizer_value('wc_atc_products');

        $data['wc_atc_activate_rule'] = $atc_status;

        if ($atc_status === true && ! empty($required_products)) {
            $data['wc_atc_products'] = array_map('absint', $required_products);
        }

        return $data;
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     * @param Customizer $customizerClassInstance
     */
    public function woocommerce_section($wp_customize, $customizerClassInstance)
    {
        $wp_customize->add_section($this->customizer_section_id, array(
                'title' => esc_html__("Woocommerce Page Targeting", 'mailoptin'),
                'panel' => $customizerClassInstance->display_rules_panel_id
            )
        );

        $wp_customize->add_section($this->customizer_wc_conditions_section_id, array(
                'title' => esc_html__("Woocommerce Conditions", 'mailoptin'),
                'panel' => $customizerClassInstance->display_rules_panel_id
            )
        );

        if (in_array($customizerClassInstance->optin_campaign_type, ['lightbox', 'slidein', 'bar'])) {

            $wp_customize->add_section($this->customizer_atc_section_id, array(
                    'title' => esc_html__("Woocommerce Added to Cart", 'mailoptin'),
                    'panel' => $customizerClassInstance->display_rules_panel_id
                )
            );
        }
    }

    public function active_sections($sections)
    {
        $sections[] = $this->customizer_section_id;
        $sections[] = $this->customizer_wc_conditions_section_id;
        $sections[] = $this->customizer_atc_section_id;

        return $sections;
    }

    public function page_targeting_rule($status, $id)
    {
        $checks = [
            'woocommerce_show_specific_woo_products'   => [],
            'woocommerce_show_specific_categories'     => [],
            'woocommerce_show_specific_tags'           => [],
            'woocommerce_show_all_woo_pages'           => ['is_woocommerce'],
            'woocommerce_show_woo_shop'                => ['is_shop'],
            'woocommerce_show_woo_products'            => ['is_product'],
            'woocommerce_show_cart_page'               => ['is_cart'],
            'woocommerce_show_checkout_page'           => ['is_checkout'],
            'woocommerce_show_account_pages'           => ['is_account_page'],
            'woocommerce_show_all_endpoints'           => ['is_wc_endpoint_url'],
            'woocommerce_show_order_pay_endpoint'      => ['is_wc_endpoint_url', 'order-pay'],
            'woocommerce_show_order_received_endpoint' => ['is_wc_endpoint_url', 'order-received'],
            'woocommerce_show_view_order_endpoint'     => ['is_wc_endpoint_url', 'view-order']
        ];

        foreach ($checks as $field => $callback) {
            $value = OCR::get_customizer_value($id, $field);

            if (empty($value)) continue;

            if ($field == 'woocommerce_show_specific_woo_products') {
                if (is_product() && is_array($value)) {
                    $product_id = wc_get_product()->get_id();
                    if (in_array($product_id, $value)) return true;
                }
                continue;
            }

            if ($field == 'woocommerce_show_specific_categories') {
                if (is_product() && is_array($value)) {
                    $post_categories = wc_get_product()->get_category_ids();
                    $intersect       = array_intersect($post_categories, $value);
                    if ( ! empty($intersect)) return true;
                }
                continue;
            }

            if ($field == 'woocommerce_show_specific_tags') {
                if (is_product() && is_array($value)) {
                    $post_tags = wc_get_product()->get_tag_ids();
                    $intersect = array_intersect($post_tags, $value);
                    if ( ! empty($intersect)) return true;
                }
                continue;
            }

            if (call_user_func_array(array_shift($callback), $callback)) return true;
        }

        return $status;
    }

    public function calc_evaluation($value, $more_than, $less_than)
    {
        if ($more_than && ! $less_than) {

            if (($value > $more_than) === false) return false;
        }

        if ($less_than && ! $more_than) {

            if (($value < $less_than) === false) return false;
        }

        if ($less_than && $more_than) {

            if ( ! ($value > $more_than) || ! ($value < $less_than)) return false;
        }

        return true;
    }

    public function wc_conditions_targeting_rule($status, $id)
    {
        /* Customer has purchased */
        $chp_products = OCR::get_customizer_value($id, 'customer_has_purchased_products', []);

        if (is_array($chp_products) && ! empty($chp_products)) {

            if (is_user_logged_in()) {

                $current_user = wp_get_current_user();

                $chp_require_all = OCR::get_customizer_value($id, 'customer_has_purchased_require_all', false);

                $purchased = [];

                foreach ($chp_products as $product_id) {
                    $purchased[$product_id] = wc_customer_bought_product($current_user->user_email, $current_user->ID, $product_id);
                }

                if ( ! $chp_require_all && ! in_array(true, $purchased)) return false;

                if ($chp_require_all && in_array(false, $purchased)) return false;
            }
        }

        /* Products in cart */
        $pic_products      = OCR::get_customizer_value($id, 'product_in_cart_products', []);
        $pic_require_all   = OCR::get_customizer_value($id, 'product_in_cart_require_all', false);
        $pic_found_in_cart = [];

        if (is_array($pic_products) && ! empty($pic_products)) {

            foreach ($pic_products as $product_id) {

                $pic_found_in_cart_status = WC()->cart->find_product_in_cart(
                    WC()->cart->generate_cart_id($product_id)
                );

                $pic_found_in_cart[$product_id] = ! empty($pic_found_in_cart_status);
            }

            if ( ! $pic_require_all && ! in_array(true, $pic_found_in_cart)) return false;

            if ($pic_require_all && in_array(false, $pic_found_in_cart)) return false;
        }

        // cart item count
        $cic_more_than = absint(OCR::get_customizer_value($id, 'cart_item_count_more_than', 0));
        $cic_less_than = absint(OCR::get_customizer_value($id, 'cart_item_count_less_than', 0));

        if ($cic_more_than > 0 || $cic_less_than > 0) {

            $cart_count = WC()->cart->get_cart_contents_count();

            if ( ! $this->calc_evaluation($cart_count, $cic_more_than, $cic_less_than)) return false;
        }

        // cart total
        $ct_total_type = absint(OCR::get_customizer_value($id, 'cart_total_type', 'total'));
        $ct_more_than  = absint(OCR::get_customizer_value($id, 'cart_total_more_than', 0));
        $ct_less_than  = absint(OCR::get_customizer_value($id, 'cart_total_less_than', 0));

        if ($ct_more_than > 0 || $ct_less_than > 0) {

            $cart = WC()->cart;

            $ct_total = $cart->get_total('edit');

            if ( ! empty($ct_total_type) && 'total' != $ct_total_type) {

                if (is_object($cart) && method_exists($cart, 'get_' . $ct_total_type)) {
                    $ct_total = call_user_func([$cart, 'get_' . $ct_total_type]);
                }
            }

            if ( ! $this->calc_evaluation($ct_total, $ct_more_than, $ct_less_than)) return false;
        }

        // customer spent
        $cs_more_than = absint(OCR::get_customizer_value($id, 'customer_spent_more_than', 0));
        $cs_less_than = absint(OCR::get_customizer_value($id, 'customer_spent_less_than', 0));

        if (is_user_logged_in() && ($cs_more_than > 0 || $cs_less_than > 0)) {

            $cs_spent = wc_get_customer_total_spent(get_current_user_id());

            if ( ! $this->calc_evaluation($cs_spent, $cs_more_than, $cs_less_than)) return false;
        }

        return $status;
    }

    /**
     * @param $settings
     * @param CustomizerSettings $customizerSettings
     *
     * @return mixed
     */
    public function woocommerce_settings($settings, $customizerSettings)
    {
        $control_settings = [
            'product_in_cart_header',
            'product_in_cart_products',
            'product_in_cart_require_all',

            'customer_has_purchased_header',
            'customer_has_purchased_products',
            'customer_has_purchased_require_all',
            'cart_item_count_header',
            'cart_total_header',
            'customer_spent_header',

            'wc_atc_activate_rule',
            'wc_atc_products',
            'woocommerce_show_all_woo_pages',
            'woocommerce_show_woo_shop',
            'woocommerce_show_woo_products',
            'woocommerce_show_specific_woo_products',
            'woocommerce_show_cart_page',
            'woocommerce_show_checkout_page',
            'woocommerce_show_account_pages',
            'woocommerce_show_all_endpoints',
            'woocommerce_show_order_pay_endpoint',
            'woocommerce_show_specific_categories',
            'woocommerce_show_specific_tags',
            'woocommerce_show_order_received_endpoint',
            'woocommerce_show_view_order_endpoint'
        ];

        $settings['cart_item_count_more_than'] = array(
            'default'   => '',
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        $settings['cart_item_count_less_than'] = $settings['cart_item_count_more_than'];

        $settings['cart_total_more_than'] = $settings['cart_item_count_more_than'];
        $settings['cart_total_less_than'] = $settings['cart_item_count_more_than'];

        $settings['customer_spent_more_than'] = $settings['cart_item_count_more_than'];
        $settings['customer_spent_less_than'] = $settings['cart_item_count_more_than'];

        $settings['cart_total_type'] = array(
            'default'   => 'total',
            'type'      => 'option',
            'transport' => 'postMessage',
        );

        foreach ($control_settings as $control_setting) {
            $settings[$control_setting] = array(
                'default'   => false,
                'type'      => 'option',
                'transport' => 'postMessage',
            );
        }

        return $settings;
    }

    /**
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function woocommerce_conditions_controls($instance, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        $woocommerce_control_args = apply_filters(
            "mo_optin_form_customizer_woocommerce_conditions_controls",
            array(
                'customer_has_purchased_header'      => new WP_Customize_Custom_Content(
                    $wp_customize,
                    $option_prefix . '[customer_has_purchased_header]',
                    [
                        'content'     => '<div class="mo-field-header">' . __("Customer Has Purchased", 'mailoptin') . '</div>',
                        'block_class' => 'mo-field-header-wrapper',
                        'section'     => $this->customizer_wc_conditions_section_id,
                        'settings'    => $option_prefix . '[customer_has_purchased_header]',
                        'priority'    => 2,
                    ]
                ),
                'customer_has_purchased_products'    => new WP_Customize_Chosen_Select_Control(
                    $wp_customize,
                    $option_prefix . '[customer_has_purchased_products]',
                    array(
                        'label'       => __('Select WooCommerce products:'),
                        'section'     => $this->customizer_wc_conditions_section_id,
                        'settings'    => $option_prefix . '[customer_has_purchased_products]',
                        'search_type' => 'woocommerce_products',
                        'choices'     => ControlsHelpers::get_post_type_posts('product'),
                        'priority'    => 4
                    )
                ),
                'customer_has_purchased_require_all' => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[customer_has_purchased_require_all]',
                    array(
                        'label'       => __('Require all', 'mailoptin'),
                        'section'     => $this->customizer_wc_conditions_section_id,
                        'settings'    => $option_prefix . '[customer_has_purchased_require_all]',
                        'description' => __('Enable to require all selected products to have been purchased.', 'mailoptin'),
                        'type'        => 'flat',// light, ios, flat
                        'priority'    => 6
                    )
                ),
                'product_in_cart_header'             => new WP_Customize_Custom_Content(
                    $wp_customize,
                    $option_prefix . '[product_in_cart_header]',
                    [
                        'content'     => '<div class="mo-field-header">' . __("Products in Cart", 'mailoptin') . '</div>',
                        'block_class' => 'mo-field-header-wrapper',
                        'section'     => $this->customizer_wc_conditions_section_id,
                        'settings'    => $option_prefix . '[product_in_cart_header]',
                        'priority'    => 10,
                    ]
                ),
                'product_in_cart_products'           => new WP_Customize_Chosen_Select_Control(
                    $wp_customize,
                    $option_prefix . '[product_in_cart_products]',
                    array(
                        'label'       => __('Select WooCommerce products:'),
                        'section'     => $this->customizer_wc_conditions_section_id,
                        'settings'    => $option_prefix . '[product_in_cart_products]',
                        'search_type' => 'woocommerce_products',
                        'choices'     => ControlsHelpers::get_post_type_posts('product'),
                        'priority'    => 20
                    )
                ),
                'product_in_cart_require_all'        => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[product_in_cart_require_all]',
                    array(
                        'label'       => __('Require all', 'mailoptin'),
                        'section'     => $this->customizer_wc_conditions_section_id,
                        'settings'    => $option_prefix . '[product_in_cart_require_all]',
                        'description' => __('Enable to require all selected products to be in cart.', 'mailoptin'),
                        'type'        => 'flat',// light, ios, flat
                        'priority'    => 30
                    )
                ),
                'cart_item_count_header'             => new WP_Customize_Custom_Content(
                    $wp_customize,
                    $option_prefix . '[cart_item_count_header]',
                    [
                        'content'     => '<div class="mo-field-header">' . __("Cart Item Count", 'mailoptin') . '</div>',
                        'block_class' => 'mo-field-header-wrapper',
                        'section'     => $this->customizer_wc_conditions_section_id,
                        'settings'    => $option_prefix . '[cart_item_count_header]',
                        'priority'    => 40,
                    ]
                ),
                'cart_item_count_more_than'          => array(
                    'type'        => 'number',
                    'label'       => __('More than (optional)', 'mailoptin'),
                    'section'     => $this->customizer_wc_conditions_section_id,
                    'settings'    => $option_prefix . '[cart_item_count_more_than]',
                    'input_attrs' => ['min' => 0],
                    'priority'    => 50
                ),
                'cart_item_count_less_than'          => array(
                    'type'        => 'number',
                    'label'       => __('Less than (optional)', 'mailoptin'),
                    'section'     => $this->customizer_wc_conditions_section_id,
                    'settings'    => $option_prefix . '[cart_item_count_less_than]',
                    'input_attrs' => ['min' => 0],
                    'priority'    => 60
                ),
                'cart_total_header'                  => new WP_Customize_Custom_Content(
                    $wp_customize,
                    $option_prefix . '[cart_total_header]',
                    [
                        'content'     => '<div class="mo-field-header">' . __("Cart Total", 'mailoptin') . '</div>',
                        'block_class' => 'mo-field-header-wrapper',
                        'section'     => $this->customizer_wc_conditions_section_id,
                        'settings'    => $option_prefix . '[cart_total_header]',
                        'priority'    => 70,
                    ]
                ),
                'cart_total_type'                    => array(
                    'type'     => 'select',
                    'choices'  => array(
                        'total'               => __('Cart Total', 'mailoptin'),
                        'total_tax'           => __('Cart Total Tax', 'mailoptin'),
                        'subtotal'            => __('Subtotal', 'mailoptin'),
                        'subtotal_tax'        => __('Subtotal Tax', 'mailoptin'),
                        'shipping_total'      => __('Shipping Total', 'mailoptin'),
                        'shipping_tax'        => __('Shipping Tax', 'mailoptin'),
                        'discount_total'      => __('Discount Total', 'mailoptin'),
                        'discount_tax'        => __('Discount Tax', 'mailoptin'),
                        'cart_contents_total' => __('Cart Contents - Total', 'mailoptin'),
                        'cart_contents_tax'   => __('Cart Contents - Tax', 'mailoptin'),
                        'fee_total'           => __('Fee Total', 'mailoptin'),
                        'fee_tax'             => __('Fee Tax', 'mailoptin'),
                    ),
                    'label'    => __('Total to check', 'mailoptin'),
                    'section'  => $this->customizer_wc_conditions_section_id,
                    'settings' => $option_prefix . '[cart_total_type]',
                    'priority' => 75,
                ),
                'cart_total_more_than'               => array(
                    'type'        => 'number',
                    'label'       => __('More than (optional)', 'mailoptin'),
                    'section'     => $this->customizer_wc_conditions_section_id,
                    'settings'    => $option_prefix . '[cart_total_more_than]',
                    'input_attrs' => ['min' => 0],
                    'priority'    => 80
                ),
                'cart_total_less_than'               => array(
                    'type'        => 'number',
                    'label'       => __('Less than (optional)', 'mailoptin'),
                    'section'     => $this->customizer_wc_conditions_section_id,
                    'settings'    => $option_prefix . '[cart_total_less_than]',
                    'input_attrs' => ['min' => 0],
                    'priority'    => 90
                ),
                'customer_spent_header'              => new WP_Customize_Custom_Content(
                    $wp_customize,
                    $option_prefix . '[customer_spent_header]',
                    [
                        'content'     => '<div class="mo-field-header">' . __("Customer Spent", 'mailoptin') . '</div>',
                        'block_class' => 'mo-field-header-wrapper',
                        'section'     => $this->customizer_wc_conditions_section_id,
                        'settings'    => $option_prefix . '[customer_spent_header]',
                        'priority'    => 100,
                    ]
                ),
                'customer_spent_more_than'           => array(
                    'type'        => 'number',
                    'label'       => __('More than (optional)', 'mailoptin'),
                    'section'     => $this->customizer_wc_conditions_section_id,
                    'settings'    => $option_prefix . '[customer_spent_more_than]',
                    'input_attrs' => ['min' => 0],
                    'priority'    => 100
                ),
                'customer_spent_less_than'           => array(
                    'type'        => 'number',
                    'label'       => __('Less than (optional)', 'mailoptin'),
                    'section'     => $this->customizer_wc_conditions_section_id,
                    'settings'    => $option_prefix . '[customer_spent_less_than]',
                    'input_attrs' => ['min' => 0],
                    'priority'    => 120
                )
            ),
            $wp_customize,
            $option_prefix,
            $customizerClassInstance
        );

        do_action('mailoptin_before_woocommerce_conditions_controls_addition');

        foreach ($woocommerce_control_args as $id => $args) {
            if (is_object($args)) {
                $wp_customize->add_control($args);
            } else {
                $wp_customize->add_control($option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_woocommerce_conditions_controls_addition');
    }

    /**
     * Click Launch display rule.
     *
     * @param \WP_Customize_Manager $wp_customize
     * @param string $option_prefix
     * @param Customizer $customizerClassInstance
     */
    public function woocommerce_controls($instance, $wp_customize, $option_prefix, $customizerClassInstance)
    {
        $woocommerce_control_args = apply_filters(
            "mo_optin_form_customizer_woocommerce_controls",
            array(
                'wc_atc_activate_rule'                     => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[wc_atc_activate_rule]',
                    array(
                        'label'       => esc_html__('Activate Rule', 'mailoptin'),
                        'section'     => $this->customizer_atc_section_id,
                        'settings'    => $option_prefix . '[wc_atc_activate_rule]',
                        'description' => __('Show optin when a product is added to cart.', 'mailoptin'),
                        'priority'    => 10
                    )
                ),
                'wc_atc_products'                          => new WP_Customize_Chosen_Select_Control(
                    $wp_customize,
                    $option_prefix . '[wc_atc_products]',
                    array(
                        'label'       => __('Required products (Optional)'),
                        'section'     => $this->customizer_atc_section_id,
                        'settings'    => $option_prefix . '[wc_atc_products]',
                        'description' => __('Select the products that must be added to trigger this optin.', 'mailoptin'),
                        'search_type' => 'woocommerce_products',
                        'choices'     => ControlsHelpers::get_post_type_posts('product'),
                        'priority'    => 15
                    )
                ),
                'woocommerce_show_all_woo_pages'           => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[woocommerce_show_all_woo_pages]',
                    array(
                        'label'       => esc_html__('Show on all WC pages', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[woocommerce_show_all_woo_pages]',
                        'description' => esc_html__('Enable to show on pages where WooCommerce templates are used. This is usually Shop and product pages as well as archives such as product categories and tags archive pages.', 'mailoptin'),
                        'type'        => 'flat',// light, ios, flat
                        'priority'    => 20
                    )
                ),
                'woocommerce_show_woo_shop'                => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[woocommerce_show_woo_shop]',
                    array(
                        'label'       => esc_html__('Show on WooCommerce shop', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[woocommerce_show_woo_shop]',
                        'description' => esc_html__('Enable to show on the shop page (product archive page).', 'mailoptin'),
                        'type'        => 'flat',// light, ios, flat
                        'priority'    => 30
                    )
                ),
                'woocommerce_show_woo_products'            => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[woocommerce_show_woo_products]',
                    array(
                        'label'       => esc_html__('Show on all WC products', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[woocommerce_show_woo_products]',
                        'description' => esc_html__('Enable to show on any single product page.', 'mailoptin'),
                        'type'        => 'flat',// light, ios, flat
                        'priority'    => 40
                    )
                ),
                'woocommerce_show_specific_woo_products'   => new WP_Customize_Chosen_Select_Control(
                    $wp_customize,
                    $option_prefix . '[woocommerce_show_specific_woo_products]',
                    array(
                        'label'       => __('Show optin specifically on', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[woocommerce_show_specific_woo_products]',
                        'description' => esc_html__('Show only on selected single products pages.', 'mailoptin'),
                        'search_type' => 'woocommerce_products',
                        'choices'     => ControlsHelpers::get_post_type_posts('product'),
                        'priority'    => 50
                    )
                ),
                'woocommerce_show_specific_categories'     => new WP_Customize_Chosen_Select_Control(
                    $wp_customize,
                    $option_prefix . '[woocommerce_show_specific_categories]',
                    array(
                        'label'       => __('Show on product categories', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[woocommerce_show_specific_categories]',
                        'description' => esc_html__('Show only on product pages that belong to selected categories.', 'mailoptin'),
                        'search_type' => 'woocommerce_product_cat',
                        'choices'     => ControlsHelpers::get_terms('product_cat'),
                        'priority'    => 55
                    )
                ),
                'woocommerce_show_specific_tags'           => new WP_Customize_Chosen_Select_Control(
                    $wp_customize,
                    $option_prefix . '[woocommerce_show_specific_tags]',
                    array(
                        'label'       => __('Show on product tags', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[woocommerce_show_specific_tags]',
                        'description' => esc_html__('Show only on product pages that belong to selected tags.', 'mailoptin'),
                        'search_type' => 'woocommerce_product_tags',
                        'choices'     => ControlsHelpers::get_terms('product_tag'),
                        'priority'    => 57
                    )
                ),
                'woocommerce_show_cart_page'               => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[woocommerce_show_cart_page]',
                    array(
                        'label'       => esc_html__('Show on WooCommerce cart', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[woocommerce_show_cart_page]',
                        'description' => esc_html__('Enable to show on WooCommerce cart page.', 'mailoptin'),
                        'type'        => 'flat',// light, ios, flat
                        'priority'    => 60
                    )
                ),
                'woocommerce_show_checkout_page'           => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[woocommerce_show_checkout_page]',
                    array(
                        'label'       => esc_html__('Show on WC checkout', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[woocommerce_show_checkout_page]',
                        'description' => esc_html__('Enable to show on WooCommerce checkout page.', 'mailoptin'),
                        'type'        => 'flat',// light, ios, flat
                        'priority'    => 70
                    )
                ),
                'woocommerce_show_account_pages'           => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[woocommerce_show_account_pages]',
                    array(
                        'label'       => esc_html__('Show on WC customer account', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[woocommerce_show_account_pages]',
                        'description' => esc_html__('Enable to show on WooCommerce customer account pages.', 'mailoptin'),
                        'type'        => 'flat',// light, ios, flat
                        'priority'    => 80
                    )
                ),
                'woocommerce_show_all_endpoints'           => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[woocommerce_show_all_endpoints]',
                    array(
                        'label'       => esc_html__('Show on any WC Endpoints', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[woocommerce_show_all_endpoints]',
                        'description' => esc_html__('Enable to show when on any WooCommerce Endpoint.', 'mailoptin'),
                        'type'        => 'flat',// light, ios, flat
                        'priority'    => 90
                    )
                ),
                'woocommerce_show_view_order_endpoint'     => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[woocommerce_show_view_order_endpoint]',
                    array(
                        'label'       => esc_html__('Show on View Order endpoint', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[woocommerce_show_view_order_endpoint]',
                        'description' => esc_html__('Enable to show when the endpoint page for view order is displayed.', 'mailoptin'),
                        'type'        => 'flat',// light, ios, flat
                        'priority'    => 95
                    )
                ),
                'woocommerce_show_order_pay_endpoint'      => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[woocommerce_show_order_pay_endpoint]',
                    array(
                        'label'       => esc_html__('Show on Order Pay endpoint', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[woocommerce_show_order_pay_endpoint]',
                        'description' => esc_html__('Enable to show when the endpoint page for order pay is displayed.', 'mailoptin'),
                        'type'        => 'flat',// light, ios, flat
                        'priority'    => 100
                    )
                ),
                'woocommerce_show_order_received_endpoint' => new WP_Customize_Toggle_Control(
                    $wp_customize,
                    $option_prefix . '[woocommerce_show_order_received_endpoint]',
                    array(
                        'label'       => esc_html__('Show on Order Received endpoint', 'mailoptin'),
                        'section'     => $this->customizer_section_id,
                        'settings'    => $option_prefix . '[woocommerce_show_order_received_endpoint]',
                        'description' => esc_html__('Enable to show when the endpoint page for order received or thank you page is displayed.', 'mailoptin'),
                        'type'        => 'flat',// light, ios, flat
                        'priority'    => 110
                    )
                )
            ),
            $wp_customize,
            $option_prefix,
            $customizerClassInstance
        );

        do_action('mailoptin_before_woocommerce_controls_addition');

        foreach ($woocommerce_control_args as $id => $args) {
            if (is_object($args)) {
                $wp_customize->add_control($args);
            } else {
                $wp_customize->add_control($option_prefix . '[' . $id . ']', $args);
            }
        }

        do_action('mailoptin_after_woocommerce_controls_addition');
    }

    public function global_vars($vars)
    {
        if ( ! empty($this->cart_basket)) {
            $vars['wc_atc_products'] = $this->cart_basket;
        }

        return $vars;
    }

    /**
     * Handles add to cart via URL / Non-ajax
     *
     * @param $message
     * @param $products
     *
     * @return mixed
     */
    public function wc_add_to_cart_message_html($message, $products)
    {
        $this->cart_basket = array_map('absint', ! is_array($products) ? [$products] : array_keys($products));

        return $message;
    }

    /**
     * Singleton poop.
     *
     * @return self
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