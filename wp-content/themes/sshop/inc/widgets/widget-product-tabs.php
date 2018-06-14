<?php
/**
 * News list Layout 1.
 */
class SShop_Widget_Product_Tabs extends SShop_Widget_Base {

    public $layout = 1;
    public $tax = 'product_cat';
    public $post_type = 'product';

    public function __construct() {
        parent::__construct(
            'sshop_products_tabs',
            esc_html__( 'FRONT PAGE: Product Tabs', 'sshop' ),
            array(
                'description'   => esc_html__( 'Display products as tabs layout, recommended for front page', 'sshop' )
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
                'default' => '10',
                'label' => esc_html__( 'No. of Posts', 'sshop' ),
            ),

            array(
                'type' =>'select',
                'name' => 'layout',
                'default' => '4',
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

            array(
                'type' =>'checkbox',
                'name' => 'show_all',
                'default' => 'on',
                'label' => esc_html__( 'Show All Filter', 'sshop' ),
            )

        );


        return $fields;

    }

    public function widget( $args, $instance )
    {

        if ( ! isset( $instance['__setup_data'] ) || ! $instance['__setup_data'] === false ){
            $instance = $this->setup_instance( $instance );
        }

        $title = $instance['title'];
        unset($instance['title']);

        $category = $instance['category'];
        $show_all = ( isset( $instance['show_all'] ) ) ? $instance['show_all'] : '';

        if ( ! is_array( $category ) ) {
            $category = ( array ) $category;
        }
        $category = array_filter( $category );

        if ( ! $instance['layout'] ) {
            $instance['layout'] = 4;
        }

        $query = $this->get_products( $instance );
        $instance['_layout'] = $this->layout;
        $instance['wid'] = get_class( $this );

        $classes = array( 'widget-tabs', 'widget-tabs-list-' . $this->layout );

        if ( $this->layout_class ) {
            $classes[] = $this->layout_class;
        }

        echo $args['before_widget'];
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        ?>
        <div class="<?php echo esc_attr( join( ' ', $classes ) ); ?>">

            <div class="layout-tabs tabs-layout-wrap" data-ajax="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" data-number="<?php echo esc_attr( $instance['layout'] ); ?>" data-instance="<?php echo esc_attr(json_encode($instance)); ?>">
                <?php if ( ! empty( $title ) ) { ?>
                    <div class="filter-inside">
                        <?php
                        if ( $title ) {
                            echo $args['before_title'].$title.$args['after_title'];
                        }
                        ?>
                        <?php if ( count( $category ) > 1 ){ ?>
                            <ul class="nav-tabs-filter">
                                <?php if ( $show_all == 'on') { ?>
                                    <li class="show-all"><a data-term-id="<?php echo esc_attr(join($category, ',')); ?>" href="#"><?php esc_html_e('All', 'sshop'); ?></a></li>
                                <?php } ?>
                                <?php foreach ($category as $t) {
                                    $term = get_term( $t, $this->tax );
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
                <div class="tabs-layout-contents no-b">
                    <?php
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