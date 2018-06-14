<?php
/**
 * Shortcode
 * 
 * @package Product Categories Designs for WooCommerce 
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

	function pcdfwoo_product_categories_slider( $atts ) {		

		$atts = extract(shortcode_atts(array(
			'number'     => null,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'design'     => 'design-1',			
			'hide_empty' => 1,
			'parent'     => '',
			'ids'        => '',
			'slidestoshow' 		=> '3',
			'slidestoscroll' 	=> '1',
			'loop' 				=> 'true',
			'dots'     			=> 'true',
			'arrows'     		=> 'true',
			'autoplay'     		=> 'false',
			'autoplay_interval' => '3000',
			'speed'             => '300',
			'height'            => '300',
		), $atts));
		
		// If needwd
		wp_enqueue_script( 'wpos-slick-jquery' );
		
		if ( isset($ids) ) {
			$ids = explode( ',', $ids);
			$ids = array_map( 'trim', $ids );
		} else {
			$ids = array();
		}

		$hide_empty = ($hide_empty == true || $hide_empty == 1 ) ? 1 : 0;

		// get terms and workaround WP bug with parents/pad counts
		$args = array(
			'orderby'    => $atts['orderby'],
			'order'      => $atts['order'],
			'hide_empty' => $hide_empty,
			'include'    => $ids,
			'pad_counts' => true,			
			'parent'     => 0
		);

		$product_categories = get_terms( 'product_cat', $args );		
	
		
		if ( '' !== $atts['parent'] ) {
			$product_categories = wp_list_filter( $product_categories, array( 'parent' => $atts['parent'] ) );
		}

		if ( $hide_empty ) {
			foreach ( $product_categories as $key => $category ) {
				if ( $category->count == 0 ) {
					unset( $product_categories[ $key ] );
				}
			}
		}

		if ( $number) {
			$product_categories = array_slice( $product_categories, 0, $number);
		}
		
		$design				= !empty($design) 					? $design 								: 'design-1';
		$slidestoshow 		= !empty($slidestoshow) 			? $slidestoshow 						: 3;
		$slidestoscroll 	= !empty($slidestoscroll) 			? $slidestoscroll 						: 1;
		$loop 				= ( $loop == 'false' ) 				? 'false' 								: 'true';
		$dots 				= ( $dots == 'false' ) 				? 'false' 								: 'true';
		$arrows 			= ( $arrows == 'false' ) 			? 'false' 								: 'true';
		$autoplay 			= ( $autoplay == 'false' ) 			? 'false' 								: 'true';
		$autoplay_interval 	= (!empty($autoplay_interval)) 		? $autoplay_interval 					: 3000;
		$speed 				= (!empty($speed)) 					? $speed 								: 300;
		$height 			= (!empty($height)) 				? $height 								: 300;
		
		$unique	= pcdfwoo_get_unique();
		// Slider configuration
		$slider_conf = compact('slidestoshow', 'slidestoscroll', 'loop', 'dots', 'arrows', 'autoplay', 'autoplay_interval', 'speed');
	
		ob_start();

		if ( $product_categories ) { ?>
			<div class="pcdfwoo-product-cat-wrp  <?php echo $design; ?>">
				<div class="pcdfwoo-product-cat-slider" id="pcdfwoo-<?php echo $unique; ?>">
					<?php foreach ( $product_categories as $category ) {
						 $cat_thumb_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true );
						 $cat_thumb_url = wp_get_attachment_image_src( $cat_thumb_id, 'shop_catalog' );
						 $term_link = get_term_link( $category, 'product_cat' );
						$cat_thumb_link = $cat_thumb_url[0];	?>
					
					<div class="pcdfwoo-product-slider">
						<div class="pcdfwoo-medium-12 pcdfwoo-columns">
						<div class="pcdfwoo-product-cat_inner" style="height:<?php echo $height; ?>px;">
							<a href="<?php echo $term_link; ?>">
								<?php if(!empty($cat_thumb_link)) { ?>
								<img  src="<?php echo $cat_thumb_link; ?>"  alt="<?php echo $category->name; ?>" />
								<?php } else { 
								echo wc_placeholder_img();
								 } ?>
								<div class="pcdfwoo_title"><?php echo $category->name; ?> <span class="pcdfwoo_count"><?php echo $category->count; ?> </span></div>
							</a>
							</div>
						</div>
					</div>
						
					<?php } ?>
					
				</div>
				<div class="pcdfwoo-slider-conf"><?php echo json_encode( $slider_conf ); ?></div><!-- end of .wpsisac-slider-conf -->
			</div>
		<?php	
		}

		woocommerce_reset_loop();

		return '<div class="pcdfwoo_woocommerce_slider">' . ob_get_clean() . '</div>';
}

add_shortcode('wpos_product_categories_slider', 'pcdfwoo_product_categories_slider');