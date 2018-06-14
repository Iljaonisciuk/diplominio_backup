<?php
/**
 * sshop Theme Customizer
 *
 * @package sshop
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function sshop_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';


    $wp_customize->add_panel( 'theme_options', array(
        'priority'       => 36,
        //'capability'     => 'edit_theme_options',
        'title'          => esc_html__( 'Theme Options', 'sshop' ),
        'description'    => '',
    ) );

    $wp_customize->add_section( 'header', array(
        'priority'       => 36,
        'title'          => esc_html__( 'Header', 'sshop' ),
        'description'    => '',
        'panel'          => 'theme_options',
    ) );

    $wp_customize->add_setting( 'header_sticky',
        array(
            'default'           => 1,
            'sanitize_callback'	=> 'sshop_sanitize_checkbox',
        )
    );
    $wp_customize->add_control( 'header_sticky',
        array(
            'label' 		=> esc_html__( 'Header sticky', 'sshop' ),
            'type'			=> 'checkbox',
            'section' 		=> 'header',
        )
    );

    $wp_customize->add_section( 'layout', array(
        'priority'       => 36,
        'title'          => esc_html__( 'Layout', 'sshop' ),
        'description'    => '',
        'panel'          => 'theme_options',
    ) );

    $wp_customize->add_setting( 'layout',
        array(
            'default'           => 'right-sidebar',
            'sanitize_callback'	=> 'sshop_sanitize_select',
        )
    );
    $wp_customize->add_control( 'layout',
        array(
            'label' 		=> esc_html__( 'Site layout', 'sshop' ),
            'type'			=> 'select',
            'choices' 		=> array(
                'right-sidebar' => esc_html__( 'Right Sidebar', 'sshop' ),
                'left-sidebar'  => esc_html__( 'Left Sidebar', 'sshop' ),
                'none'          => esc_html__( 'No Sidebar', 'sshop' ),
            ),
            'section' 		=> 'layout',
        )
    );

}
add_action( 'customize_register', 'sshop_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function sshop_customize_preview_js() {
	wp_enqueue_script( 'sshop_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'sshop_customize_preview_js' );


function sshop_sanitize_select( $input, $setting ) {
    $input = sanitize_key( $input );
    $choices = $setting->manager->get_control( $setting->id )->choices;
    return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

function sshop_sanitize_checkbox( $input ){
    if ( $input == 1 || $input == 'true' || $input === true ) {
        return 1;
    } else {
        return 0;
    }
}

