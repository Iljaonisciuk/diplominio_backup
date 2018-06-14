<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package sshop
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function sshop_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

    $classes[] = 'woocommerce';

    if ( is_page() ) {
        global $post;
        $m = get_post_meta( $post->ID, '_sshop_page_margin', true );
        if ( $m == 'no' ) {
            $classes[] = 'page-no-margin';
        }
    }

    $classes[] = 'site-layout-'.esc_attr( get_theme_mod( 'layout', 'right-sidebar' ) );

	return $classes;
}
add_filter( 'body_class', 'sshop_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function sshop_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'sshop_pingback_header' );


add_filter( 'sshop_layout_has_sidebar', 'sshop_layout_sidebar' );

function sshop_layout_sidebar(  $active_sidebar ){
    if ( get_theme_mod( 'layout', 'right-sidebar' ) == 'none' ) {
        return false;
    }
    return $active_sidebar;
}

if ( ! function_exists( 'storefront_display_comments' ) ) {
    /**
     * Storefront display comments
     *
     * @since  1.0.0
     */
    function storefront_display_comments() {
        // If comments are open or we have at least one comment, load up the comment template.
        if ( comments_open() || '0' != get_comments_number() ) :
            comments_template();
        endif;
    }
}

if ( ! function_exists( 'sshop_comment' ) ) {
    /**
     * SShop comment template
     *
     * @param array $comment the comment array.
     * @param array $args the comment args.
     * @param int   $depth the comment depth.
     * @since 1.0.0
     */
    function sshop_comment( $comment, $args, $depth ) {
        if ( 'div' == $args['style'] ) {
            $tag = 'div';
            $add_below = 'comment';
        } else {
            $tag = 'li';
            $add_below = 'div-comment';
        }
        ?>
        <<?php echo esc_attr( $tag ); ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
        <div class="comment-body">

        <div class="comment-meta">
            <div class="comment-author vcard">
                <?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
                <div class="commentmetadata">
                    <?php printf( wp_kses_post( '<cite class="fn">%s</cite>', 'sshop' ), get_comment_author_link() ); ?>
                    <a class="comment-date" href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><time pubdate datetime="<?php comment_time( 'c' ); ?>">
                            <?php
                            /* translators: 1: date, 2: time */
                            printf( __( '%1$s at %2$s', 'sshop' ), get_comment_date(), get_comment_time() ); ?>
                        </time>
                    </a>
                </div>
            </div>

        </div>

        <?php if ( '0' == $comment->comment_approved ) : ?>
            <em class="comment-awaiting-moderation"><?php esc_attr_e( 'Your comment is awaiting moderation.', 'sshop' ); ?></em>
            <br />
        <?php endif; ?>

        <?php if ( 'div' != $args['style'] ) : ?>
        <div id="div-comment-<?php comment_ID() ?>" class="comment-content">
    <?php endif; ?>
        <div class="comment-text">
            <?php comment_text(); ?>
            <div class="reply">
                <?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'], 'after' => ' <i class="fa fa-reply"></i>' ) ) ); ?>
                <?php edit_comment_link( __( 'Edit', 'sshop' ), '  ', '' ); ?>
            </div>
        </div>

        </div>
        <?php if ( 'div' != $args['style'] ) : ?>
            </div>
        <?php endif; ?>
        <?php
    }
}
