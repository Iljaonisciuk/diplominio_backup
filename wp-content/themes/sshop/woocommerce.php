<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package sshop
 */

get_header();

$has_sidebar = is_active_sidebar( 'sidebar-woocommerce' );
$has_sidebar = apply_filters( 'sshop_layout_has_sidebar', $has_sidebar );
global $sshop_shop_layout_colums;
$sshop_shop_layout_colums = apply_filters( 'sshop_shop_layout_colums', 4, $has_sidebar );
?>
	<div id="primary" class="woocommerce-main content-area <?php echo  ( $has_sidebar ) ? 'has-sidebar' : 'no-sidebar'; ?>">
        <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
            <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
        <?php endif; ?>
        <?php

        /**
         * woocommerce_before_main_content hook.
         *
         * @hooked woocommerce_breadcrumb - 20
         * @hooked WC_Structured_Data::generate_website_data() - 30
         */
        do_action( 'woocommerce_before_main_content' );

        if ( ! is_singular( 'product' ) ) {
            do_action('woocommerce_archive_description');
        }
        ?>
		<main id="main" class="site-main" role="main">
			<?php
            woocommerce_content();
			?>
		</main><!-- #main -->
        <?php
        if ( $has_sidebar ) {
            get_sidebar( 'woocommerce');
        }
        ?>
        <?php
        /**
         * woocommerce_after_main_content hook.
         *
         */
        do_action( 'woocommerce_after_main_content' );
        ?>
	</div><!-- #primary -->

<?php

get_footer();
