<?php
/*
Plugin Name: WooCommerce Policy Checkboxes
Plugin URI: https://github.com/Louisreed/woocommerce-policy-checkboxes
Description: This plugin adds a compulsory checkbox to the checkout page in WooCommerce that must be checked in order for the customer to place their order.
Version: 1.4.4
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
        <h1>Wordpress Policy Checkboxes Settings</h1>
        <form method="post" action="options.php">
          <?php settings_fields( 'policy-checkboxes-settings-group' ); ?>
          <?php do_settings_sections( 'policy-checkboxes-settings-group' ); ?>

          <table class="form-table">
            <tr valign="top">
              <th scope="row">Privacy Policy URL</th>
              <td><input type="text" name="privacy-policy-url" size="80" value="<?php echo esc_attr( get_option('privacy-policy-url') ); ?>" /></td>
            </tr>
            <tr valign="top">
              <th scope="row">Refunds and Returns URL</th>
              <td><input type="text" name="refunds-and-returns-url" size="80" value="<?php echo esc_attr( get_option('refunds-and-returns-url') ); ?>" /></td>
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
  $privacy_policy_url = get_option( 'privacy_policy_url' );
  woocommerce_form_field( 'privacy_policy', array(
    'type'          => 'checkbox',
    'class'         => array('privacy-policy form-row-wide'),
    'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
    'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
    'required'      => true,
    'label'         => 'I have read and understand the <a href="' . esc_url( $privacy_policy_url ) . '" target="_blank" class="policy-link">privacy policy</a>',
  ));
}

add_action( 'woocommerce_review_order_before_submit', 'refunds_and_returns_checkbox' );
function refunds_and_returns_checkbox() {
  $refunds_and_returns_url = get_option( 'refunds_and_returns_url' );
  woocommerce_form_field( 'refunds_and_returns', array(
    'type'          => 'checkbox',
    'class'         => array('refunds-and-returns form-row-wide'),
    'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
    'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
    'required'      => true,
    'label'         => 'I have read and understand the <a href="' . esc_url( $refunds_and_returns_url ) . '" target="_blank" class="policy-link">refunds and returns policy</a>',
  ));


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

<script>
  // Set the checkboxes to be disabled by default
  document.querySelectorAll('.policy-checkbox').forEach(function(checkbox) {
    checkbox.setAttribute('disabled', true);
  });

  // Add an event listener to the policy links
  document.querySelectorAll('.policy-link').forEach(function(link) {
    link.addEventListener('click', function(event) {
      // Enable the checkbox when the policy link is clicked
      event.target.parentElement.querySelector('.policy-checkbox').removeAttribute('disabled');
    });
  });
</script>