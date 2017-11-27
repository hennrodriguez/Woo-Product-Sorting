<?php
function cruxstore_child_scripts() {
    wp_enqueue_style( 'rotsen-furniture', get_stylesheet_directory_uri() . '/style.css' );
}
add_action('wp_enqueue_scripts', 'cruxstore_child_scripts', 99);


/* To add WooCommerce registration form custom fields. */

function WC_extra_registation_fields() {?>
    <p class="form-row form-row-first">
       <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
    </p>
    
    <p class="form-row form-row-last">
       <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
    </p>
    
    <p class="form-row form-row-wide">
       <label for="reg_billing_country"><?php _e( 'Country', 'woocommerce' ); ?> <span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_country" id="reg_billing_country" value="<?php esc_attr_e( $_POST['billing_country'] ); ?>" />
    </p>

    <p class="form-row form-row-wide">
       <label for="reg_billing_state"><?php _e( 'State', 'woocommerce' ); ?> <span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_state" id="reg_billing_state" value="<?php esc_attr_e( $_POST['billing_state'] ); ?>" />
    </p>
    
    <p class="form-row form-row-wide">
       <label for="reg_billing_city"><?php _e( 'City', 'woocommerce' ); ?> <span class="required">*</span></label>
       <input type="text" class="input-text" name="billing_city" id="reg_billing_city" value="<?php esc_attr_e( $_POST['billing_city'] ); ?>" />
    </p>
    
    <div class="clear"></div>
    <?php
}

add_action( 'woocommerce_register_form', 'WC_extra_registation_fields');


/* To validate WooCommerce registration form custom fields.  */
function WC_validate_reg_form_fields($username, $email, $validation_errors) {
    if (isset($_POST['billing_first_name']) && empty($_POST['billing_first_name']) ) {
        $validation_errors->add('billing_first_name_error', __('First name is required.', 'woocommerce'));
    }
    if (isset($_POST['billing_last_name']) && empty($_POST['billing_last_name']) ) {
        $validation_errors->add('billing_last_name_error', __('Last name is required.', 'woocommerce'));
    }
    if (isset($_POST['billing_country']) && empty($_POST['billing_country']) ) {
        $validation_errors->add('billing_country_error', __('Country is required.', 'woocommerce'));
    }
    if (isset($_POST['billing_state']) && empty($_POST['billing_state']) ) {
        $validation_errors->add('billing_state_error', __('State is required.', 'woocommerce'));
    }
    if (isset($_POST['billing_city']) && empty($_POST['billing_city']) ) {
        $validation_errors->add('billing_city_error', __('City is required.', 'woocommerce'));
    }

    return $validation_errors;
}

add_action('woocommerce_register_post', 'WC_validate_reg_form_fields', 10, 3);


/* To save WooCommerce registration form custom fields. */
function WC_save_registration_form_fields($customer_id) {
    
    //First name field
    if (isset($_POST['billing_first_name'])) {
        update_user_meta($customer_id, 'billing_first_name', sanitize_text_field($_POST['billing_first_name']));
    }
    //Last name field
    if (isset($_POST['billing_last_name'])) {
        update_user_meta($customer_id, 'billing_last_name', sanitize_text_field($_POST['billing_last_name']));
    }
    //Country field
    if (isset($_POST['billing_country'])) {
        update_user_meta($customer_id, 'billing_country', sanitize_text_field($_POST['billing_country']));
    }
    //State field
    if (isset($_POST['billing_state'])) {
        update_user_meta($customer_id, 'billing_state', sanitize_text_field($_POST['billing_state']));
    }
    //City field
    if (isset($_POST['billing_city'])) {
        update_user_meta($customer_id, 'billing_city', sanitize_text_field($_POST['billing_city']));
    }
    
}

add_action('woocommerce_created_customer', 'WC_save_registration_form_fields');



// Modify the default WooCommerce orderby dropdown
//
// Options: menu_order, popularity, rating, date, price, price-desc
// In this example I'm removing price & price-desc but you can remove any of the options
function rotsen_woocommerce_catalog_orderby( $orderby ) {
	unset($orderby["rating"]);
	unset($orderby["popularity"]);
	$orderby["date"] = __('newest items first', 'woocommerce');
	$orderby["price"] = __('highest to lowest price', 'woocommerce');
	$orderby["price-desc"] = __('lowest to highest price', 'woocommerce');
	return $orderby;
}
add_filter( "woocommerce_catalog_orderby", "rotsen_woocommerce_catalog_orderby", 20 );



// Add "Sort by date: oldest to newest" to the menu
// We still need to add the functionality that actually does the sorting
// Original credit to Remi Corson: http://www.remicorson.com/woocommerce-sort-products-from-oldest-to-most-recent/
function rotsen_woocommerce_catalog_sortby( $sortby ) {
	$sortby['oldest_to_recent'] = __( 'oldest items first', 'woocommerce' );
	return $sortby;
}
add_filter( 'woocommerce_catalog_orderby', 'rotsen_woocommerce_catalog_sortby', 20 );
// Add the ability to sort by oldest to newest
function rotsen_woocommerce_get_catalog_ordering_args( $args ) {
	$orderby_value = isset( $_GET['orderby'] ) ? woocommerce_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
	if ( 'oldest_to_recent' == $orderby_value ) {
		$args['orderby'] = 'date';
		$args['order']   = 'ASC';
	}
	return $args;
}
add_filter( 'woocommerce_get_catalog_ordering_args', 'rotsen_woocommerce_get_catalog_ordering_args', 20 );



/* ADD ROTSEN ORDERING OPTION */
add_filter( 'woocommerce_get_catalog_ordering_args', 'custom_woocommerce_get_catalog_ordering_args' );

function custom_woocommerce_get_catalog_ordering_args( $args ) {
    $orderby_value = isset( $_GET['orderby'] ) ? woocommerce_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

    if ( 'random_list' == $orderby_value ) {
		$args['orderby'] = array(
			'menu_order' => 'ASC', 
			'date' => 'DESC'
		);
        $args['meta_key'] = '';
    }
    return $args;
}

add_filter( 'woocommerce_default_catalog_orderby_options', 'custom_woocommerce_catalog_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby' );

function custom_woocommerce_catalog_orderby( $sortby ) {
    $sortby['random_list'] = 'Rotsen';
    return $sortby;
}


/*
** REGISTER SINGLE PRODUCT PAGE SIDEBAR WIDGET AREA
*/

function arphabet_widgets_init() {

    register_sidebar( array(
        'name'          => 'Product Page sidebar',
        'id'            => 'product_page_sidebar',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );

}
add_action( 'widgets_init', 'arphabet_widgets_init' );



/*
** AJAX TIMEOUT
*/

/*function new_ajax_timeout() {
    return 180000; // 50 seconds
}
add_filter( 'sswcaf_ajax_timeout', 'new_ajax_timeout' );*/






