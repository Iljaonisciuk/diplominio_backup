<?php

/**
 * Plugin Name: Slider WD
 * Plugin URI: https://web-dorado.com/products/wordpress-slider-plugin.html
 * Description: This is a responsive plugin, which allows adding sliders to your posts/pages and to custom location. It uses large number of transition effects and supports various types of layers.
 * Version: 1.2.2
 * Author: WebDorado
 * Author URI: https://web-dorado.com/wordpress-plugins-bundle.html
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

define('WD_S_NAME', plugin_basename(dirname(__FILE__))); 
define('WD_S_DIR', WP_PLUGIN_DIR . "/" . WD_S_NAME);
define('WD_S_URL', plugins_url(WD_S_NAME));
define('FAKE_SITE_URL', '@#$%');
define('FAKE_SITE_URL_BTNS', '@##$%');
define('WD_S_PREFIX', 'wds');
define('WD_S_NICENAME', __( 'Slider WD', WD_S_PREFIX ));
define('WD_S_NONCE', 'nonce_wd');

define('WD_S_DB_VERSION', '1.2.2');
define('WD_S_VERSION', '1.2.2');

define('WD_S_FREE', TRUE);

function wds_use_home_url() {
  $home_url = str_replace("http://", "", home_url());
  $home_url = str_replace("https://", "", $home_url);
  $pos = strpos($home_url, "/");
  if ($pos) {
    $home_url = substr($home_url, 0, $pos);
  }
  $site_url = str_replace("http://", "", WD_S_URL);
  $site_url = str_replace("https://", "", $site_url);
  $pos = strpos($site_url, "/");
  if ($pos) {
    $site_url = substr($site_url, 0, $pos);
  }
  return $site_url != $home_url;
}

if (wds_use_home_url()) {
  define('WD_S_FRONT_URL', home_url("wp-content/plugins/" . plugin_basename(dirname(__FILE__))));
}
else {
  define('WD_S_FRONT_URL', WD_S_URL);
}

$upload_dir = wp_upload_dir();
$WD_S_UPLOAD_DIR = str_replace(ABSPATH, '', $upload_dir['basedir']) . '/slider-wd';

// Plugin menu.
function wds_options_panel() {
  $parent_slug = WD_S_FREE ? null : 'sliders_wds';
  if( !WD_S_FREE || get_option( "wds_subscribe_done" ) == 1 ) {
    add_menu_page(__('Slider WD', 'wds'), __('Slider WD', 'wds'), 'manage_options', 'sliders_wds', 'wd_sliders', WD_S_URL . '/images/wd_slider.png');
    $parent_slug = "sliders_wds";
  }

  $sliders_page = add_submenu_page($parent_slug, __('Sliders', WD_S_PREFIX), __('Sliders', WD_S_PREFIX), 'manage_options', 'sliders_wds', 'wd_sliders');
  add_action('admin_print_styles-' . $sliders_page, 'wds_styles');
  add_action('admin_print_scripts-' . $sliders_page, 'wds_scripts');

  $global_options_page = add_submenu_page($parent_slug, __('Options', WD_S_PREFIX), __('Options', WD_S_PREFIX), 'manage_options', 'goptions_wds', 'wd_sliders');
  add_action('admin_print_styles-' . $global_options_page, 'wds_styles');
  add_action('admin_print_scripts-' . $global_options_page, 'wds_scripts');

  if ( WD_S_FREE ) {
    add_submenu_page($parent_slug, __('Get Pro', 'wds'), __('Get Pro', 'wds'), 'manage_options', 'licensing_wds', 'wds_licensing');
  }

  $demo_slider = add_submenu_page($parent_slug, __('Import', 'wds'), __('Import', 'wds'), 'manage_options', 'demo_sliders_wds', 'wds_demo_sliders');
  add_action('admin_print_scripts-' . $demo_slider, 'wds_scripts');
  add_action('admin_print_styles-' . $demo_slider, 'wds_styles');

  $uninstall_page = add_submenu_page(null, __('Uninstall', WD_S_PREFIX), __('Uninstall', WD_S_PREFIX), 'manage_options', 'uninstall_wds', 'wd_sliders');
  add_action('admin_print_styles-' . $uninstall_page, 'wds_styles');
  add_action('admin_print_scripts-' . $uninstall_page, 'wds_scripts');
}
add_action('admin_menu', 'wds_options_panel');

function wds_licensing() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  wp_register_style('wds_licensing', WD_S_URL . '/licensing/style.css', array(), WD_S_VERSION);
  wp_print_styles('wds_licensing');
  require_once(WD_S_DIR . '/licensing/licensing.php');
}

function wd_sliders() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_S_DIR . '/framework/WDW_S_Library.php');
  $page = WDW_S_Library::get('page');
  if (($page != '') && (($page == 'sliders_wds') || ($page == 'uninstall_wds') || ($page == 'WDSShortcode') || ($page == 'goptions_wds'))) {
    require_once(WD_S_DIR . '/admin/controllers/WDSController' . (($page == 'WDSShortcode') ? $page : ucfirst(strtolower($page))) . '.php');
    $controller_class = 'WDSController' . ucfirst(strtolower($page));
    $controller = new $controller_class();
    $controller->execute();
  }
}

function wds_demo_sliders() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_S_DIR . '/demo_sliders/demo_sliders.php');
  wp_register_style('wds_demo_sliders', WD_S_URL . '/demo_sliders/style.css', array(), WD_S_VERSION);
  wp_print_styles('wds_demo_sliders');
  spider_demo_sliders();
}

function wds_frontend() {
  require_once(WD_S_DIR . '/framework/WDW_S_Library.php');
  $page = WDW_S_Library::get('action');
  if (($page != '') && ($page == 'WDSShare')) {
    require_once(WD_S_DIR . '/frontend/controllers/WDSController' . ucfirst($page) . '.php');
    $controller_class = 'WDSController' . ucfirst($page);
    $controller = new $controller_class();
    $controller->execute();
  }
}

if ( !WD_S_FREE ) {
  add_action('wp_ajax_WDSShare', 'wds_frontend');
  add_action('wp_ajax_nopriv_WDSShare', 'wds_frontend');
}

function wds_ajax() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_S_DIR . '/framework/WDW_S_Library.php');
  $page = WDW_S_Library::get('action');
  if ($page != '' && (($page == 'WDSShortcode') || ($page == 'WDSPosts') || ($page == 'WDSExport') || ($page == 'WDSImport'))) {
    require_once(WD_S_DIR . '/admin/controllers/WDSController' . ucfirst($page) . '.php');
    $controller_class = 'WDSController' . ucfirst($page);
    $controller = new $controller_class();
    $controller->execute();
  }
}

function wds_shortcode($params) {
  if ( is_admin() || isset($_GET['elementor-preview'])) {
    // return ob_get_clean();
    return __('Preview unavailable', 'wds');
   }
  else {
    $params = shortcode_atts(array('id' => (isset($_GET['slider_id']) ?  (int) $_GET['slider_id'] : 0)), $params);
    ob_start();
    wds_front_end($params['id']);
    return str_replace(array("\r\n", "\n", "\r"), '', ob_get_clean());
  }
}
add_shortcode('wds', 'wds_shortcode');
add_shortcode('SliderPreview', 'wds_shortcode');

function wd_slider($id) {
  echo wds_front_end($id);
}
$wds = 0;
function wds_front_end($id, $from_shortcode = 1) {
  require_once(WD_S_DIR . '/frontend/controllers/WDSControllerSlider.php');
  $controller = new WDSControllerSlider();
  global $wds;
  $controller->execute($id, $from_shortcode, $wds);
  $wds++;
  return;
}

function wds_media_button($context) {
  global $pagenow;
  if (in_array($pagenow, array('post.php', 'page.php', 'post-new.php', 'post-edit.php', 'admin-ajax.php'))) {
    $context .= '
      <a onclick="tb_click.call(this); wds_thickDims(); return false;" href="' . add_query_arg(array('action' => 'WDSShortcode', 'TB_iframe' => '1'), admin_url('admin-ajax.php')) . '" class="wds_thickbox button" style="padding-left: 0.4em;" title="Select slider">
        <span class="wp-media-buttons-icon wds_media_button_icon" style="vertical-align: text-bottom; background: url(' . WD_S_URL . '/images/wd_slider.png) no-repeat scroll left top rgba(0, 0, 0, 0);"></span>
        Add Slider WD
      </a>';
  }
  return $context;
}
add_filter('media_buttons_context', 'wds_media_button');

// Add the Slider button to editor.
add_action('wp_ajax_WDSShortcode', 'wds_ajax');
add_action('wp_ajax_WDSPosts', 'wds_ajax');
if ( !WD_S_FREE ) {
  add_action('wp_ajax_WDSExport', 'wds_ajax');
  add_action('wp_ajax_WDSImport', 'wds_ajax');
}

function wds_admin_ajax() {
  ?>
  <script>
    var wds_thickDims, wds_tbWidth, wds_tbHeight;
    wds_tbWidth = 400;
    wds_tbHeight = 200;
    wds_thickDims = function() {
      var tbWindow = jQuery('#TB_window'), H = jQuery(window).height(), W = jQuery(window).width(), w, h;
      w = (wds_tbWidth && wds_tbWidth < W - 90) ? wds_tbWidth : W - 40;
      h = (wds_tbHeight && wds_tbHeight < H - 60) ? wds_tbHeight : H - 40;
      if (tbWindow.size()) {
        tbWindow.width(w).height(h);
        jQuery('#TB_iframeContent').width(w).height(h - 27);
        tbWindow.css({'margin-left': '-' + parseInt((w / 2),10) + 'px'});
        if (typeof document.body.style.maxWidth != 'undefined') {
          tbWindow.css({'top':(H-h)/2,'margin-top':'0'});
        }
      }
    };
  </script>
  <?php
}
add_action('admin_head', 'wds_admin_ajax');

// Add images to Slider.
add_action('wp_ajax_wds_UploadHandler', 'wds_UploadHandler');
add_action('wp_ajax_addImage', 'wds_filemanager_ajax');

// Upload.
function wds_UploadHandler() {
  require_once(WD_S_DIR . '/framework/WDW_S_Library.php');
  WDW_S_Library::verify_nonce('wds_UploadHandler');
  require_once(WD_S_DIR . '/filemanager/UploadHandler.php');
}

function wds_filemanager_ajax() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_S_DIR . '/framework/WDW_S_Library.php');
  $page = WDW_S_Library::get('action');
  $tab = WDW_S_Library::get('tab');

//  $query_url = wp_nonce_url($query_url, 'addImage', WD_S_NONCE);
  if ( (($page != '') && (($page == 'addImage') || ($page == 'addMusic')))
   || $tab == 'wds_custom_uploader' ) {
    if ( $tab != 'wds_custom_uploader' ) {
      WDW_S_Library::verify_nonce($page);
    }
    require_once(WD_S_DIR . '/filemanager/controller.php');
    $controller_class = 'FilemanagerController';
    $controller = new $controller_class();
    $addImages_ajax = WDW_S_Library::get('addImages_ajax');
    if ($addImages_ajax == 'addImages_ajax') {
      $load_count = WDW_S_Library::get('load_count');
      $images_list = $controller->get_images(intval($load_count));
      echo (json_encode($images_list, true));
      die;
    }
    else {
      $controller->execute(true, 1);
    }
  }
}
// Slider Widget.
if (class_exists('WP_Widget')) {
  require_once(WD_S_DIR . '/admin/controllers/WDSControllerWidgetSlideshow.php');
  add_action('widgets_init', create_function('', 'return register_widget("WDSControllerWidgetSlideshow");'));
}

// Activate plugin.
function wds_activate() {
  delete_transient('wds_update_check');
  wds_install();
}
register_activation_hook(__FILE__, 'wds_activate');

function wds_install() {
  $version = get_option("wds_version");
  $new_version = WD_S_DB_VERSION;
  if ($version && version_compare($version, $new_version, '<')) {
    require_once WD_S_DIR . "/sliders-update.php";
    wds_update($version);
    update_option("wds_version", $new_version);
  }
  elseif (!$version) {
    require_once WD_S_DIR . "/sliders-insert.php";
    wds_insert();
    add_option("wds_version", $new_version, '', 'no');
    add_option("wds_version_1.0.46", 1, '', 'no');
    if ( WD_S_FREE ) {
      add_option("wds_theme_version", '1.0.0', '', 'no');
    }
  }
}
if ((!isset($_GET['action']) || $_GET['action'] != 'deactivate')
  && (!isset($_GET['page']) || $_GET['page'] != 'uninstall_wds')) {
  add_action('admin_init', 'wds_install');
}

// Plugin styles.
function wds_styles() {
  wp_admin_css('thickbox');
  wp_enqueue_style(WD_S_PREFIX . '_tables');
  wp_enqueue_style('wds_tables_640', WD_S_URL . '/css/wds_tables_640.css', array(), WD_S_VERSION);
  wp_enqueue_style('wds_tables_320', WD_S_URL . '/css/wds_tables_320.css', array(), WD_S_VERSION);
  require_once(WD_S_DIR . '/framework/WDW_S_Library.php');
  $google_fonts = WDW_S_Library::get_google_fonts();
  for ($i = 0; $i < count($google_fonts); $i = $i + 150) {
    $fonts = array_slice($google_fonts, $i, 150);
    $query = implode("|", str_replace(' ', '+', $fonts));
    $url = 'https://fonts.googleapis.com/css?family=' . $query . '&subset=greek,latin,greek-ext,vietnamese,cyrillic-ext,latin-ext,cyrillic';
    wp_enqueue_style('wds_googlefonts_' . $i, $url, null, null);
  }
  wp_enqueue_style('wds_deactivate-css',  WD_S_URL . '/wd/assets/css/deactivate_popup.css', array(), WD_S_VERSION);
}

function wds_global_options_defults() {
  $global_options = array(
    'default_layer_fweight'          => 'normal',
    'default_layer_start'            => 1000,
    'default_layer_effect_in'        => 'none',
    'default_layer_duration_eff_in'  => 1000,
    'default_layer_infinite_in'      => 1,
    'default_layer_end'              => 3000,
    'default_layer_effect_out'       => 'none',
    'default_layer_duration_eff_out' => 1000,
    'default_layer_infinite_out'     => 1,
    'default_layer_add_class'        => '',
    'default_layer_ffamily'          => 'arial',
    'default_layer_google_fonts'     => 0,
    'loading_gif'                    => 0,
    'register_scripts'               => 0,
    'spider_uploader'                => 0,
    'possib_add_ffamily'             => '',
    'possib_add_ffamily_google'      => '',
  );
  return $global_options;
}

// Plugin scripts.
function wds_scripts() {
  $wds_global_options = get_option("wds_global_options", 0);
  $global_options = json_decode($wds_global_options);
  if (!$wds_global_options) {
    $wds_global_options = wds_global_options_defults();
  }
  wp_enqueue_media();
  wp_enqueue_script('thickbox');
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-sortable');
  wp_enqueue_script('jquery-ui-draggable');
  wp_enqueue_script('jquery-ui-tooltip');
  wp_enqueue_script(WD_S_PREFIX . '_admin');
  wp_enqueue_script('jscolor', WD_S_URL . '/js/jscolor/jscolor.js', array(), '1.3.9');
  wp_enqueue_style('wds_font-awesome', WD_S_URL . '/css/font-awesome/font-awesome.css', array(), '4.6.3');
  wp_enqueue_style('wds_effects', WD_S_URL . '/css/wds_effects.css', array(), WD_S_VERSION);
  if ( !WD_S_FREE ) {
    wp_enqueue_script('wds_hotspot', WD_S_URL . '/js/wds_hotspot.js', array(), WD_S_VERSION);
    wp_enqueue_script('wds_embed', WD_S_URL . '/js/wds_embed.js', array(), WD_S_VERSION);
  }
  require_once(WD_S_DIR . '/framework/WDW_S_Library.php');  
  wp_localize_script('wds_admin', 'wds_object', array(
    "GGF" => WDW_S_Library::get_google_fonts(),
    "FGF" => WDW_S_Library::get_font_families(),
    "LDO" => $global_options,
    "is_free" => WD_S_FREE,
	  'translate' => array(
      'check_at_least' => __('You must check at least one item.', WD_S_PREFIX),
      'no_slider' => __('There is no slider.', WD_S_PREFIX),
      'min_size' => __('Sets the minimal size of the text. It will be shrunk until the font size is equal to this value.', WD_S_PREFIX),
      'font_size' => __('Size:', WD_S_PREFIX),
      'please_enter_url_to_embed' => __('Please enter url to embed.', WD_S_PREFIX),
      'error_cannot_get_response_from_the_server' => __('Error: cannot get response from the server.', WD_S_PREFIX),
      'error_something_wrong_happened_at_the_server' => __('Error: something wrong happened at the server.', WD_S_PREFIX),
      'edit_filmstrip_thumbnail' => __('Edit Filmstrip Thumbnail', WD_S_PREFIX),
      'you_must_set_watermark_type' => __('You must set watermark type.', WD_S_PREFIX),
      'watermark_succesfully_set' => __('Watermark Succesfully Set.', WD_S_PREFIX),
      'watermark_succesfully_reset' => __('Watermark Succesfully Reset.', WD_S_PREFIX),
      'items_succesfully_saved' => __('Items Succesfully Saved.', WD_S_PREFIX),
      'changes_made_in_this_table_should_be_saved' => __('Changes made in this table should be saved.', WD_S_PREFIX),
      'selected' => __('Selected', WD_S_PREFIX),
      'item' => __('item', WD_S_PREFIX),
      's' => __('s', WD_S_PREFIX),
      'you_must_select_an_image_file' => __('You must select an image file.', WD_S_PREFIX),
      'album_thumb_dimensions' => __('Album thumb dimensions:', WD_S_PREFIX),
      'album_thumb_width' => __('Album thumb width:', WD_S_PREFIX),
      'edit_thumbnail' => __('Edit Thumbnail', WD_S_PREFIX),
      'do_you_want_to_delete_layer' => __('Do you want to delete the layer?', WD_S_PREFIX),
      'drag_to_re_order' => __('Drag to re-order', WD_S_PREFIX),
      'layer' => __('Layer', WD_S_PREFIX),
      'delete_layer' => __('Delete layer', WD_S_PREFIX),
      'duplicate_layer' => __('Duplicate layer', WD_S_PREFIX),
      'text' => __('Text:', WD_S_PREFIX),
      'sample_text' => __('Sample text', WD_S_PREFIX),
      'leave_blank_to_keep_the_initial_width_and_height' => __('Leave blank to keep the initial width and height.', WD_S_PREFIX),
      'dimensions' => __('Dimensions:', WD_S_PREFIX),
      'break_word' => __('Break-word', WD_S_PREFIX),
      'edit_image' => __('Edit Image', WD_S_PREFIX),
      'set_the_html_attribute_specified_in_the_img_tag' => __('Set the value of alt HTML attribute for this image layer.', WD_S_PREFIX),
      'alt' => __('Alt:', WD_S_PREFIX),
      'use_http_and_https_for_external_links' => __('Use http:// and https:// for external links.', WD_S_PREFIX),
      'link' => __('Link:', WD_S_PREFIX),
      'open_in_a_new_window' => __('Open in a new window', WD_S_PREFIX),
      'in_addition_you_can_drag_and_drop_the_layerto_a_desired_position' => __('In addition, you can drag the layer and drop it to the desired position.', WD_S_PREFIX),
      'position' => __('Position:', WD_S_PREFIX),
      'published' => __('Published:', WD_S_PREFIX),
      'fixed_step_left_center_right' => __('Fixed step (left, center, right)', WD_S_PREFIX),
      'yes' => __('Yes', WD_S_PREFIX),
      'no' => __('No', WD_S_PREFIX),
      'color' => __('Color:', WD_S_PREFIX),
      'hover_color' => __('Hover Color', WD_S_PREFIX),
      'size' => __('Size:', WD_S_PREFIX),
      'font_family' => __('Font family:', WD_S_PREFIX),
      'google_fonts' => __('Google fonts', WD_S_PREFIX),
      'default' => __('Default', WD_S_PREFIX),
      'font_weight' => __('Font weight:', WD_S_PREFIX),
      'padding' => __('Padding:', WD_S_PREFIX),
      'value_must_be_between_0_to_100' => __('Value must be between 0 and 100.', WD_S_PREFIX),
      'transparent' => __('Transparency:', WD_S_PREFIX),
      'border' => __('Border:', WD_S_PREFIX),
      'use_css_type_values' => __('Use CSS type values.', WD_S_PREFIX),
      'use_css_type_values_e_g_10_10_5_888888' => __('Use CSS type values (e.g. 10px 10px 5px #888888).', WD_S_PREFIX),
      'shadow' => __('Shadow', WD_S_PREFIX),
      'dimensions' => __('Dimensions:', WD_S_PREFIX),
      'set_width_and_height_of_the_image' => __('Set width and height of the image.', WD_S_PREFIX),
      'set_width_and_height_of_the_video' => __('Set width and height of the video.', WD_S_PREFIX),
      'social_button' => __('Social button', WD_S_PREFIX),
      'effect_in' => __('Effect in:', WD_S_PREFIX),
      'effect_out' => __('Effect out:', WD_S_PREFIX),
      'start' => __('Start', WD_S_PREFIX),
      'effect' => __('Effect', WD_S_PREFIX),
      'duration' => __('Duration', WD_S_PREFIX),
      'iteration' => __('Iteration', WD_S_PREFIX),
      'autoplay' => __('Autoplay:', WD_S_PREFIX),
      'controls' => __('Controls:', WD_S_PREFIX),
      'hotspot_width' => __('Hotspot Width:', WD_S_PREFIX),
      'hotspot_background_color' => __('Hotspot Background Color:', WD_S_PREFIX),
      'hotspot_border' => __('Hotspot Border:', WD_S_PREFIX),
      'hotspot_radius' => __('Hotspot Radius:', WD_S_PREFIX),
      'in_addition_you_can_drag_and_drop_the_layer_to_a_desired_position' => __('In addition, you can drag the layer and drop it to the desired position.', WD_S_PREFIX),
      'leave_blank_to_keep_the_initial_width_and_height' => __('Leave blank to keep the initial width and height.', WD_S_PREFIX),
      'video_loop' => __('Video Loop', WD_S_PREFIX),
      'disable_youtube_related_video' => __('Disable youtube related video:', WD_S_PREFIX),
      'hotspot_animation' => __('Hotspot Animation:', WD_S_PREFIX),
      'add_click_action' => __('Add click action:', WD_S_PREFIX),
      'select_between_the_option_of_always_displaying_the_navigation_buttons_or_only_when_hovered' => __('Select between the option of always displaying the navigation buttons or only when hovered.', WD_S_PREFIX),
      'show_hotspot_text' => __('Show Hotspot text:', WD_S_PREFIX),
      'on_hover' => __('On hover', WD_S_PREFIX),
      'on_click' => __('On click', WD_S_PREFIX),
      'text_alignment' => __('Text alignment:', WD_S_PREFIX),
      'slides_name' => __('Slides name:', WD_S_PREFIX),
      'static_layer' => __('Static layer:', WD_S_PREFIX),
      'the_layer_will_be_visible_on_all_slides' => __('The layer will be visible on all slides.', WD_S_PREFIX),
      'add_edit_image' => __('Add/Edit Image', WD_S_PREFIX),
      'add_image_layer' => __('Add Image Layer', WD_S_PREFIX),
      'slide' => __('Slide', WD_S_PREFIX),
      'duplicate_slide' => __('Duplicate slide', WD_S_PREFIX),
      'delete_slide' => __('Delete slide', WD_S_PREFIX),
      'add_image_by_url' => __('Add Image by URL', WD_S_PREFIX),
      'embed_media' => __('Embed Media', WD_S_PREFIX),
      'add_post' => __('Add Post', WD_S_PREFIX),
      'delete' => __('Delete', WD_S_PREFIX),
      'youtube_related_video' => __('Youtube related video:', WD_S_PREFIX),
      'video_loop' => __('Video Loop:', WD_S_PREFIX),
      'you_can_set_a_redirection_link_so_that_the_user_will_get_to_the_mentioned_location_upon_hitting_the_slide_use_http_and_https_for_external_links' => __('You can add a URL, to which the users will be redirected upon clicking on the slide. Use http:// and https:// for external links.', WD_S_PREFIX),
      'link_the_slide_to' => __('Link the slide to:', WD_S_PREFIX),
      'add_text_layer' => __('Add Text Layer', WD_S_PREFIX),
      'add_video_layer' => __('Add Video Layer', WD_S_PREFIX),
      'embed_media_layer' => __('Embed Media Layer', WD_S_PREFIX),
      'add_social_buttons_layer' => __('Add Social Buttons Layer', WD_S_PREFIX),
      'add_hotspot_layer' => __('Add Hotspot Layer', WD_S_PREFIX),
      'do_you_want_to_delete_slide' => __('Do you want to delete slide?', WD_S_PREFIX),
      'sorry_you_are_not_allowed_to_upload_this_type_of_file' => __('Sorry, you are not allowed to upload this type of file.', WD_S_PREFIX),
      'you_must_select_at_least_one_item' => __('You must select at least one item.', WD_S_PREFIX),
      'do_you_want_to_delete_selected_items' => __('Do you want to delete selected items?', WD_S_PREFIX),
      'are_you_sure_you_want_to_reset_the_settings' => __('Are you sure you want to reset the settings?', WD_S_PREFIX),
      'choose' => __('Choose', WD_S_PREFIX),
      'choose_video' => __('Choose Video', WD_S_PREFIX),
      'choose_image' => __('Choose Image', WD_S_PREFIX),
      'insert' => __('Insert', WD_S_PREFIX),
      'add_class' => __('Add class:', WD_S_PREFIX),
      'radius' => __('Radius:', WD_S_PREFIX),
      'editor' => __('Editor', WD_S_PREFIX),
      'group' => __('Group', WD_S_PREFIX),
      'color' => __('Color', WD_S_PREFIX),
      'background_color' => __('Background Color:', WD_S_PREFIX),
      'none' => __('None', WD_S_PREFIX),
      'bounce' => __('Bounce', WD_S_PREFIX),
      'flash' => __('Flash', WD_S_PREFIX),
      'pulse' => __('Pulse', WD_S_PREFIX),
      'shake' => __('Shake', WD_S_PREFIX),
      'swing' => __('Swing', WD_S_PREFIX),
      'tada' => __('Tada', WD_S_PREFIX),
      'wobble' => __('Wobble', WD_S_PREFIX),
      'hinge' => __('Hinge', WD_S_PREFIX),
      'rubberBand' => __('RubberBand', WD_S_PREFIX),
      'lightSpeedIn' => __('LightSpeedIn', WD_S_PREFIX),
      'rollIn' => __('RollIn', WD_S_PREFIX),
      'bounceIn' => __('BounceIn', WD_S_PREFIX),
      'bounceInDown' => __('BounceInDown', WD_S_PREFIX),
      'bounceInLeft' => __('BounceInLeft', WD_S_PREFIX),
      'bounceInRight' => __('BounceInRight', WD_S_PREFIX),
      'bounceInUp' => __('BounceInUp', WD_S_PREFIX),
      'fadeIn' => __('FadeIn', WD_S_PREFIX),
      'fadeInDown' => __('FadeInDown', WD_S_PREFIX),
      'fadeInDownBig' => __('FadeInDownBig', WD_S_PREFIX),
      'fadeInLeft' => __('FadeInLeft', WD_S_PREFIX),
      'fadeInLeftBig' => __('FadeInLeftBig', WD_S_PREFIX),
      'fadeInRight' => __('FadeInRight', WD_S_PREFIX),
      'fadeInRightBig' => __('FadeInRightBig', WD_S_PREFIX),
      'fadeInUp' => __('FadeInUp', WD_S_PREFIX),
      'fadeInUpBig' => __('FadeInUpBig', WD_S_PREFIX),
      'flip' => __('Flip', WD_S_PREFIX),
      'flipInX' => __('FlipInX', WD_S_PREFIX),
      'flipInY' => __('FlipInY', WD_S_PREFIX),
      'rotateIn' => __('RotateIn', WD_S_PREFIX),
      'rotateInDownLeft' => __('RotateInDownLeft', WD_S_PREFIX),
      'rotateInDownRight' => __('RotateInDownRight', WD_S_PREFIX),
      'rotateInUpLeft' => __('RotateInUpLeft', WD_S_PREFIX),
      'rotateInUpRight' => __('RotateInUpRight', WD_S_PREFIX),
      'zoomIn' => __('ZoomIn', WD_S_PREFIX),
      'zoomInDown' => __('ZoomInDown', WD_S_PREFIX),
      'zoomInLeft' => __('ZoomInLeft', WD_S_PREFIX),
      'zoomInRight' => __('ZoomInRight', WD_S_PREFIX),
      'zoomInUp' => __('ZoomInUp', WD_S_PREFIX),
      'lightSpeedOut' => __('LightSpeedOut', WD_S_PREFIX),
      'rollOut' => __('RollOut', WD_S_PREFIX),
      'bounceOut' => __('BounceOut', WD_S_PREFIX),
      'bounceOutDown' => __('BounceOutDown', WD_S_PREFIX),
      'bounceOutLeft' => __('BounceOutLeft', WD_S_PREFIX),
      'bounceOutRight' => __('BounceOutRight', WD_S_PREFIX),
      'bounceOutUp' => __('BounceOutUp', WD_S_PREFIX),
      'fadeOut' => __('FadeOut', WD_S_PREFIX),
      'fadeOutDown' => __('FadeOutDown', WD_S_PREFIX),
      'fadeOutDownBig' => __('FadeOutDownBig', WD_S_PREFIX),
      'fadeOutLeft' => __('FadeOutLeft', WD_S_PREFIX),
      'fadeOutLeftBig' => __('FadeOutLeftBig', WD_S_PREFIX),
      'fadeOutRight' => __('FadeOutRight', WD_S_PREFIX),
      'fadeOutRightBig' => __('FadeOutRightBig', WD_S_PREFIX),
      'fadeOutUp' => __('FadeOutUp', WD_S_PREFIX),
      'fadeOutUpBig' => __('FadeOutUpBig', WD_S_PREFIX),
      'flip' => __('Flip', WD_S_PREFIX),
      'flipOutX' => __('FlipOutX', WD_S_PREFIX),
      'flipOutY' => __('FlipOutY', WD_S_PREFIX),
      'rotateOut' => __('RotateOut', WD_S_PREFIX),
      'rotateOutDownLeft' => __('RotateOutDownLeft', WD_S_PREFIX),
      'rotateOutDownRight' => __('RotateOutDownRight', WD_S_PREFIX),
      'rotateOutUpLeft' => __('RotateOutUpLeft', WD_S_PREFIX),
      'rotateOutUpRight' => __('RotateOutUpRight', WD_S_PREFIX),
      'zoomOut' => __('ZoomOut', WD_S_PREFIX),
      'zoomOutDown' => __('ZoomOutDown', WD_S_PREFIX),
      'zoomOutLeft' => __('ZoomOutLeft', WD_S_PREFIX),
      'zoomOutRight' => __('ZoomOutRight', WD_S_PREFIX),
      'zoomOutUp' => __('ZoomOutUp', WD_S_PREFIX),
      'insert_valid_audio_file' => __('Insert valid audio file', WD_S_PREFIX),
      'fillmode' => __('Fillmode', WD_S_PREFIX),
      'fill' => __('Fill', WD_S_PREFIX),
      'Changes_must_be_saved' => __('Changes must be saved', WD_S_PREFIX),
      'edit_slide' => __('Edit Slide', WD_S_PREFIX),
      'media_library' => __('Media Library'), // This is WP translation.
      'disabled_in_free_version' => __('This functionality is disabled in free version.', WD_S_PREFIX),
      'video_disabled_in_free_version' => __('You can`t add video slide in free version', WD_S_PREFIX),
    )
  ));

  wp_enqueue_script('wds-deactivate-popup', WD_S_URL.'/wd/assets/js/deactivate_popup.js', array(), WD_S_VERSION, true );
  $admin_data = wp_get_current_user();

  wp_localize_script( 'wds-deactivate-popup', 'wdsWDDeactivateVars', array(
    "prefix" => "wds" ,
    "deactivate_class" =>  'wds_deactivate_link',
    "email" => $admin_data->data->user_email,
    "plugin_wd_url" => "https://web-dorado.com/products/wordpress-slider-plugin.html",
  ));
}

function wds_front_end_scripts() {
	global $wpdb;
	$rows = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "wdslayer ORDER BY `depth` ASC");
	$font_array = array();
	foreach ($rows as $row) {
		if (isset($row->google_fonts) && ($row->google_fonts == 1) && ($row->ffamily != "") && !in_array($row->ffamily, $font_array)) {
			$font_array[] = $row->ffamily;
		}
	}
	$query = implode("|", $font_array);
	if ($query != '') {
		$url = 'https://fonts.googleapis.com/css?family=' . $query . '&subset=greek,latin,greek-ext,vietnamese,cyrillic-ext,latin-ext,cyrillic';
	}
    ?>
    <script>var wds_params = [];</script>
    <?php
	wp_register_style('wds_frontend', WD_S_FRONT_URL . '/css/wds_frontend.css', array(), WD_S_VERSION);
	wp_register_style('wds_effects', WD_S_FRONT_URL . '/css/wds_effects.css', array(), WD_S_VERSION);
	wp_register_style('wds_font-awesome', WD_S_FRONT_URL . '/css/font-awesome/font-awesome.css', array(), '4.6.3');
	if ($query != '') {
		wp_register_style('wds_googlefonts', $url, null, null);
	}

	wp_register_script('wds_jquery_mobile', WD_S_FRONT_URL . '/js/jquery.mobile.js', array('jquery'), WD_S_VERSION);
    wp_register_script(WD_S_PREFIX . '_frontend', WD_S_FRONT_URL . '/js/wds_frontend.js', array('jquery'), WD_S_VERSION);
    wp_localize_script( WD_S_PREFIX . '_frontend', 'wds_object', array(
    "is_free" => WD_S_FREE,
    'pause' => __('Pause', WD_S_PREFIX),
    'play' => __('Play', WD_S_PREFIX),
  ));
  if ( !WD_S_FREE ) {
    wp_register_script('wds_jquery_featureCarouselslider', WD_S_FRONT_URL . '/js/jquery.featureCarouselslider.js', array( 'jquery' ), WD_S_VERSION);
    wp_register_script('wds_hotspot', WD_S_FRONT_URL . '/js/wds_hotspot.js', array( 'jquery' ), WD_S_VERSION);
    wp_register_script('wds_youtube', 'https://www.youtube.com/iframe_api');
  }
}
add_action('wp_enqueue_scripts', 'wds_front_end_scripts');

// Languages localization.
function wds_language_load() {
  load_plugin_textdomain(WD_S_PREFIX, FALSE, basename(dirname(__FILE__)) . '/languages');
}
add_action('init', 'wds_language_load');

function wds_add_embed_ajax() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_S_DIR . '/framework/WDW_S_Library.php');
  require_once(WD_S_DIR . '/framework/WDW_S_LibraryEmbed.php');

  if (!WDW_S_LibraryEmbed::verify_nonce('')) {
    die(WDW_S_LibraryEmbed::delimit_wd_output(json_encode(array("error", "Sorry, your nonce did not verify."))));
  }
  $embed_action = WDW_S_Library::get('action');
  if (($embed_action != '') && ($embed_action == 'wds_addEmbed')) {
    $url_to_embed = WDW_S_Library::get('URL_to_embed');
    $data = WDW_S_LibraryEmbed::add_embed($url_to_embed);
    echo WDW_S_LibraryEmbed::delimit_wd_output($data);
    wp_die();
  }
  die('Nothing to add');
}
if ( !WD_S_FREE ) {
  add_action('wp_ajax_wds_addEmbed', 'wds_add_embed_ajax');
}

function wds_get_sliders() {
  global $wpdb;
  $results = $wpdb->get_results("SELECT `id`,`name` FROM `" . $wpdb->prefix . "wdsslider`", OBJECT_K);
  $sliders = array();
  foreach ($results as $id => $slider) {
    $sliders[$id] = isset($slider->name) ? $slider->name : '';
  }
  return $sliders;
}

function wds_overview() {
  if (is_admin() && !isset($_REQUEST['ajax'])) {
    if (!class_exists("DoradoWeb")) {
      require_once(WD_S_DIR . '/wd/start.php');
    }
    global $wds_options;
    $wds_options = array(
      "prefix" => "wds",
      "wd_plugin_id" => 69,
      "plugin_title" => "Slider WD",
      "plugin_wordpress_slug" => "slider-wd",
      "plugin_dir" => WD_S_DIR,
      "plugin_main_file" => __FILE__,
      "description" => __('Slider WD is a responsive plugin, which allows adding sliders to your posts/pages and to custom location. It uses large number of transition effects and supports various types of layers.', WD_S_PREFIX),
      // from web-dorado.com
      "plugin_features" => array(
        0 => array(
          "title" => __("Responsive", "wds"),
          "description" => __("Sleek, powerful and intuitive design and layout brings the slides on a new level, for perfect and fast web surfing. Ways that users interact with 100% responsive Slider WD guarantees better and brave experience.", "wds"),
        ),
        1 => array(
          "title" => __("SEO Friendly", "wds"),
          "description" => __("Slider WD has developed the best practices in SEO field. The plugin supports all functions necessary for top-rankings.", "wds"),
        ),
        2 => array(
          "title" => __("Drag & Drop Back-End Interface", "wds"),
          "description" => __("Arrange each and every layer via user friendly drag and drop interface in seconds. This function guarantees fast and effective usability of the plugin without any development skills.", "wds"),
        ),
        3 => array(
          "title" => __("Touch Swipe Navigation", "wds"),
          "description" => __("Touch the surface of your mobile devices and experience smooth finger navigation. In desktop devices you can experience the same navigation using mouse dragging.", "wds"),
        ),
        4 => array(
          "title" => __("Navigation Custom Buttons", "wds"),
          "description" => __("You can choose among variety of navigation button designs included in the plugin or upload and use your custom ones, based on preferences.", "wds"),
        )
      ),
      // user guide from web-dorado.com
      "user_guide" => array(
        0 => array(
          "main_title" => __("Installing the Slider WD", "wds"),
          "url" => "https://web-dorado.com/wordpress-slider-wd/installing.html",
          "titles" => array()
        ),
        1 => array(
          "main_title" => __("Adding Images to Sliders", "wds"),
          "url" => "https://web-dorado.com/wordpress-slider-wd/adding-images.html",
          "titles" => array()
        ),
        2 => array(
          "main_title" => __("Adding Layers to The Slide", "wds"),
          "url" => "https://web-dorado.com/wordpress-slider-wd/adding-layers.html",
          "titles" => array()
        ),
        3 => array(
          "main_title" => __("Changing/Modifying Slider Settings", "wds"),
          "url" => "https://web-dorado.com/wordpress-slider-wd/changing-settings.html",
          "titles" => array()
        ),
        4 => array(
          "main_title" => __("Publishing the Created Slider", "wds"),
          "url" => "https://web-dorado.com/wordpress-slider-wd/publishing-slider.html",
          "titles" => array()
        ),
        5 => array(
          "main_title" => __("Importing/Exporting Sliders", "wds"),
          "url" => "https://web-dorado.com/wordpress-slider-wd/import-export.html",
          "titles" => array()
        ),
      ),
      "video_youtube_id" => "xebpM_-GwG0",  // e.g. https://www.youtube.com/watch?v=acaexefeP7o youtube id is the acaexefeP7o
      "plugin_wd_url" => "https://web-dorado.com/products/wordpress-slider-plugin.html",
      "plugin_wd_demo_link" => "http://wpdemo.web-dorado.com/slider/",
      "plugin_wd_addons_link" => "",
      "after_subscribe" => admin_url('admin.php?page=overview_wds'), // this can be plagin overview page or set up page
      "plugin_wizard_link" => '',
      "plugin_menu_title" => "Slider WD",
      "plugin_menu_icon" => WD_S_URL . '/images/wd_slider.png',
      "deactivate" => ( WD_S_FREE ? TRUE : FALSE ),
      "subscribe" => ( WD_S_FREE ? TRUE : FALSE ),
      "custom_post" => 'sliders_wds',
      "menu_position" => null,
    );

    dorado_web_init($wds_options);
  }
}
add_action('init', 'wds_overview', 9);

function wds_topic() {
  $page = isset($_GET['page']) ? $_GET['page'] : '';
  $user_guide_link = 'https://web-dorado.com/wordpress-slider-wd/';
  $support_forum_link = 'https://wordpress.org/support/plugin/slider-wd';
  $pro_link = 'https://web-dorado.com/files/fromslider.php';
  $pro_icon = WD_S_URL . '/images/wd_logo.png';
  $support_icon = WD_S_URL . '/images/support.png';
  $prefix = 'wds';
  switch ($page) {
    case 'sliders_wds': {
      $help_text = 'create, edit and delete sliders';
      $user_guide_link .= 'adding-images.html';
      break;
    }
    case 'goptions_wds': {
      $help_text = 'edit global options for sliders';
      $user_guide_link .= 'adding-images.html';
      break;
    }
    case 'licensing_wds': {
      $help_text = '';
      $user_guide_link .= 'adding-images.html';
      break;
    }
    default: {
      return '';
      break;
    }
  }
  ob_start();
  ?>
  <style>
    .wd_topic {
      background-color: #ffffff;
      border: none;
      box-sizing: border-box;
      clear: both;
      color: #6e7990;
      font-size: 14px;
      font-weight: bold;
      line-height: 44px;
      padding: 0 0 0 15px;
      vertical-align: middle;
      width: 98%;
    }
    .wd_topic .wd_help_topic {
      float: left;
    }
    .wd_topic .wd_help_topic a {
      color: #0073aa;
    }
    .wd_topic .wd_help_topic a:hover {
      color: #00A0D2;
    }
    .wd_topic .wd_support {
      float: right;
      margin: 0 10px;
    }
    .wd_topic .wd_support img {
      vertical-align: middle;
    }
    .wd_topic .wd_support a {
      text-decoration: none;
      color: #6E7990;
    }
    .wd_topic .wd_pro {
      float: right;
      padding: 0;
    }
    .wd_topic .wd_pro a {
      border: none;
      box-shadow: none !important;
      text-decoration: none;
    }
    .wd_topic .wd_pro img {
      border: none;
      display: inline-block;
      vertical-align: middle;
    }
    .wd_topic .wd_pro a,
    .wd_topic .wd_pro a:active,
    .wd_topic .wd_pro a:visited,
    .wd_topic .wd_pro a:hover {
      background-color: #D8D8D8;
      color: #175c8b;
      display: inline-block;
      font-size: 11px;
      font-weight: bold;
      padding: 0 10px;
      vertical-align: middle;
    }
  </style>
  <div class="update-nag wd_topic">
    <?php
    if ($help_text) {
      ?>
      <span class="wd_help_topic">
      <?php echo sprintf(__('This section allows you to %s.', $prefix), $help_text); ?>
        <a target="_blank" href="<?php echo $user_guide_link; ?>">
        <?php _e('Read More in User Manual', $prefix); ?>
      </a>
    </span>
      <?php
    }
    if ( WD_S_FREE ) {
      $text = strtoupper(__('Upgrade to paid version', $prefix));
      ?>
      <div class="wd_pro">
        <a target="_blank" href="<?php echo $pro_link; ?>">
          <img alt="web-dorado.com" title="<?php echo $text; ?>" src="<?php echo $pro_icon; ?>" />
          <span><?php echo $text; ?></span>
        </a>
      </div>
      <?php
    }
    if (FALSE) {
      ?>
      <span class="wd_support">
      <a target="_blank" href="<?php echo $support_forum_link; ?>">
        <img src="<?php echo $support_icon; ?>" />
        <?php _e('Support Forum', $prefix); ?>
      </a>
    </span>
      <?php
    }
    ?>
  </div>
  <?php
  echo ob_get_clean();
}
add_action('admin_notices', 'wds_topic', 11);

/**
 * Show notice to install Image Optimization plugin
 */
function wds_io_install_notice() {
  // Remove old notice.
  if ( get_option('wds_io_notice_status') !== FALSE ) {
    update_option('wds_io_notice_status', '1', 'no');
  }

  // Show notice only on plugin pages.
  if ( !isset($_GET['page']) || strpos(esc_html($_GET['page']), '_wds') === FALSE ) {
    return '';
  }

  $meta_value = get_option('wds_io_notice_status');
  if ( $meta_value === '' || $meta_value === FALSE ) {
    ob_start();
    $prefix = WD_S_PREFIX;
    $nicename = WD_S_NICENAME;
    $url = WD_S_URL;
    $dismiss_url = add_query_arg(array( 'action' => 'wd_io_dismiss' ), admin_url('admin-ajax.php'));
    $install_url = esc_url(wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=image-optimizer-wd'), 'install-plugin_image-optimizer-wd'));
    ?>
    <div class="notice notice-info" id="wd_io_notice_cont">
      <p>
        <img id="wd_io_logo_notice" src="<?php echo $url . '/images/iopLogo.png'; ?>" />
        <?php echo sprintf(__("%s advises: Install brand new %s plugin to optimize your website images quickly and easily.", $prefix), $nicename, '<a href="https://wordpress.org/plugins/image-optimizer-wd/" title="' . __("More details", $prefix) . '" target="_blank">' .  __("Image Optimizer WD", $prefix) . '</a>'); ?>
        <a class="button button-primary" href="<?php echo $install_url; ?>">
          <span onclick="jQuery.post('<?php echo $dismiss_url; ?>');"><?php _e("Install", $prefix); ?></span>
        </a>
      </p>
      <button type="button" class="wd_io_notice_dissmiss notice-dismiss" onclick="jQuery('#wd_io_notice_cont').hide(); jQuery.post('<?php echo $dismiss_url; ?>');"><span class="screen-reader-text"></span></button>
    </div>
    <style>
      @media only screen and (max-width: 500px) {
        body #wd_backup_logo {
          max-width: 100%;
        }
        body #wd_io_notice_cont p {
          padding-right: 25px !important;
        }
      }
      #wd_io_logo_notice {
        height: 32px;
        float: left;
        margin-right: 10px;
      }
      #wd_io_notice_cont {
        position: relative;
      }
      #wd_io_notice_cont a {
        margin: 0 5px;
      }
      #wd_io_notice_cont .dashicons-dismiss:before {
        content: "\f153";
        background: 0 0;
        color: #72777c;
        display: block;
        font: 400 16px/20px dashicons;
        speak: none;
        height: 20px;
        text-align: center;
        width: 20px;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
      }
      .wd_io_notice_dissmiss {
        margin-top: 5px;
      }
    </style>
    <?php
    echo ob_get_clean();
  }
}

if ( !is_dir(plugin_dir_path(__DIR__) . 'image-optimizer-wd') ) {
  add_action('admin_notices', 'wds_io_install_notice');
}

if ( !function_exists('wd_iops_install_notice_status') ) {
  // Add usermeta to db.
  function wd_iops_install_notice_status() {
    update_option('wds_io_notice_status', '1', 'no');
  }
  add_action('wp_ajax_wd_io_dismiss', 'wd_iops_install_notice_status');
}

/**
 * Register slider preview custom post type.
 */
function wds_register_slider_preview() {
  $args = array(
    'public' => TRUE,
    'show_in_menu' => FALSE,
    'exclude_from_search' => TRUE,
    'create_posts' => 'do_not_allow',
    'capabilities' => array(
      'create_posts' => FALSE,
      'edit_post' => 'edit_posts',
      'read_post' => 'edit_posts',
      'delete_posts' => FALSE,
    ),
  );
  register_post_type('wds-slider', $args);
}

add_action('init', 'wds_register_slider_preview');

// Add custom tabs to media uploader.
function wds_custom_media_upload_tab_name( $tabs ) {
  $custom_tabs = array( 'wds_posts', 'wds_embed', 'wds_custom_uploader' );

  if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'sliders_wds' )
    || ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], $custom_tabs ) ) ) {
    $newtabs = array(
      'wds_posts' => __( "Posts", WD_S_PREFIX ),
      'wds_embed' => __( "Embed Media", WD_S_PREFIX ),
    );

    $wds_global_options = get_option("wds_global_options", 0);
    $global_options = json_decode($wds_global_options);
    $spider_uploader = isset($global_options->spider_uploader) ? $global_options->spider_uploader : 0;
    if ( $spider_uploader ) {
      $newtabs['wds_custom_uploader'] = __( "WD Media Uploader", WD_S_PREFIX );
    }

    if ( isset($tabs['nextgen']) ) {
      unset($tabs['nextgen']);
    }

    if ( is_array( $tabs ) ) {
      return array_merge( $tabs, $newtabs );
    }
    else {
      return $newtabs;
    }
  }

  return $tabs;
}
add_filter( 'media_upload_tabs', 'wds_custom_media_upload_tab_name' );

/**
 * Remove unused tabs from media uploader.
 *
 * @param $strings
 *
 * @return mixed
 */
function wds_custom_media_uploader_tabs( $strings ) {
  if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'sliders_wds' ) ) {
    // Update strings.
    $strings['insertMediaTitle'] = __( "Images / Videos", WD_S_PREFIX );
    $strings['insertIntoPost'] = __( "Add to slider", WD_S_PREFIX );

    // Remove options.
    $strings_to_remove = array(
      'createVideoPlaylistTitle',
      'createGalleryTitle',
      'insertFromUrlTitle',
      'createPlaylistTitle'
    );
    foreach ($strings_to_remove as $string) {
      if (isset($strings[$string])) {
       unset($strings[$string]);
      }
    }
  }

  return $strings;
}
add_filter( 'media_view_strings', 'wds_custom_media_uploader_tabs', 5 );

/**
 *
 */
function wds_media_upload_window() {
  if (function_exists('current_user_can')) {
    if (!current_user_can('manage_options')) {
      die('Access Denied');
    }
  }
  else {
    die('Access Denied');
  }
  require_once(WD_S_DIR . '/framework/WDW_S_Library.php');
  $tab = WDW_S_Library::get('tab');
  $custom_tabs = array( 'wds_posts', 'wds_embed' );
  if ( in_array($tab, $custom_tabs) ) {
    $tab = str_replace('wds_', '', $tab);
    require_once(WD_S_DIR . '/admin/controllers/' . $tab . '.php');
    $controller_class = 'WDSController' . $tab;
    $controller = new $controller_class();
    $controller->execute();
  }
}
add_action( 'media_upload_wds_posts', 'wds_media_upload_window' );
add_action( 'media_upload_wds_embed', 'wds_media_upload_window' );
add_action( 'media_upload_wds_custom_uploader', 'wds_filemanager_ajax' );

/**
 * Register iframe styles and scripts.
 */
function wds_register_iframe_scripts() {
  $required_scripts = array( 'jquery' );
  $required_styles = array(
    // 'admin-bar',
    // 'dashicons',
    'wp-admin', // admin styles
    'buttons', // buttons styles
    'media-views', // media uploader styles
    'wp-auth-check', // check all
  );
  wp_register_script(WD_S_PREFIX . '_admin', WD_S_URL . '/js/wds.js', $required_scripts, WD_S_VERSION);

  wp_register_style(WD_S_PREFIX . '_tables', WD_S_URL . '/css/wds_tables.css', $required_styles, WD_S_VERSION);

  wp_localize_script( WD_S_PREFIX . '_admin', 'wds', array(
    "file_not_supported" => __('This file type is not supported.', WD_S_PREFIX),
  ));
}

/**
 * Register admin styles and scripts.
 */
function wds_register_admin_scripts() {
  $required_scripts = array( 'jquery' );
  wp_register_script(WD_S_PREFIX . '_admin', WD_S_URL . '/js/wds.js', $required_scripts, WD_S_VERSION);
  wp_register_style(WD_S_PREFIX . '_tables', WD_S_URL . '/css/wds_tables.css', FALSE, WD_S_VERSION);
  wp_localize_script( WD_S_PREFIX . '_admin', 'wds', array(
    "file_not_supported" =>  __('This file type is not supported.', WD_S_PREFIX),
  ));
}
add_action('admin_enqueue_scripts', 'wds_register_admin_scripts');

function wds_add_plugin_meta_links($meta_fields, $file) {
  if ( plugin_basename(__FILE__) == $file ) {
    $plugin_url = "https://wordpress.org/support/plugin/slider-wd";
    $prefix = WD_S_PREFIX;
    $meta_fields[] = "<a href='" . $plugin_url . "' target='_blank'>" . __('Support Forum', $prefix) . "</a>";
    $meta_fields[] = "<a href='" . $plugin_url . "/reviews#new-post' target='_blank' title='" . __('Rate', $prefix) . "'>
            <i class='wdi-rate-stars'>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "</i></a>";

    $stars_color = "#ffb900";

    echo "<style>"
      . ".wdi-rate-stars{display:inline-block;color:" . $stars_color . ";position:relative;top:3px;}"
      . ".wdi-rate-stars svg{fill:" . $stars_color . ";}"
      . ".wdi-rate-stars svg:hover{fill:" . $stars_color . "}"
      . ".wdi-rate-stars svg:hover ~ svg{fill:none;}"
      . "</style>";
  }

  return $meta_fields;
}
if ( WD_S_FREE ) {
  add_filter("plugin_row_meta", 'wds_add_plugin_meta_links', 10, 2);
}
