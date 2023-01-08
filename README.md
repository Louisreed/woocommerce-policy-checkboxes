# WooCommerce Policy Checkboxes
This plugin adds a compulsory checkbox to the checkout page in WooCommerce that must be checked in order for the customer to place their order. The checkbox reads "You must accept the privacy policy" and has a link to the privacy policy page. A second checkbox for the refunds and returns policy is also included.

## Installation
1. Download the plugin zip file from the releases tab.
2. Go to your WordPress dashboard and click on "Plugins"
3. Click "Add New" and then "Upload Plugin"
4. Choose the plugin zip file and click "Install Now"
5. Activate the plugin

## Configuration
1. Go to the WooCommerce settings page
2. Click on the "Policy Checkboxes" tab
3. Check the box to enable the privacy policy checkbox
4. Check the box to enable the refunds and returns policy checkbox
5. Enter the URL of your privacy policy page in the "Privacy Policy URL" field
6. Enter the URL of your refunds and returns policy page in the "Refunds and Returns Policy URL" field (optional)
7. Click "Save Changes"

## FAQ
**How do I customize the text of the policy checkboxes?**

You can customize the text of the policy checkboxes by modifying the label parameter in the woocommerce_form_field function. For example:

`
woocommerce_form_field( 'privacy_policy', array(
  'type'          => 'checkbox',
  'class'         => array('privacy-policy form-row-wide'),
  'label_class'   => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
  'input_class'   => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
  'required'      => true,
  'label'         => 'I have read and accept the <a href="' . esc_url( $privacy_policy_url ) . '" target="_blank" class="policy-link">terms and conditions</a>',
));
`

**How do I customize the look and feel of the policy checkboxes?**

You can customize the look and feel of the policy checkboxes by modifying the class and label_class parameters in the woocommerce_form_field function. You can also use custom CSS to style the checkboxes.

**How do I redirect the customer to the policy pages if they don't check the policy checkboxes?**

You can redirect the customer to the policy pages if they don't check the policy checkboxes by adding the following code to your theme's functions.php file:

`
add_action( 'woocommerce_checkout_process', 'policy_checkbox_validation' );
function policy_checkbox_validation() {
  if ( ! isset( $_POST['privacy_policy'] ) || ! isset( $_POST['refunds_and_returns'] ) ) {
    wc_add_notice( __( 'Please read and accept the policy checkboxes to proceed with your order.' ), 'error' );
  }
}
`

**How do I disable the policy checkboxes for certain products?**

You can disable the policy checkboxes for certain products by adding the following code to your theme's functions.php file:

`
add_filter( 'woocommerce_product_supports', 'disable_policy_checkboxes_for_product', 10, 3 );
function disable_policy_checkboxes_for_product( $supports, $feature, $product ) {
  if ( 'policy-checkboxes' === $feature ) {
    $supports = $product->get_meta( '_disable_policy_checkboxes' );
  }
  return $supports;
}
`

Then, you can disable the policy checkboxes for a specific product by checking the "Disable Policy Checkboxes" checkbox in the product's "Advanced" tab.

## Credits
This plugin was developed by Louis Reed.

## License
This plugin is licensed under the GPLv3. See the LICENSE file for more information.





