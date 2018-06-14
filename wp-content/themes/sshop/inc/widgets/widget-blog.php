<?php

class SShop_Widget_Blog extends WP_Widget {

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        $widget_ops = array(
            'classname' => 'widget-blog',
            'description' => esc_html__( 'Display blog posts, recommended for front page', 'sshop' ),
        );
        parent::__construct( 'sshop_blog', esc_html__( 'FRONT PAGE: Blog' , 'sshop' ), $widget_ops );
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
        // outputs the content of the widget

        $instance = wp_parse_args( $instance,  array(
            'title' => '',
            'layout' => 3,
            'number' => 3,
        ) );

        echo $args['before_widget'];

        $title = $instance['title'];
        unset($instance['title']);
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        if ( $title ) {
            echo $args['before_title'].$title.$args['after_title'];
        }

        $posts = get_posts( array(
            'numberposts' => $instance['number']
        ) );
        global $post;
        ?>
        <div class="blog-wrapper">
            <div class="blog-posts eq-row-col-<?php echo esc_attr( $instance['layout'] ); ?>">

                <?php foreach ( $posts as $post ) { setup_postdata( $post ); ?>
                    <div class="eq-col">

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
                                the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
                                ?>

                                <div class="entry-excerpt">
                                    <?php
                                    the_excerpt( );
                                    ?>
                                </div><!-- .entry-content -->

                                <div class="entry-meta">
                                    <?php
                                    sshop_posted_on();
                                    ?>
                                </div><!-- .entry-content -->

                            </div>
                        </article><!-- #post-## -->


                    </div>
                <?php } ?>

            </div>
        </div>
        <?php
        wp_reset_postdata();
        echo $args['after_widget'];
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {

        $instance = wp_parse_args( $instance,  array(
            'title' => '',
            'layout' => 3,
            'number' => 3,
        ) );

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title', 'sshop' ); ?></label><br/>
            <input class="widefat" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number posts to show', 'sshop' ); ?></label><br/>
            <input class="widefat" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'layout' ); ?>"><?php  esc_html_e( 'Number items per row', 'sshop'); ?></label><br/>
            <input class="widefat" type="text" value="<?php echo esc_attr( $instance['layout'] ); ?>" id="<?php echo $this->get_field_id( 'layout' ); ?>" name="<?php echo $this->get_field_name( 'layout' ); ?>">
        </p>
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

        $new_instance['title']      = sanitize_text_field( $new_instance[ 'title' ] );
        $new_instance['number']     = absint( $new_instance[ 'number' ] );
        $new_instance['layout']     = absint( $new_instance[ 'layout' ] );

        return $new_instance;
    }
}
