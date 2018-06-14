
<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package sshop
 */

?>

	</div><!-- #content -->

<?php if ( is_active_sidebar( 'sidebar-footer' ) ) { ?>
<div class="footer-widgets">
    <div class="container-fluid">
        <div class="row">
            <?php dynamic_sidebar('sidebar-footer'); ?>
        </div>
    </div>
</div>
<?php } ?>
<footer id="colophon" class="site-footer" role="contentinfo"> <p class="footer_text"> © 2017 UAB "Silke plius". Be UAB "Silke plius" sutikimo draudžiama kopijuoti ir platinti svetainėje esančią informaciją. </p>
		<div class="container-fluid">
            
        </div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-71278359-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-71278359-3');
</script>


</body>
</html>
