<?php

/**
 * Register a meta box using a class.
 */
class SShop_Custom_Meta_Box {

    /**
     * Constructor.
     */
    public function __construct() {
        if ( is_admin() ) {
            add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
            add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
        }

    }

    /**
     * Meta box initialization.
     */
    public function init_metabox() {
        add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );
        add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );
    }

    /**
     * Adds the meta box.
     */
    public function add_metabox() {
        add_meta_box(
            'sshop_page_settings',
            esc_html__( 'Page Settings', 'sshop' ),
            array( $this, 'render_metabox' ),
            'page',
            'side',
            'low'
        );

    }

    /**
     * Renders the meta box.
     */
    public function render_metabox( $post ) {
        // Add nonce for security and authentication.
        wp_nonce_field( 'sshop_metabox_nonce', 'sshop_metabox_nonce' );

        $show_options = array(
            'default' => esc_html__( 'Default', 'sshop' ),
            'hide' => esc_html__( 'Hide', 'sshop' ),
            'show' => esc_html__( 'Show', 'sshop' ),
        );
        ?>
        <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="_sshop_page_title"><?php esc_html_e( 'Show page title', 'sshop' ); ?></label></p>
        <select name="_sshop_page_title" id="_sshop_page_title">
            <?php foreach ( $show_options as $k => $v ) { ?>
                <option <?php selected( get_post_meta( $post->ID, '_sshop_page_title', true ), $k ); ?> value="<?php echo esc_attr( $k ); ?>"><?php echo $v; ?></option>
            <?php } ?>
        </select>
        <?php

        $margins = array(
            'default' => esc_html__( 'Default', 'sshop' ),
            'no' => esc_html__( 'No margin', 'sshop' ),
        );
        ?>
        <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="_sshop_page_margin"><?php esc_html_e( 'Content margin', 'sshop' ); ?></label></p>
        <select name="_sshop_page_margin" id="_sshop_page_margin">
            <?php foreach ( $margins as $k => $v ) { ?>
                <option <?php selected( get_post_meta( $post->ID, '_sshop_page_margin', true ), $k ); ?> value="<?php echo esc_attr( $k ); ?>"><?php echo $v; ?></option>
            <?php } ?>
        </select>
        <?php
    }

    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save_metabox( $post_id, $post ) {
        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['sshop_metabox_nonce'] ) ? sanitize_text_field( $_POST['sshop_metabox_nonce'] ) : '';
        $nonce_action = 'sshop_metabox_nonce';

        // Check if nonce is set.
        if ( ! isset( $nonce_name ) ) {
            return;
        }

        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }

        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }

        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }


        $v = isset( $_POST['_sshop_page_title'] ) ? sanitize_text_field( $_POST['_sshop_page_title'] ) : '';
        update_post_meta( $post_id, '_sshop_page_title', $v );

        $v = isset( $_POST['_sshop_page_margin'] ) ? sanitize_text_field( $_POST['_sshop_page_margin'] ) : '';
        update_post_meta( $post_id, '_sshop_page_margin', $v );


    }
}

if ( is_admin() ) {
    new SShop_Custom_Meta_Box();
}

