<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package sshop
 */

get_header();

$has_sidebar = is_active_sidebar( 'sidebar-1' );
$has_sidebar = apply_filters( 'sshop_layout_has_sidebar', $has_sidebar );
?>

    <div id="primary" class="content-area <?php echo  ( $has_sidebar ) ? 'has-sidebar' : 'no-sidebar'; ?>">
        <?php
        /**
         * @hooked sshop_main_content_title - 10
         */
        do_action( 'sshop_before_main_content' ) ;
        ?>
		<main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) : ?>
			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'template-parts/loop' );

			endwhile;

            // Previous/next page navigation.
            sshop_paging();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->
        <?php
        if ( $has_sidebar ) {
            get_sidebar();
        }
        ?>
        <?php do_action( 'sshop_after_main_content' ); ?>
	</div><!-- #primary -->

<?php
get_footer();
