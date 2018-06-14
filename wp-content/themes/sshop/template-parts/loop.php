<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package sshop
 */

?>

<article <?php post_class( 'loop-post '. ( has_post_thumbnail() ? 'has-thumbnail' : 'no-thumbnail' )  ); ?>>

    <?php
    if ( has_post_thumbnail() ) {
        echo '<a href="'.esc_url( get_permalink() ).'" class="enter-thumbnail">';
        the_post_thumbnail( 'large' );
        echo '</a>';
    }
    ?>

	<div class="enter-content-w">
        <?php
        the_title( '<h2 class="entry-title"><a title="'. the_title_attribute( array( 'echo' => false ) ).'" href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        ?>
        <div class="entry-meta">
            <?php
            sshop_posted_on();
            ?>
        </div><!-- .entry-meta -->

        <div class="entry-excerpt">
            <?php
            the_excerpt( );
            ?>
        </div><!-- .entry-content -->

        <a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>" type="button" class="entry-more-btn "><?php esc_html_e( 'Read more', 'sshop' ); ?> <i class="fa fa-long-arrow-right"></i></a>

    </div>
</article><!-- #post-## -->
