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

$has_sidebar = is_active_sidebar( 'sidebar-1' );
$has_sidebar = apply_filters( 'sshop_layout_has_sidebar', $has_sidebar );
?>
    <div id="primary" class="content-area <?php echo  ( $has_sidebar ) ? 'has-sidebar' : 'no-sidebar'; ?>">
        <?php
        /**
         * @hooked sshop_main_content_title - 10
         * @see sshop_main_content_title
         */
        do_action( 'sshop_before_main_content' );
        ?>
        <main id="main" class="site-main" role="main">
            <?php
            while ( have_posts() ) : the_post();

                get_template_part( 'template-parts/loop' );

                // If comments are open or we have at least one comment, load up the comment template.
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;

            endwhile; // End of the loop.

            // Previous/next page navigation.
            sshop_paging();

            ?>

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
