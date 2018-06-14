<?php
/**
 * Widget Product for font home page
 */
class SShop_Widget_Sale_Countdown_Products extends SShop_Widget_Base {

    public $layout = 1;
    public $tax = 'product_cat';
    public $post_type = 'product';

    public function __construct() {
        parent::__construct(
            'sshop_sales_countdown_products',
            esc_html__( 'FRONT PAGE: Sales Countdown Products', 'sshop' ),
            array(
                'description'   => esc_html__( 'Display sales countdown products, recommended for front page', 'sshop' )
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
                'type' =>'text',
                'name' => 'number',
                'default' => 4,
                'label' => esc_html__( 'No. of products', 'sshop' ),
            ),

            array(
                'type' =>'text',
                'name' => 'layout',
                'default' => 4,
                'label' => esc_html__( 'Items per row', 'sshop' ),
            ),

        );

        return $fields;

    }

    public function form( $instance ) {
        ?>
        <div class="w-fields">
            <?php
            $this->render_fields( $instance );
            ?>
        </div>
        <p class="description">
            <?php esc_html_e( 'Set sale price dates to show sales countdown products', 'sshop' ); ?>
        </p>
        <?php
    }


    public function widget( $args, $instance )
    {

        if ( ! isset( $instance['__setup_data'] ) || ! $instance['__setup_data'] === false ){
            $instance = $this->setup_instance( $instance );
        }

        $title = $instance['title'];
        unset($instance['title']);
        echo $args['before_widget'];
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
        $number = absint( $instance['number'] );
        if ( ! $number ) {
            return ;
        }
        global $wpdb;
        $current = current_time( 'timestamp' );
        $sql = "
        SELECT
            p.ID,
            p.post_parent,
            pm.meta_value AS end_time
        FROM {$wpdb->postmeta} AS pm LEFT JOIN {$wpdb->posts} AS p ON pm.post_id = p.ID
        WHERE
          p.post_type IN( 'product', 'product_variation' )
          AND p.post_status IN( 'publish' )
          AND pm.meta_key = '_sale_price_dates_to'
          AND CAST(pm.meta_value AS UNSIGNED)  > $current
        ORDER BY end_time ASC
        LIMIT 0, 50";
        $rows = $wpdb->get_results( $sql );

        if ( ! $rows || empty ( $rows ) ) {
            return ;
        }

        $end_times = array();
        $product_ids =  array();
        foreach ( $rows as $r ) {
            $id = $r->post_parent > 0 ? $r->post_parent : $r->ID;
            $t = $r->end_time;
            if ( isset( $end_times[ $id ] ) ) {
                $t = $t > $end_times[ $id ] ? $t : $end_times[ $id ];
            }

            $end_times[ $id ] = $t;
            $product_ids[ $id ] = $id;
        }
        $query = new WP_Query( array(
            'post_type' => 'product',
            'post__in'  => $product_ids,
            'orderby'   => 'post__in',
            'posts_per_page'   => $number
        ) );

        if ( $query->have_posts() ) {
            if ($title) {
                echo $args['before_title'] . $title . $args['after_title'];
            }

            $col = $instance['layout'];
            if (!$col || $col > 12) {
                $col = 5;
            }

            $GLOBALS['sshop_sales_countdown_product'] = true;
            ?>
            <div class="grid-products">
                <ul class="products eq-row-col-<?php echo esc_attr($col); ?>">
                    <?php
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $GLOBALS['sshop_wc_product_sale_end'] = isset ( $end_times[ get_the_ID() ] ) ? $end_times[ get_the_ID() ] : false;
                        wc_get_template_part('content', 'product');
                    }
                    unset( $GLOBALS['sshop_wc_product_sale_end'] );
                    wp_reset_postdata();
                    ?>
                </ul>
            </div>
            <?php
            $GLOBALS['sshop_sales_countdown_product'] = false;
            echo $args['after_widget'];
        }
    }


}