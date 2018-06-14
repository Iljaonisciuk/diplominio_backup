<?php
/**
 * News list Layout 1.
 */
class SShop_Widget_Brand_Products extends SShop_Widget_Base {

    public $layout = 1;
    public $tax = 'pwb-brand';
    public $post_type = 'product';

    public function __construct() {
        parent::__construct(
            'sshop_products_brand',
            esc_html__( 'FRONT PAGE: Product Brands', 'sshop' ),
            array(
                'description'   => esc_html__( 'Display brand and products as tabs, recommended for front page.', 'sshop' )
            )
        );
    }

    function get_configs( ){
        $fields = array(
            array(
                'type' =>'text',
                'name' => 'title',
                'label' => esc_html__( 'Title', 'sshop' ),
            ),
            array(
                'type' =>'list_cat',
                'name' => 'category',
                'label' => esc_html__( 'Categories', 'sshop' ),
            ),

            array(
                'type' =>'text',
                'name' => 'number',
                'label' => esc_html__( 'No. of Posts', 'sshop' ),
            ),

            array(
                'type' =>'select',
                'name' => 'layout',
                'default' => '3',
                'label' => esc_html__( 'Number products visible', 'sshop' ),
                'options' => array(
                    '2'   => 2,
                    '3'   => 3,
                    '4'   => 4,
                    '5'   => 5,
                    '6'   => 6,
                ),
            ),

            array(
                'type' =>'select',
                'name' => 'orderby',
                'label' => esc_html__( 'Orderby', 'sshop' ),
                'options' => array(
                    'date'   => esc_html__( 'Date', 'sshop' ),
                    'price'  => esc_html__( 'Price', 'sshop' ),
                    'rand'   => esc_html__( 'Random', 'sshop' ),
                    'sales'  => esc_html__( 'Sales', 'sshop' ),
                ),
            ),

            array(
                'type' =>'order',
                'name' => 'order',
                'label' => esc_html__( 'Order', 'sshop' ),
            ),

        );

        $fields[] =  array(
            'type' =>'checkbox',
            'name' => 'show_paging',
            'label' => esc_html__( 'Show Ajax Paging', 'sshop' ),
            'default' => 'on'
        );


        $fields[] =  array(
            'type' =>'checkbox',
            'name' => 'show_brand_info',
            'label' => esc_html__( 'Show banner', 'sshop' ),
            'default' => 'on'
        );


        return $fields;

    }

    public function widget( $args, $settings )
    {


        $args = wp_parse_args( $args, array(
            'name'          => '',
            'id'            => '',
            'description'   => '',
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '',
            'after_title'   => '',
        ) );

        if ( ! isset( $settings['__setup_data'] ) || ! $settings['__setup_data'] === false ){
            $instance = $this->setup_instance( $settings );
        } else {
            $instance = $settings;
        }

        $title = $instance['title'];
        unset($instance['title']);

        $category = $instance['category'];
        if ( ! is_array( $category ) ) {
            $category = ( array ) $category;
        }

        $category = array_filter( $category );

        $terms = get_terms( array(
            'taxonomy' => $this->tax,
            'include' => empty( $category ) ? null: $category,
            'orderby' =>  empty( $category ) ? null: 'include',
        ) );

        if ( empty( $terms ) ) {
            return ;
        }

        if ( ! $instance['layout'] ) {
            $instance['layout'] = 3;
        }

        $query = $this->get_products( $instance );
        $instance['_layout'] = $this->layout;
        $instance['wid'] = get_class( $this );

        $classes = array( 'widget-tabs', 'widget-tabs-list-' . $this->layout );

        if ( $this->layout_class ) {
            $classes[] = $this->layout_class;
        }

        echo $args['before_widget'];



        $first_term = current( $terms );
        reset( $terms );

        ?>
        <div class="<?php echo esc_attr( join( ' ', $classes ) ); ?>">
            <div class="layout-tabs tabs-layout-wrap" data-number="<?php echo esc_attr( $instance['layout'] ); ?>" data-ajax="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-instance="<?php echo esc_attr(json_encode($instance)); ?>">
                <?php if ( ! empty( $title ) ) { ?>
                    <div class="filter-inside">
                        <?php
                        if ( $title ) {
                            echo $args['before_title'].$title.$args['after_title'];
                        }
                        ?>
                        <?php if ( count( $terms ) > 1 ){ ?>
                            <ul class="nav-tabs-filter">
                                <?php foreach ($terms as $term) {
                                    if ( $term ) {
                                        ?>
                                        <li><a data-term-id="<?php echo esc_attr($term->term_id); ?>" href="<?php echo get_term_link($term) ?>"><?php echo esc_html($term->name); ?></a></li>
                                        <?php
                                    }
                                } ?>
                                <li class="subfilter-more">
                                    <a class="a-more" href="#"><?php esc_html_e('More', 'sshop'); ?> <i class="fa fa-angle-down"></i></a>
                                    <ul class="sub-filters"></ul>
                                </li>
                            </ul>
                        <?php } ?>

                        <div class="tab-item-actions">
                            <span class="slider-prev fa fa-angle-left"></span>
                            <span class="slider-next fa fa-angle-right"></span>
                        </div>

                    </div>
                <?php } ?>


                <div class="tabs-layout-contents <?php echo ( $instance['show_brand_info'] == 'on' ) ? 'has-b' : 'no-b'; ?>">
                    <?php if ( $instance['show_brand_info'] == 'on' ) { ?>
                        <div class="tabs-intro">
                            <?php
                            if ( is_array( $this->viewing ) ) {
                                $brand = current( $this->viewing );
                            } else {
                                $brand = $this->viewing;
                            }

                            if ( ! $brand ) {
                                $brand = $first_term;
                            }

                            $term = get_term( $brand , $this->tax );
                            if ( $term && ! is_wp_error( $term  ) ) {
                                $image_id = get_term_meta( $term->term_id, 'pwb_brand_image', true );
                                $image = wp_get_attachment_url( $image_id );
                                ?>
                                <?php if ( $image ) { ?>
                                    <div class="brand-logo"><img src="<?php echo esc_attr( $image ) ; ?>" alt="<?php echo esc_attr( $term->name ); ?>"></div>
                                <?php } else { ?>
                                    <h3 class="brand-name"><?php echo esc_html( $term->name ); ?></h3>
                                <?php } ?>
                                <?php
                                if ( $term->description ) {
                                    ?>
                                    <div class="brand-desc"><?php echo wp_kses_post( $term->description ); ?></div>
                                    <?php
                                }
                                ?>
                                <a class="btn-shop-brand btn btn-secondary " href="<?php echo get_term_link( $term ) ?>"><?php esc_html_e( 'Shop this brand', 'sshop' ); ?></a>
                                <?php

                            }
                            ?>
                        </div>
                        <?php
                    }
                    echo '<div class="tabs-layout-content animate tabs-layout'.$this->layout.'">';
                    echo '<div class="tabs-content-items-wrapper">';
                    if ( $query->have_posts() ) {
                        if (method_exists($this, 'layout_' . $this->layout)) {
                            $this->{'layout_' . $this->layout}($query);
                        } else {
                            $this->layout_content($query);
                        }
                    } else {
                        $this->not_found();
                    }

                    echo '</div>';
                    echo '</div>';

                    wp_reset_postdata();
                    ?>
                </div>


            </div>
        </div>
        <?php
        echo $args['after_widget'];
    }


}