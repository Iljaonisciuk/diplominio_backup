<?php

/**
 * Enqueue Admin Scripts
 */
function sshop_media_scripts( $hook ) {

    // Color picker Style
    wp_enqueue_style( 'wp-color-picker' );

    // Update CSS within in Admin
    wp_enqueue_style( 'sshop-widgets', get_template_directory_uri() . '/inc/widgets/widgets.css' );

    wp_enqueue_media();
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_script( 'sshop-media-upload-js', get_template_directory_uri() . '/inc/widgets/widgets.js', array( 'jquery' ), '', true );

}
add_action( 'admin_enqueue_scripts', 'sshop_media_scripts' );



// Register widgets
function sshop_register_widgets() {
    if( class_exists( 'SShop_Widget_Slider' ) ) {
        register_widget( 'SShop_Widget_Slider' );
    }
    register_widget( 'SShop_Widget_Blog' );

    if ( class_exists( 'WooCommerce' ) ) {
        register_widget('SShop_Widget_Product_Tabs');
        register_widget('SShop_Widget_Brand_Products');
        register_widget('SShop_Widget_Product_Categories');
        register_widget('SShop_Widget_Products');
        register_widget('SShop_Widget_Sale_Countdown_Products');
    }

}
add_action( 'widgets_init', 'sshop_register_widgets' );


class SShop_Widget_Classes{
    function __construct(){
        /**
         * @see $params = apply_filters( 'dynamic_sidebar_params', $params );
         */
        add_filter( 'dynamic_sidebar_params', array( $this, 'add_classes' ) );

        /**
         * see do_action_ref_array( 'in_widget_form', array( &$this, &$return, $instance ) );
         */
        add_action( 'in_widget_form', array( $this, 'add_settings' ), 35, 3 );
        add_filter( 'widget_update_callback', array( $this, 'update_widget' ), 10, 2 );
    }
    function update_widget( $instance, $new_instance ){

        if ( isset( $new_instance['css_classes'] ) ) {
            $instance['css_classes'] = sanitize_text_field( $new_instance[ 'css_classes' ] );
        }

        if ( isset( $new_instance['css_id'] ) ) {
            $instance['css_id'] = sanitize_title( $new_instance[ 'css_id' ] );
        }

        return $instance;

    }

    function add_settings( $widget, $return, $instance ){
        $instance = wp_parse_args( $instance, array(
            'css_classes' => '',
            'css_style' => '',
            'css_id' => ''
        ) );

        /*
        $build_in_style = apply_filters( 'sshop_widget_build_in_style', array(
            'none' => esc_html__( 'Default', 'sshop' ),
            'white-bg' => esc_html__( 'White Background', 'sshop' ),
        ) );
        ?>
        <?php if ( ! empty( $build_in_style ) && is_array( $build_in_style ) ) { ?>
        <p class="custom-css-build-in">
            <label for="<?php echo $widget->get_field_id( 'css_style' ); ?>"><?php esc_html_e( 'Widget Style:', 'sshop' ); ?></label><br/>
            <select id="<?php echo $widget->get_field_id( 'css_style' ); ?>" name="<?php echo $widget->get_field_name( 'css_style' ); ?>">
                <?php foreach ( $build_in_style as $k => $v ) { ?>
                <option <?php selected( $instance['css_style'], 'white-bg' ); ?> value="<?php echo esc_attr( $k ); ?>"><?php echo $v; ?></option>
                <?php } ?>
            </select>
        </p>
        <?php }
        */
        ?>
        <p class="custom-css-classes">
            <label for="<?php echo $widget->get_field_id( 'css_classes' ); ?>"><?php esc_html_e( 'CSS Classes:', 'sshop' ); ?></label><br/>
            <input class="widefat" type="text" id="<?php echo $widget->get_field_id( 'css_classes' ); ?>" value="<?php echo esc_attr( $instance['css_classes'] ); ?>" name="<?php echo $widget->get_field_name( 'css_classes' ); ?>" />
        </p>
        <p class="custom-css-id">
            <label for="<?php echo $widget->get_field_id( 'css_id' ); ?>"><?php esc_html_e( 'CSS ID:', 'sshop' ); ?></label><br/>
            <input class="widefat" type="text" id="<?php echo $widget->get_field_id( 'classes' ); ?>" value="<?php echo esc_attr( $instance['css_id'] ); ?>" name="<?php echo $widget->get_field_name( 'css_id' ); ?>" />
        </p>
        <?php
    }

    function add_classes( $params ){
        if ( is_admin() ) {

        }

        /*
        if ( $params['id'] != 'sidebar-home' ) {
            return $params;
        }
        */

        global $wp_registered_widgets, $wp_registered_sidebars , $widget_number;

        if ( ! isset( $params[0] ) ) {
            return $params;
        }

        //$arr_registered_widgets = wp_get_sidebars_widgets(); // Get an array of ALL registered widgets
        $sidebar_id     = $params[0]['id']; // Get the id for the current sidebar we're processing
        $widget_id      = $params[0]['widget_id'];
        $widget_obj     = $wp_registered_widgets[$widget_id];
        $widget_index   = $widget_obj['params'][0]['number'];
        $widget_opt     = null;
        $widget_opt     = get_option( $widget_obj['callback'][0]->option_name );

        // Get registered sidebars
        $sidebar = $wp_registered_sidebars[ $sidebar_id ];

        $settings = $widget_opt[$widget_index];

        if ( isset( $settings['css_id'] ) && sanitize_title( $settings['css_id'] ) != '' ) {
            $_id = sanitize_title( $settings['css_id'] );
        } else {
            $_id = $widget_id;
        }

        if ( isset( $settings['css_classes'] ) ) {
            $_custom_class = sanitize_text_field( $settings['css_classes'] );
        } else {
            $_custom_class = $widget_id;
        }

        $classes = array();

        if ( isset( $settings['css_style'] ) ) {
            $d = sanitize_text_field( $settings['css_style'] );
            if ( $d ) {
                $classes[] = $d;
            }
        }

        if ( isset( $widget_obj['classname'] ) ) {
            $classes[] = $widget_obj['classname'];
        }

        $classes[] =  $widget_obj['callback'][0]->option_name;
        if ( $_custom_class ) {
            $classes[] = $_custom_class;
        }


        $classes = join( ' ', $classes );
        $params[0]['before_widget'] = sprintf( $sidebar['before_widget'], $_id, $classes );
        if ( strpos( $classes, 'full-width' ) !== false ) {
            $params[0]['before_widget'] .= '<div class="container-fluid widget-full-width-content">';
            $params[0]['after_widget'] = '</div>'.$params[0]['after_widget'];
        }

        return $params;
    }
}

new SShop_Widget_Classes();