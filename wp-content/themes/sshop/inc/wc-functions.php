<?php
/**
 * Hook in on activation
 */

/**
 * Define image sizes
 */
function sshop_wc_image_dimensions() {
    global $pagenow;

    if ( ! isset( $_GET['activated'] ) || $pagenow != 'themes.php' ) {
        return;
    }

    $catalog = array(
        'width' 	=> '300',	// px
        'height'	=> '366',	// px
        'crop'		=> 1 		// true
    );

    $single = array(
        'width' 	=> '600',	// px
        'height'	=> '600',	// px
        'crop'		=> 1 		// true
    );

    $thumbnail = array(
        'width' 	=> '180',	// px
        'height'	=> '180',	// px
        'crop'		=> 1 		// false
    );

    // Image sizes
    update_option( 'shop_catalog_image_size', $catalog ); 		// Product category thumbs
    update_option( 'shop_single_image_size', $single ); 		// Single product image
    update_option( 'shop_thumbnail_image_size', $thumbnail ); 	// Image gallery thumbs
}

add_action( 'after_switch_theme', 'sshop_wc_image_dimensions', 1 );


function sshop_get_wc_sale_flash( $html = '', $post, $product ) {
    $s = '';
    if ( $product->get_type() == 'variable') {
        $available_variations = $product->get_available_variations();
        $max = 0;
        $min = 0;
        foreach( $available_variations as $vr ) {
            if ( isset( $vr['regular_price'] ) && isset( vr['sale_price'] ) ) {
                if ($vr['regular_price'] > 0 && $vr['sale_price'] > 0) {
                    $percentage = round(((($vr['regular_price'] - $vr['sale_price']) / $vr['regular_price']) * 100), 1);
                    if ($min > $percentage) {
                        $min = $percentage;
                    }
                    if ($max < $percentage) {
                        $max = $percentage;
                    }
                }
            }
        }
        $s = sprintf( esc_html_x( '-%s%%', 'price save value', 'sshop' ), $max );
    } elseif ( $product->get_type() == 'simple' ) {
        $percentage = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
        $s = sprintf( esc_html_x( '-%s%%', 'price save value', 'sshop' ), $percentage );
    }

    if ( $s ) {
        $s = '<div class="onsale">'.$s.'</div>';
    }
    return $s;

}

/** Template pages ********************************************************/

if ( ! function_exists( 'woocommerce_content' ) ) {

    /**
     * Output WooCommerce content.
     *
     * This function is only used in the optional 'woocommerce.php' template.
     * which people can add to their themes to add basic woocommerce support.
     * without hooks or modifying core templates.
     *
     */
    function woocommerce_content() {

        if ( is_singular( 'product' ) ) {

            while ( have_posts() ) : the_post();

                wc_get_template_part( 'content', 'single-product' );

            endwhile;

        } else { ?>

            <?php if ( have_posts() ) : ?>

                <?php do_action( 'woocommerce_before_shop_loop' ); ?>

                <?php woocommerce_product_loop_start(); ?>

                <?php woocommerce_product_subcategories(); ?>

                <?php while ( have_posts() ) : the_post(); ?>

                    <?php wc_get_template_part( 'content', 'product' ); ?>

                <?php endwhile; // end of the loop. ?>

                <?php woocommerce_product_loop_end(); ?>

                <?php do_action( 'woocommerce_after_shop_loop' ); ?>

            <?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

                <?php do_action( 'woocommerce_no_products_found' ); ?>

            <?php endif;

        }
    }
}



