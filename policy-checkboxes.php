<?php
/*
Plugin Name: Policy Checkboxes
Plugin URI: https://github.com/Louisreed/woocommerce-policy-checkboxes
Description: This plugin adds a compulsory checkbox to the checkout page in WooCommerce that must be checked in order for the customer to place their order.
Description: The checkbox reads "You must accept the privacy policy" and has a link to the privacy-policy page (#privacypolicy).
Version: 1.3
Author: Louis Reed
Author URI: https://louisreed.co.uk
*/

// Register settings
add_action( 'admin_init', 'register_policy_checkboxes_settings' );
function register_policy_checkboxes_settings() {
  register_setting( 'policy-checkboxes-settings-group', 'privacy-policy-url' );
  register_setting( 'policy-checkboxes-settings-group', 'refunds-and-returns-url' );
}

// Add settings tab in WooCommerce settings
add_action( 'admin_menu', 'add_policy_checkboxes_settings_tab', 50 );
function add_policy_checkboxes_settings_tab() {
  add_submenu_page( 'woocommerce', 'Policy Checkboxes Settings', 'Policy Checkboxes', 'manage_options', 'policy-checkboxes', 'policy_checkboxes_settings_page' );
}

function policy_checkboxes_settings_page() {
    ?>
    
      <div class="wrap">
        <h1>Policy Checkboxes Settings</h1>
        <form method="post" action="options.php">
          <?php settings_fields( 'policy-checkboxes-settings-group' ); ?>
          <?php do_settings_sections( 'policy-checkboxes-settings-group' ); ?>

          <table class="form-table">
            <tr valign="top">
              <th scope="row">Privacy Policy URL</th>
              <td><input type="text" name="privacy-policy-url" value="<?php echo esc_attr( get_option('privacy-policy-url') ); ?>" /></td>
            </tr>
            <tr valign="top">
              <th scope="row">Refunds and Returns URL</th>
              <td><input type="text" name="refunds-and-returns-url" value="<?php echo esc_attr( get_option('refunds-and-returns-url') ); ?>" /></td>
            </tr>
          </table>
          <?php submit_button(); ?>
        </form>
      </div>
    <?php
    }

// Update checkbox labels with new policy URLs
add_action( 'woocommerce_review_order_before_submit', 'privacy_policy_checkbox' );
function privacy_policy_checkbox() {
  $policy_url = get_option( 'privacy-policy-url' );
  woocommerce_form_field( 'privacy_policy', array(
  'type' => 'checkbox',
  'class' => array('privacy-policy form-row-wide'),
  'label_class' => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
  'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
  'required' => true,
  'label' => 'I have read and understand the <a href="' . $policy_url . '" target="_blank" >privacy policy</a>',
  ));
}

add_action( 'woocommerce_review_order_before_submit', 'refunds_and_returns_checkbox' );
function refunds_and_returns_checkbox() {
  $policy_url = get_option( 'refunds-and-returns-url' );
  woocommerce_form_field( 'refunds_and_returns', array(
  'type' => 'checkbox',
  'class' => array('refunds-and-returns form-row-wide'),
  'label_class' => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
  'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
  'required' => true,
  'label' => 'I have read and understand the <a href="' . $policy_url . '" target="_blank">refunds and returns policy</a>',
  ));
}

// Validation
add_action( 'woocommerce_checkout_process', 'privacy_policy_checkbox_validation' );
function privacy_policy_checkbox_validation() {
  if ( ! (int) isset( $_POST['privacy_policy'] ) ) {
  wc_add_notice( __( 'Please accept the privacy policy' ), 'error' );
  }
}

add_action( 'woocommerce_checkout_process', 'refunds_and_returns_checkbox_validation' );
function refunds_and_returns_checkbox_validation() {
  if ( ! (int) isset( $_POST['refunds_and_returns'] ) ) {
  wc_add_notice( __( 'Please read and accept the refunds and returns policy' ), 'error' );
  }
}