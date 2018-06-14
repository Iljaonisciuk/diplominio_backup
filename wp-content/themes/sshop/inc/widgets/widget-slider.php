<?php

class SShop_Widget_Slider extends WP_Widget {

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        $widget_ops = array(
            'classname' => 'widget-slider',
            'description' => esc_html__( 'Display your slider, recommended for front page', 'sshop' ),
        );
        parent::__construct( 'sshop_slider', esc_html__( 'FRONT PAGE: Slider' , 'sshop' ), $widget_ops );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {

        if ( ! function_exists( 'slide_anything_shortcode' ) ) {
            return ;
        }

        // outputs the content of the widget
        $instance = wp_parse_args( $instance, array(
            'slider_id' => ''
        ) );

        if ( $instance['slider_id'] ) {
            $slider = get_post( $instance['slider_id'] );
        }

        if ( ! $slider || get_post_type( $slider ) != 'sa_slider' ) {
            $sliders = get_posts( array(
                'post_type' => 'sa_slider',
                'posts_per_page' => 1,
            ) );
            if ( $sliders ) {
                $slider = current(  $sliders );
            }
        }

        if ( $slider ) {
            echo $args['before_widget'];
            ?>
            <div class="slider-wrapper">
                <?php
                echo slide_anything_shortcode( array( 'id' => $slider->ID ) );
                ?>
            </div>
            <?php
            echo $args['after_widget'];
        }

    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
        // outputs the options form on admin

        $sliders = get_posts( array(
            'post_type' => 'sa_slider',
            'posts_per_page' => -1,
        ) );

        $instance = wp_parse_args( $instance, array(
            'slider_id' => ''
        ) );
        ?>
        <div class="w-admin-input-wrap">
            <label for="<?php echo $this->get_field_id( 'slider_id' ); ?>"><?php esc_html_e( 'Slider', 'sshop' ); ?></label>

            <select id="<?php echo $this->get_field_id( 'slider_id' ); ?>" name="<?php echo $this->get_field_name( 'slider_id' ); ?>">
                <?php foreach ( ( array ) $sliders as  $p ) { ?>
                    <option <?php selected( $instance['slider_id'], $p->ID ); ?> value="<?php echo esc_attr( $p->ID ); ?>"><?php echo esc_html( $p->post_title ); ?></option>
                <?php } ?>
            </select>

        </div><!-- .w-admin-input-wrap -->
        <?php
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     */
    public function update( $new_instance, $old_instance ) {
        // processes widget options to be saved
        $new_instance['slider_id'] = absint( $new_instance['slider_id'] );
        return $new_instance;
    }
}
