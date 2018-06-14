<?php
/**
 * Change WC hooks
 */

add_filter( 'woocommerce_enqueue_styles', 'sshop_dequeue_styles' );
/**
 * Disable layout css
 *
 * @param $enqueue_styles
 * @return mixed
 */
function sshop_dequeue_styles( $enqueue_styles ) {
    //unset( $enqueue_styles['woocommerce-general'] );	// Remove the gloss
    unset( $enqueue_styles['woocommerce-layout'] );		// Remove the layout
    //unset( $enqueue_styles['woocommerce-smallscreen'] );	// Remove the smallscreen optimisation
    return $enqueue_styles;
}


// Remove star on loop product
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );


// Remove deafault btn
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
/**
 * Add wishlist button
 */
function sshop_add_wishlist_btn(){
    if ( defined( 'YITH_WCWL' ) ) {
        echo do_shortcode('[yith_wcwl_add_to_wishlist]');
    }
}

// New button position
/*
add_action( 'woocommerce_shop_loop_item_btn', 'sshop_add_wishlist_btn', 30 );
add_action( 'woocommerce_shop_loop_item_btn', 'woocommerce_template_loop_add_to_cart', 10 );
*/
//
add_action( 'woocommerce_shop_loop_item_btn', 'sshop_add_wishlist_btn', 30 );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 10 );


// Change number product to show
function sshop_number_products_to_show(){
    return 24;
}
add_filter( 'loop_shop_per_page','sshop_number_products_to_show', 20 );


function sshop_wc_show_page_title(){
    if ( is_product() ) {
        return false;
    }

    return true;
}
add_filter( 'woocommerce_show_page_title', 'sshop_wc_show_page_title' );

// Change sale fash to percent
add_filter( 'woocommerce_sale_flash', 'sshop_get_wc_sale_flash', 25, 3 );

// Remove wrapper
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

// Do not show sidebar on cart and checkout page
function sshop_wc_hide_sidebar( $has_sidebar = false ){
    if ( is_cart() || is_checkout() || is_account_page() || is_wc_endpoint_url() ) {
        $has_sidebar = false;
    }

    if ( $has_sidebar ) {
        if ( function_exists( 'yith_wcwl_is_wishlist_page' ) ) {
            if ( yith_wcwl_is_wishlist_page() ){
                $has_sidebar = false;
            }

        }
    }
    return $has_sidebar;
}
add_filter( 'sshop_layout_has_sidebar', 'sshop_wc_hide_sidebar' );

// Remove cross sell and up-sell in page page
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );




