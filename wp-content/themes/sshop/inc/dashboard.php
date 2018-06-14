<?php
/**
 * Add theme dashboard page
 */

if ( ! function_exists( 'sshop_admin_scripts' ) ) {
    /**
     * Enqueue scripts for admin page only: Theme info page
     */
    function sshop_admin_scripts($hook)
    {
        if ($hook === 'widgets.php' || $hook === 'appearance_page_ft_sshop') {
            wp_enqueue_style('sshop-admin-css', get_template_directory_uri() . '/assets/css/admin.css');
            // Add recommend plugin css
            wp_enqueue_style('plugin-install');
            wp_enqueue_script('plugin-install');
            wp_enqueue_script('updates');
            add_thickbox();
        }
    }
}
add_action( 'admin_enqueue_scripts', 'sshop_admin_scripts' );

add_action('admin_menu', 'sshop_theme_info');
function sshop_theme_info() {
    $menu_title = esc_html__('SShop Theme', 'sshop');
    add_theme_page( esc_html__( 'SShop Dashboard', 'sshop' ), $menu_title, 'edit_theme_options', 'sshop', 'sshop_theme_info_page');
}



function sshop_theme_info_page() {

    $theme_data = wp_get_theme('sshop');
    $template_slug = get_option( 'template' );
    // Check for current viewing tab
    $tab = null;
    if ( isset( $_GET['tab'] ) ) {
        $tab = sanitize_text_field( $_GET['tab'] );
    } else {
        $tab = null;
    }
    ?>
    <div class="wrap about-wrap theme_info_wrapper">
        <h1><?php printf(esc_html__('Welcome to SShop - Version %1s', 'sshop'), $theme_data->Version ); ?></h1>
        <div class="about-text"><?php esc_html_e( 'SShop is a flexible, clean, simple responsive WordPress theme, perfect for any store website.', 'sshop' ); ?></div>
        <h2 class="nav-tab-wrapper">
            <a href="?page=sshop" class="nav-tab<?php echo is_null($tab) ? ' nav-tab-active' : null; ?>"><?php echo $theme_data->Name; ?></a>
            <?php do_action( 'sshop_admin_more_tabs' ); ?>
        </h2>

        <?php if ( is_null( $tab ) ) { ?>
            <div class="theme_info info-tab-content">
                <div class="theme_info_column clearfix">
                    <div class="theme_info_left">

                        <div class="theme_link">
                            <h3><?php esc_html_e( 'Theme Customizer', 'sshop' ); ?></h3>
                            <p class="about"><?php printf(esc_html__('%s supports the Theme Customizer for all theme settings. Click "Customize" to start customize your site.', 'sshop'), $theme_data->Name); ?></p>
                            <p>
                                <a href="<?php echo admin_url('customize.php'); ?>" class="button button-primary"><?php esc_html_e('Start Customize', 'sshop'); ?></a>
                            </p>
                        </div>
                        <div class="theme_link">
                            <h3><?php esc_html_e( 'Theme Documentation', 'sshop' ); ?></h3>
                            <p class="about"><?php printf(esc_html__('Need any help to setup and configure %s? Please have a look at our documentations instructions.', 'sshop'), $theme_data->Name); ?></p>
                            <p>
                                <a href="<?php echo esc_url( 'http://sshopwp.com' ); ?>" target="_blank" class="button button-secondary"><?php esc_html_e('sshop Documentation', 'sshop'); ?></a>
                            </p>
                            <?php do_action( 'sshop_dashboard_theme_links' ); ?>
                        </div>
                        <div class="theme_link">
                            <h3><?php esc_html_e( 'Having Trouble, Need Support?', 'sshop' ); ?></h3>
                            <p class="about"><?php printf(esc_html__('Support for %s WordPress theme is conducted through SShopWP support ticket system.', 'sshop'), $theme_data->Name); ?></p>
                            <p>
                                <a href="<?php echo esc_url('http://sshopwp.com' ); ?>" target="_blank" class="button button-secondary"><?php echo sprintf( esc_html__('Create a support ticket', 'sshop'), $theme_data->Name); ?></a>
                            </p>
                        </div>
                    </div>

                    <div class="theme_info_right">
                        <img src="<?php echo get_template_directory_uri(); ?>/screenshot.png" alt="<?php esc_attr_e( 'Theme Screenshot', 'sshop' ); ?>" />
                    </div>
                </div>
            </div>
        <?php } ?>


        <?php do_action( 'sshop_more_tabs_details' ); ?>

    </div> <!-- END .theme_info -->
    <?php
}
