<?php
/*
Plugin Name: WPM Bundle Products
Plugin URI: https://wp-masters.com/
Description: Add free Bundle products after buy main product
Author: wp-masters
Version: 1.0
*/

define('WPM_BUNDLE_DIR', plugins_url('', __FILE__));

class WPM_Bundle_Products
{
    /**
     * Initialize functions
     */
    public function __construct()
    {
        // Init Functions
        add_action('save_post', [$this, 'save_result']);

        // Include Styles and Scripts
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts_and_styles']);
        add_action('wp_enqueue_scripts', [$this, 'include_scripts_and_styles'], 99);

        // WooCommerce Functions
        add_filter('woocommerce_add_to_cart_validation', [$this, 'validate_add_cart_item'], 10, 5);
        add_action('woocommerce_before_calculate_totals', [$this, 'remove_price_for_bundle_products']);
        add_action('woocommerce_product_meta_end', [$this, 'add_bundle_information']);

        // Add new table to WooCommerce product
        add_filter('woocommerce_product_data_tabs', [$this, 'discount_rates']);
        add_action('woocommerce_product_data_panels', [$this, 'discounts_tab_contents']);
    }

    /**
     * Add Bundle Tab
     */
    public function add_bundle_information()
    {
        $bundle_products = unserialize(get_post_meta(get_the_ID(), 'wpm_bundle_products', true));

        include('templates/frontend/list_bundle_items.php');
    }

    /**
     * Add Bundle Tab
     */
    public function discount_rates($tabs)
    {
        $tabs['bundle_products'] = array(
            'label'    => 'Bundle Products',
            'target'   => 'wpm_bundle_products_tab_content',
            'priority' => 15,
        );

        return $tabs;
    }

    /**
     * Content for Bundle Tab
     */
    public function discounts_tab_contents()
    {
        // Get Bundle Settings
        $bundle_products = unserialize(get_post_meta($_GET['post'], 'wpm_bundle_products', true));

        // Get Products from WooCommerce
        $args = array(
            'post_type'      => 'product',
            'orderby'        => 'desc',
            'posts_per_page' => - 1
        );
        $products = get_posts($args);

        include('templates/admin/bundle_products.php');
    }

    /**
     * Save meta
     *
     * @param $post_id
     */
    public function save_result($post_id)
    {
        if(isset($_POST['wpm_bundle_products'])) {
            update_post_meta($post_id, 'wpm_bundle_products', serialize($this->sanitize_array($_POST['wpm_bundle_products'])));
        }
    }

    /**
     * Check if item in the Storage
     */
    public function validate_add_cart_item($passed, $product_id, $quantity, $variation_id = '', $variations = '' )
    {
        // Check is Bundled product
        $bundle_products = unserialize(get_post_meta($product_id, 'wpm_bundle_products', true));

        if($bundle_products && !empty($bundle_products)) {
            foreach($bundle_products as $item_id) {
                if($item_id != 0) {
                    WC()->cart->add_to_cart($item_id);
                }
            }
        }

        return $passed;
    }

    /**
     * Remove price from Bundled products
     */
    public function remove_price_for_bundle_products($cart_object)
    {
        $remove_price_from_ids = [];

        // Set Default Price to products in Cart
        foreach($cart_object->cart_contents as $item) {
            $product = wc_get_product($item['product_id']);
            $item['data']->set_price($product->get_price());

            $bundle_products = unserialize(get_post_meta($item['product_id'], 'wpm_bundle_products', true));

            if($bundle_products && !empty($bundle_products)) {
                foreach($bundle_products as $product_id) {
                    if($product_id != 0) {
                        $remove_price_from_ids[] = $product_id;
                    }
                }
            }
        }

        // Remove Price from IDS bundle List
        foreach ($cart_object->cart_contents as $item) {
            if(in_array($item['product_id'], $remove_price_from_ids)) {
                $item['data']->set_price(0);
            }
        }
    }

    /**
     * Sanitize Array Data
     */
    public function sanitize_array($data)
    {
        $filtered = [];
        foreach($data as $key => $value) {
            if(is_array($value)) {
                foreach($value as $sub_key => $sub_value) {
                    $filtered[$key][$sub_key] = sanitize_text_field($sub_value);
                }
            } else {
                $filtered[$key] = sanitize_text_field($value);
            }
        }

        return $filtered;
    }

    /**
     * Include Scripts And Styles on FrontEnd
     */
    public function include_scripts_and_styles()
    {
        // Register styles
        wp_enqueue_style('wpm-bundle-products', plugins_url('templates/assets/css/frontend.css', __FILE__), false, '1.0.11', 'all');
    }

    /**
     * Include Scripts And Styles on Admin Pages
     */
    public function admin_scripts_and_styles()
    {
        // Register styles
        wp_enqueue_style('wpm-bundle-products-selectstyle', plugins_url('templates/libs/selectstyle/selectstyle.css', __FILE__));
        wp_enqueue_style('wpm-bundle-products-font-awesome', plugins_url('templates/libs/font-awesome/scripts/all.min.css', __FILE__));
        wp_enqueue_style('wpm-bundle-products', plugins_url('templates/assets/css/admin.css', __FILE__));

        // Register Scripts
        wp_enqueue_script('wpm-bundle-products-selectstyle', plugins_url('templates/libs/selectstyle/selectstyle.js', __FILE__));
        wp_enqueue_script('wpm-bundle-products-font-awesome', plugins_url('templates/libs/font-awesome/scripts/all.min.js', __FILE__));
        wp_enqueue_script('wpm-bundle-products', plugins_url('templates/assets/js/admin.js', __FILE__), array('jquery'), '1.2.4', 'all');
    }
}

new WPM_Bundle_Products();