<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package sshop
 */

if ( ! is_active_sidebar( 'sidebar-1' ) || get_theme_mod( 'layout', 'right-sidebar' ) == 'none' ) {
	return;
}

?>
<aside id="secondary" class="widget-area " role="complementary">
    <div class="boxed-widget">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
    </div>
</aside><!-- #secondary -->
