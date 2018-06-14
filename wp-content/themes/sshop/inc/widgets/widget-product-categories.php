<?php

class SShop_Widget_Product_Categories extends SShop_Widget_Base {

    public $tax = 'product_cat';
    public $post_type = 'product';

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {

        parent::__construct(
            'sshop_product_categories',
            esc_html__( 'FRONT PAGE: Product Categories', 'sshop' ),
            array(
                'classname' => 'widget-shop-categories',
                'description'   => esc_html__( 'Display your product categories, Recommended for front page.', 'sshop' )
            )
        );

    }

    function get_configs( ){
        $fields = array(
            array(
                'type' =>'text',
                'name' => 'title',
                'label' => esc_html__( 'Title:', 'sshop' ),
            ),
            array(
                'type' =>'list_cat',
                'name' => 'category',
                'label' => esc_html__( 'Categories:', 'sshop' ),
            ),

            array(
                'type' =>'text',
                'name' => 'columns',
                'default' => 4,
                'label' => esc_html__( 'Number columns:', 'sshop' ),
            ),

        );

        return $fields;

    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $settings = array() ) {

        $instance = $this->setup_instance( $settings );

        $category = $instance['category'];
        if ( ! is_array( $category ) ) {
            $category = ( array ) $category;
        }

        if ( ! $instance['columns'] ) {
            $instance['columns'] = 4;
        }
        echo $args['before_widget'];
        $instance['title'] = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
        if ( $instance['title'] ) {
            echo $args['before_title'].$instance['title'].$args['after_title'];
        }

        if ( ! empty( $category ) ) {
            $terms = get_terms( array(
                'taxonomy'      => $this->tax,
                'include'       => $category,
                'hierarchical'  => false,
                'orderby'       => 'include'
            ) );
        } else {
            $terms = get_terms( array( 'taxonomy' => $this->tax, 'orderby' => 'count', 'order' => 'desc' ) );
        }

        ?>
        <ul class="list-shop-cats eq-row-col-<?php echo esc_attr( $instance['columns'] ); ?>">
            <?php foreach ( $terms as $t ) {

                $image_id = get_term_meta( $t->term_id, 'thumbnail_id', true );
                $image = wp_get_attachment_url( $image_id, 'medium' );
                ?>
                <li class="eq-col top-lv-1">
                    <div class="top-p-cat <?php echo ( $image ) ? 'has-image' : 'no-img'; ?>" <?php echo ( $image ) ? ' style="background-image: url('.esc_url( $image ).')"' : ''; ?>>
                       <div class="inner">
                        <span class="cat-name"><?php echo esc_html( $t->name ); ?></span>
                        <a class="cat-link" href="<?php echo get_term_link( $t ); ?>"><?php esc_html_e( 'Shop now', 'sshop' ); ?></a>
                       </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
        <?php
        echo $args['after_widget'];
    }

}
