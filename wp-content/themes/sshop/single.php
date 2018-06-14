<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package sshop
 */

get_header();

$has_sidebar = is_active_sidebar( 'sidebar-1' );
$has_sidebar = apply_filters( 'sshop_layout_has_sidebar', $has_sidebar );
?>

    <div id="primary" class="content-area <?php echo  ( $has_sidebar ) ? 'has-sidebar' : 'no-sidebar'; ?>">
		<main id="main" class="site-main" role="main">

		<?php
		while ( have_posts() ) : the_post();

			get_template_part( 'template-parts/content', get_post_format() );

			the_post_navigation();

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>
		</main><!-- #main -->
        <?php
        if ( $has_sidebar ) {
            get_sidebar();
        }
        ?>
	</div><!-- #primary -->

<?php
get_footer();
