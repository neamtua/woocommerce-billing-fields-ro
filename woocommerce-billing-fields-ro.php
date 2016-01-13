<?php
/*
Plugin Name: WooCommerce Romanian Billing fields
Plugin URI: https://github.com/neamtua/woocommerce-billing-fields-ro
Description: This is a WooCommerce plugin that adds extra fields to the billing address containing required information by romanian law (CUI, numar de inregistrare la registrul comertului, cont bancar, banca)
Version: 1.0.3
Author: Andrei Neamtu
Author URI: https://ameya.ro
*/
add_action('plugins_loaded', 'woocommerce_billing_fields_ro_load', 0);
function woocommerce_billing_fields_ro_load()
{
    /**
     * Check if WooCommerce is active
     **/
    if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        add_action('woocommerce_after_order_notes', 'woocommerce_billing_fields_ro');

        function woocommerce_billing_fields_ro($checkout)
        {

            echo '<div id="woocommerce_billing_fields_ro">
                    <h3 style="margin:0;padding:0;">'.__('Date facturare firmă').'</h3>
                    <p>&nbsp;</p>';

            woocommerce_form_field('wbfr_cif', array(
                'type'          => 'text',
                'class'         => array('form-row-wide'),
                'label'         => __('CIF'),
                'placeholder'   => __('Cod de identificare fiscală'),
                ), $checkout->get_value('wbfr_cif'));

            woocommerce_form_field('wbfr_regcom', array(
                'type'          => 'text',
                'class'         => array('form-row-wide'),
                'label'         => __('Nr. înregistrare Registrul Comerțului'),
                'placeholder'   => __('Nr. înregistrare Registrul Comerțului'),
                ), $checkout->get_value('wbfr_regcom'));

            woocommerce_form_field('wbfr_cont_banca', array(
                'type'          => 'text',
                'class'         => array('form-row-wide'),
                'label'         => __('Cont bancar'),
                'placeholder'   => __('Cont bancar'),
                ), $checkout->get_value('wbfr_cont_banca'));

            woocommerce_form_field('wbfr_banca', array(
                'type'          => 'text',
                'class'         => array('form-row-wide'),
                'label'         => __('Banca'),
                'placeholder'   => __('Banca'),
                ), $checkout->get_value('wbfr_banca'));

            echo '</div>';

        }

        /**
         * Process the checkout
         **/
        add_action('woocommerce_checkout_process', 'woocommerce_billing_fields_ro_process');

        function woocommerce_billing_fields_ro_process()
        {
            global $woocommerce;

            // Check if set, if its not set add an error. This one is only required for companies
            if (isset($_POST['billing_company']) && !empty($_POST['billing_company'])) {
                if (empty($_POST['wbfr_cif']) ||
                    empty($_POST['wbfr_regcom']) ||
                    empty($_POST['wbfr_cont_banca']) ||
                    empty($_POST['wbfr_banca'])
                ) {
                    wc_add_notice(
                        __('Nu ai introdus toate câmpurile de la <strong>Date facturare firmă</strong> din partea de jos a paginii'),
                        'error'
                    );
                }
            }
        }

        /**
         * Update the user meta with field value
         **/
        add_action('woocommerce_checkout_update_user_meta', 'woocommerce_billing_fields_ro_update_user_meta');

        function woocommerce_billing_fields_ro_update_user_meta($userId)
        {
            if ($userId && isset($_POST['wbfr_cif'])) {
                update_user_meta($userId, 'wbfr_cif', esc_attr($_POST['wbfr_cif']));
            }
            if ($userId && isset($_POST['wbfr_regcom'])) {
                update_user_meta($userId, 'wbfr_regcom', esc_attr($_POST['wbfr_regcom']));
            }
            if ($userId && isset($_POST['wbfr_cont_banca'])) {
                update_user_meta($userId, 'wbfr_cont_banca', esc_attr($_POST['wbfr_cont_banca']));
            }
            if ($userId && isset($_POST['wbfr_banca'])) {
                update_user_meta($userId, 'wbfr_banca', esc_attr($_POST['wbfr_banca']));
            }
        }

        /**
         * Update the order meta with field value
         **/
        add_action('woocommerce_checkout_update_order_meta', 'woocommerce_billing_fields_ro_update_order_meta');

        function woocommerce_billing_fields_ro_update_order_meta($orderId)
        {
            if (isset($_POST['wbfr_cif'])) {
                update_post_meta($orderId, 'wbfr_cif', esc_attr($_POST['wbfr_cif']));
            }
            if (isset($_POST['wbfr_regcom'])) {
                update_post_meta($orderId, 'wbfr_regcom', esc_attr($_POST['wbfr_regcom']));
            }
            if (isset($_POST['wbfr_cont_banca'])) {
                update_post_meta($orderId, 'wbfr_cont_banca', esc_attr($_POST['wbfr_cont_banca']));
            }
            if (isset($_POST['wbfr_banca'])) {
                update_post_meta($orderId, 'wbfr_banca', esc_attr($_POST['wbfr_banca']));
            }
        }

        /**
         * Add the field to order emails
         **/
        add_filter('woocommerce_email_order_meta_keys', 'woocommerce_billing_fields_ro_order_meta_keys');

        function woocommerce_billing_fields_ro_order_meta_keys($keys)
        {
            $keys[__('CIF')] = 'wbfr_cif';
            $keys[__('Nr. înregistrare Registrul Comerțului')] = 'wbfr_regcom';
            $keys[__('Cont bancar')] = 'wbfr_cont_banca';
            $keys[__('Banca')] = 'wbfr_banca';
            return $keys;
        }
    }
}
