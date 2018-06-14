<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package sshop
 */

if ( ! function_exists( 'sshop_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function sshop_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	$posted_on = '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark"><span class="fa fa-calendar"></span> ' . $time_string . '</a>';

	$byline = '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '"><span class="fa fa-user"></span>' . esc_html( get_the_author() ) . '</a></span>';

	echo '<span class="posted-on">' . $posted_on . '</span><span class="byline published"> ' . $byline . '</span>'; // WPCS: XSS OK.

}
endif;

if ( ! function_exists( 'sshop_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function sshop_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'sshop' ) );
		if ( $categories_list && sshop_categorized_blog() ) {
			printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'sshop' ) . '</span>', $categories_list ); // WPCS: XSS OK.
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'sshop' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'sshop' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		/* translators: %s: post title */
		comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'sshop' ), array( 'span' => array( 'class' => array() ) ) ), get_the_title() ) );
		echo '</span>';
	}

	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'sshop' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link">',
		'</span>'
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function sshop_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'sshop_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'sshop_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so sshop_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so sshop_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in sshop_categorized_blog.
 */
function sshop_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'sshop_categories' );
}
add_action( 'edit_category', 'sshop_category_transient_flusher' );
add_action( 'save_post',     'sshop_category_transient_flusher' );


function sshop_display_main_title( $title = '', $desc = '' ){
    if ( $title || $desc ) {
        echo '<header class="page-header">';
        if ( $title ) {
            echo '<h1 class="page-title">'.$title.'</h1>';
        }
        if ( $desc ) {
            echo '<div class="page-description">'.$desc.'</div>';
        }
        echo '</header>';
    }
}

function sshop_main_content_title()
{
    if ( is_search() ) {
        sshop_display_main_title( sprintf( esc_html__( 'Search Results for: %s', 'sshop' ), '<span>' . get_search_query() . '</span>' ) );
        return;
    }

    if ( is_front_page() ) {
        // Default show latest post, not-front-page setup
       if ( ! is_active_sidebar( 'sidebar-home' ) ) {
           $title = __( 'Blog', 'sshop' );
           sshop_display_main_title( $title );
       }
        return ;
    }

    if ( is_home() ) {

        // Front page displays, Posts page selected
        sshop_display_main_title( get_the_title( get_option('page_for_posts') ) );
        return ;
    }

    if ( is_page() ) {
        if ( get_post_meta( get_the_ID(), '_sshop_page_title', true ) == 'hide' ) {
            return ;
        }
        sshop_display_main_title( get_the_title( ) );
    }

    if ( is_category() || is_search() || is_archive() || is_tag() ) {
        sshop_display_main_title( get_the_archive_title(), get_the_archive_description() );
        return;
    }



}
add_action( 'sshop_before_main_content', 'sshop_main_content_title', 10 );


/**
 * Filter the except length to 20 words.
 *
 * @param int $length Excerpt length.
 * @return int (Maybe) modified excerpt length.
 */
function sshop_custom_excerpt_length( $length ) {
    return 30;
}
add_filter( 'excerpt_length', 'sshop_custom_excerpt_length', 20 );


/**
 * Filter the excerpt "read more" string.
 *
 * @param string $more "Read more" excerpt string.
 * @return string (Maybe) modified "read more" excerpt string.
 */
function sshop_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'sshop_excerpt_more' );



function sshop_site_brand(){
    ?>
    <div id="site-branding" class="site-branding">
        <?php
        if ( function_exists( 'the_custom_logo' ) ) {
            the_custom_logo();
        }
        if ( is_front_page() && is_home() ) : ?>
            <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
        <?php else : ?>
            <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
            <?php
        endif;
       ?>
    </div><!-- .site-branding -->
    <?php
}


function sshop_nav_right(){
    ?>
    <nav id="site-navigation-right" class="right-navigation" role="navigation">

        <div class="header-shop__icon search-icon has-dropdown">
            <a href="#">
                <span class="shop__icon fa fa-search"></span>
            </a>
            <div class="header-dropdown">
                <form class="top-search-form header-dropdown-inner" >
                    <div class="input-group">
                        <input type="text" class="form-control" name="s" placeholder="<?php esc_attr_e( 'Search...' ,'sshop' ); ?>." aria-describedby="basic-addon2">
                        <button class="input-group-addon btn" type="submit"><?php echo esc_html_x( 'Search', 'Search form button' ,'sshop' ); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ( function_exists( 'yith_wcwl_count_all_products' ) ) { ?>
            <div class="header-shop__icon">
                <a href="<?php echo esc_url( YITH_WCWL()->get_wishlist_url() ); ?>">
                    <?php $n = yith_wcwl_count_all_products(); ?>
                    <span class="shop__number wishlist-number <?php echo ( $n > 0 ) ? 'show' : '' ?>"><?php echo $n; ?></span>
                    <span class="shop__icon fa fa-heart-o"></span>
                </a>
            </div>
        <?php } ?>
        <?php if ( function_exists( 'wc_get_checkout_url' ) ) { ?>
            <div class="header-shop__icon has-dropdown">
                <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
                    <span class="shop__number cart-number-items">0</span>
                    <span class="shop__icon fa fa-shopping-cart"></span>
                </a>
                <div class="header-dropdown">
                    <div class="header-dropdown-inner">
                        <div class="widget_shopping_cart_content"></div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if ( function_exists( 'wc_get_page_permalink' ) ) { ?>
            <div class="header-shop__icon has-dropdown">
                <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
                    <span class="shop__icon fa fa-user-circle-o"></span>
                </a>

                <div class="header-dropdown account-menu">
                    <div class="header-dropdown-inner">
                        <?php if ( is_user_logged_in() ) { ?>
                        <ul class="menu">
                            <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) { ?>
                                <li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
                                    <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                        <?php } else { ?>
                            <a class="btn btn-secondary" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Login', 'sshop' ); ?></a>
                        <?php } ?>
                    </div>
                </div>


            </div>
        <?php } ?>
    </nav><!-- #site-navigation-right -->
    <?php
}

function sshop_nav(){
    ?>
    <nav id="site-navigation" class="main-navigation no-js" role="navigation">
        <a href="#" class="menu-toggle"><span class="icon fa fa-bars"></span><span class="label"><?php esc_html_e( 'Navigation', 'sshop' ); ?></span></a>
        <?php wp_nav_menu( array(
            'theme_location'    => 'menu-1',
            'menu_id'           => 'primary-menu',
            'fallback_cb'       => 'sshop_nav_fallback_cb',
            'container_class'   => 'primary-menu-wrapper',
            'container_id'      => 'primary-menu-wrapper',
        ) ); ?>
    </nav><!-- #site-navigation -->
    <?php
}
function sshop_nav_fallback_cb(){
    if ( current_user_can( 'edit_theme_options' ) ) {
        ?>
        <div id="primary-menu-wrapper" class="primary-menu-wrapper">
            <ul id="primary-menu" class="menu">
                <li><a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Add Menu', 'sshop' ); ?></a></li>
            </ul>
        </div>
        <?php
    }
}

add_action( 'sshop_header', 'sshop_site_brand', 10 );
add_action( 'sshop_header', 'sshop_nav_right', 15 );
add_action( 'sshop_header', 'sshop_nav', 20 );


function sshop_paging(){
    the_posts_pagination( array(
        'before_page_number' => '',
        'prev_text'    => '&larr;',
        'next_text'    => '&rarr;',
    ) );
}