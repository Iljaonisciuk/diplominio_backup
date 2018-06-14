<?php
/**
 * The HOME template file
 *
 * Template Name: Home - Width Sidebar Content
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

get_header(); ?>

<div id="primary" class="content-area front-page">

    <div class="home-content-sidebar sidebar">
        <?php
        if ( is_active_sidebar('sidebar-home') ) {
            dynamic_sidebar('sidebar-home');
        } else {
            ?>
            <div class="alert alert-info" role="alert">
                <?php
                _e( "Almost Done! just add some widget to sidebar Front Page: Content", 'sshop' );
                ?>
            </div>
            <?php
        }
        ?>
    </div>

</div><!-- #primary -->
<?php
get_footer();
