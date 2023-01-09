<?php
/*
Plugin Name: WooCommerce Policy Checkboxes
Plugin URI: https://github.com/Louisreed/woocommerce-policy-checkboxes
Description: This plugin adds a compulsory checkbox to the checkout page in WooCommerce that must be checked in order for the customer to place their order.
Version: 1.5.7
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

// Create settings page
function policy_checkboxes_settings_page() {
    ?>
    
      <div class="wrap">
        <h1>Wordpress Policy Checkboxes Settings</h1>
        <form method="post" action="options.php">
          <?php settings_fields( 'policy-checkboxes-settings-group' ); ?>
          <?php do_settings_sections( 'policy-checkboxes-settings-group' ); ?>

          <table class="form-table">
          <tr valign="top">
          <th scope="row">Privacy Policy URL</th>
          <td>
            <?php
            $args = array(
              'depth' => 0,
              'child_of' => 0,
              'selected' => get_option('privacy-policy-url'),
              'echo' => 1,
              'name' => 'privacy-policy-url',
              'id' => '',
              'class' => '',
              'show_option_none' => '',
              'show_option_no_change' => '',
              'option_none_value' => ''
            );
            wp_dropdown_pages( $args );
            ?>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row">Refunds and Returns URL</th>
          <td>
            <?php
            $args = array(
              'depth' => 0,
              'child_of' => 0,
              'selected' => get_option('refunds-and-returns-url'),
              'echo' => 1,
              'name' => 'refunds-and-returns-url',
              'id' => '',
              'class' => '',
              'show_option_none' => '',
              'show_option_no_change' => '',
              'option_none_value' => ''
            );
            wp_dropdown_pages( $args );
            ?>
          </td>
        </tr>
          </table>
          <?php submit_button(); ?>
        </form>
      </div>
    <?php
    }

// Update checkbox labels with new policy URLs
add_action( 'woocommerce_review_order_before_submit', 'privacy_policy_checkbox' );
function privacy_policy_checkbox()
{
  // Retrieve content of the privacy policy page
  $privacy_policy_id = get_option('privacy-policy-url');
  $privacy_policy = get_post($privacy_policy_id);
  $privacy_policy_content = $privacy_policy->post_content;

  // Display content in a scrollable window above the privacy policy checkbox
  echo '<div id="privacy-policy-scrollable" style="height: 200px; overflow: scroll;">';
  echo $privacy_policy_content;
  echo '</div>';

  $policy_id = get_option('privacy-policy-url');
  $policy_url = get_permalink($policy_id);

  woocommerce_form_field('privacy_policy', array(
    'type' => 'checkbox',
    'class' => array('privacy-policy form-row-wide'),
    'id' => 'privacy-policy-checkbox',
    'label_class' => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
    'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
    'required' => true,
    'disabled' => true,
    'label' => sprintf(__('I have read and agree to the <a href="%s" target="_blank">Privacy Policy</a>'), $policy_url),
  )
  );
}

add_action( 'woocommerce_review_order_before_submit', 'refunds_and_returns_checkbox' );
function refunds_and_returns_checkbox() {

  // Retrieve content of the refunds and returns policy page
  $refunds_policy_id = get_option('refunds-and-returns-url');
  $refunds_policy = get_post($refunds_policy_id);
  $refunds_policy_content = $refunds_policy->post_content;

  // Display content in a scrollable window above the refunds and returns policy checkbox
  echo '<div id="refunds-policy-scrollable" style="height: 200px; overflow: scroll;">';
  echo $refunds_policy_content;
  echo '</div>';

  $policy_id = get_option( 'refunds-and-returns-url' );
  $policy_url = get_permalink( $policy_id );

  // Create checkbox
  woocommerce_form_field( 'refunds_policy', array(
    'type' => 'checkbox',
    'class' => array('refunds-policy form-row-wide'),
    'id' => 'refunds-policy-checkbox',
    'label_class' => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
    'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
    'required' => true,
    'disabled' => true,
    'label' => sprintf( __('I have read and agree to the <a href="%s" target="_blank">Refunds and Returns Policy</a>'), $policy_url ),
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

// Add Settings link
add_filter( 'plugin_action_links', 'policy_checkboxes_settings_link', 10, 2 );

function policy_checkboxes_settings_link( $links, $file ) {
    if ( strpos( $file, 'policy-checkboxes.php' ) !== false ) {
        $settings_link = '<a href="' . admin_url( 'admin.php?page=policy-checkboxes' ) . '">' . __( 'Settings', 'policy-checkboxes' ) . '</a>';
        array_unshift( $links, $settings_link );
    }
    return $links;
}

// Add JavaScript to disable checkboxes until scrollable windows are scrolled to the bottom
add_action( 'woocommerce_review_order_before_submit', 'disable_checkboxes_until_scrolled' );
function disable_checkboxes_until_scrolled() {
  ?>
  <script type="text/javascript">
    (function($) {
      // Disable privacy policy checkbox until privacy policy scrollable window is scrolled to the bottom
      var privacyPolicyScrollable = document.getElementById('privacy-policy-scrollable');
      var privacyPolicyCheckbox = document.getElementById('privacy-policy-checkbox');
      privacyPolicyCheckbox.disabled = true;
      privacyPolicyScrollable.onscroll = function() {
        if (privacyPolicyScrollable.scrollTop + privacyPolicyScrollable.clientHeight >= privacyPolicyScrollable.scrollHeight) {
          privacyPolicyCheckbox.disabled = false;
        }
      };
      // Disable refunds and returns policy checkbox until refunds and returns policy scrollable window is scrolled to the bottom
      var refundsPolicyScrollable = document.getElementById('refunds-policy-scrollable');
      var refundsPolicyCheckbox = document.getElementById('refunds-policy-checkbox');
      refundsPolicyCheckbox.disabled = true;
      refundsPolicyScrollable.onscroll = function() {
        if (refundsPolicyScrollable.scrollTop + refundsPolicyScrollable.clientHeight >= refundsPolicyScrollable.scrollHeight) {
          refundsPolicyCheckbox.disabled = false;
        }
      };
    })(jQuery);
  </script>
  <?php
}
