<?php
/**
 * The full width template file
 *
 * Template Name: Full Width
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package sshop
 */


get_header();

$has_sidebar = false;
?>

	<div id="primary" class="content-area front-page <?php echo  ( $has_sidebar ) ? 'has-sidebar' : 'no-sidebar'; ?>">
        <?php
        /**
         * @hooked sshop_main_content_title - 10
         * @see sshop_main_content_title
         */
        do_action( 'sshop_before_main_content' ) ;
        ?>
        <?php
        while ( have_posts() ) : the_post();

            get_template_part( 'template-parts/content', 'page' );
            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;

        endwhile; // End of the loop.
        ?>
        <?php do_action( 'sshop_after_main_content' ); ?>
	</div><!-- #primary -->

<?php
get_footer();
