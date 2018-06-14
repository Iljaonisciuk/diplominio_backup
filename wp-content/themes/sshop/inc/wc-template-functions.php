<?php
/**
 * Just overwrite woocommerce functions
 *
 * @file wp-content/plugins/woocommerce/includes/wc-template-functions.php
 */


if ( ! function_exists( 'woocommerce_get_product_thumbnail' ) ) {
    /**
     * Change woocommerce get thumbnail html code
     *
     * @see woocommerce_get_product_thumbnail
     */
    function woocommerce_get_product_thumbnail($size = 'shop_catalog', $deprecated1 = 0, $deprecated2 = 0)
    {
        global $product;

        $post = get_post( $product->get_id() );

        $image_size = apply_filters('single_product_archive_thumbnail_size', $size);
        $html = '';
        if (has_post_thumbnail($post)) {
            $props = wc_get_product_attachment_props(get_post_thumbnail_id( $post ), $post);
            $html = get_the_post_thumbnail($post->ID, $image_size, array(
                'title' => $props['title'],
                'alt' => $props['alt'],
            ));
        } elseif (wc_placeholder_img_src()) {
            $html = wc_placeholder_img($image_size);
        }

        return '<a class="product-thumbnail" href="'.esc_url( get_permalink() ).'">' . $html . '</a>';
    }
}

if ( ! function_exists( 'wc_sale_countdown' ) ) {
    function wc_sale_countdown()
    {
        global $sshop_sales_countdown_product;
        if ( ! isset( $sshop_sales_countdown_product ) || ! $sshop_sales_countdown_product ) {
            return ;
        }
        global $post, $sshop_wc_product_sale_end;

        if ( isset( $sshop_wc_product_sale_end ) && $sshop_wc_product_sale_end > 0 ) {
            $date_to = $sshop_wc_product_sale_end;
        } else {
            $date_to = absint( get_post_meta( $post->ID, '_sale_price_dates_to', true ) );
        }

        if ( $date_to > 0 ) {
            ?>
            <div class="wc-countdown" data-final-date="<?php echo esc_attr(date('Y/m/d H:i:s', $date_to)); ?>"><?php esc_html_e('0 Days 00:00:00', 'sshop'); ?></div>
            <?php
        }
    }
}
add_action( 'woocommerce_before_shop_loop_item_title', 'wc_sale_countdown' );


if ( ! function_exists( 'woocommerce_template_loop_product_title' ) ) {
    /**
     * Show the product title in the product loop. By default this is an H2.
     */
    function woocommerce_template_loop_product_title()
    {
        echo '<h2 class="woocommerce-loop-product__title"><a href="'.esc_url( get_permalink() ).'">' . get_the_title() . '</a></h2>';
    }
}


