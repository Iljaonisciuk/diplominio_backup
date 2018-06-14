<?php
class WDSModelSliders_wds {

  public function __construct() {
  }

  public function get_slides_count($slider_id) {
    global $wpdb;
    $count = $wpdb->get_var("SELECT COUNT(id) FROM " . $wpdb->prefix . "wdsslide WHERE slider_id='". $slider_id ."' AND image_url<>'' AND image_url NOT LIKE '%images/no-image.png%'");
    return $count;
  }

  public function get_slides_row_data($slider_id) {
    global $wpdb;
    $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "wdsslide WHERE slider_id='%d' ORDER BY `order` ASC", $slider_id));
    if (!$rows) {
      $rows = array();
    }
    else {
      foreach ($rows as $row) {
        $row->image_url = $row->image_url ? str_replace('{site_url}', site_url(), $row->image_url) : WD_S_URL . '/images/no-image.png';
        $row->thumb_url = $row->thumb_url ? str_replace('{site_url}', site_url(), $row->thumb_url) : WD_S_URL . '/images/no-image.png';
        $title_dimension = json_decode($row->title);
        if ($title_dimension) {
          $row->att_width = isset($title_dimension->att_width) ? $title_dimension->att_width : 0;
          $row->att_height = isset($title_dimension->att_height) ? $title_dimension->att_height : 0;
          $row->video_duration = isset($title_dimension->video_duration) ? $title_dimension->video_duration : 0;
          $row->title = isset($title_dimension->title) ? $title_dimension->title : '';
        }
        else {
          $row->att_width = 0;
          $row->att_height = 0;
          $row->video_duration = 0;
        }
      }
    }
    return $rows;
  }

  public function get_layers_row_data($slide_id) {
    global $wpdb;
    $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "wdslayer WHERE slide_id='%d' ORDER BY `depth` ASC", $slide_id));
    foreach ($rows as $row) {
      $row->image_url = $row->image_url ? str_replace('{site_url}', site_url(), $row->image_url) : WD_S_URL . '/images/no-image.png';
      $title_dimension = json_decode($row->title);
      if ($title_dimension) {
        $row->attr_width = $title_dimension->attr_width;
        $row->attr_height = $title_dimension->attr_height;
        $row->title = $title_dimension->title;
      }
      else {
        $row->attr_width = 0;
        $row->attr_height = 0;
      }
    }
    return $rows;
  }

  public function get_slider_prev_img($slider_id) {
    global $wpdb;
    $slider = $wpdb->get_row($wpdb->prepare("SELECT `thumb_url`, `type` FROM " . $wpdb->prefix . "wdsslide WHERE slider_id='%d' ORDER BY `order` ASC",  $slider_id));
    $preview_img_url = WD_S_URL . '/images/no-image.png';
    if ($slider) {
      $img_url = $slider->type == 'video' && ctype_digit($slider->thumb_url) ? (wp_get_attachment_url(get_post_thumbnail_id($slider->thumb_url)) ? wp_get_attachment_url(get_post_thumbnail_id($slider->thumb_url)) : WD_S_URL . '/images/no-video.png') : $slider->thumb_url;
      if ($img_url) {
        $preview_img_url = $img_url;
        $preview_img_url = str_replace('{site_url}', site_url(), $preview_img_url);
      }
    }
    return $preview_img_url;
  }

  public function get_rows_data() {
    global $wpdb;
    $where = ((isset($_POST['search_value'])) ? 'WHERE name LIKE "%' . esc_html(stripslashes($_POST['search_value'])) . '%"' : '');
    $asc_or_desc = ((isset($_POST['asc_or_desc']) && esc_html($_POST['asc_or_desc']) == 'desc') ? 'desc' : 'asc');
    $order_by_arr = array('id', 'name', 'published');
    $order_by = ((isset($_POST['order_by']) && in_array(esc_html($_POST['order_by']), $order_by_arr)) ? esc_html($_POST['order_by']) : 'id');
    $order_by = ' ORDER BY `' . $order_by . '` ' . $asc_or_desc;
    if (isset($_POST['page_number']) && $_POST['page_number']) {
      $limit = ((int) $_POST['page_number'] - 1) * 20;
    }
    else {
      $limit = 0;
    }
    $query = "SELECT * FROM " . $wpdb->prefix . "wdsslider " . $where . $order_by . " LIMIT " . $limit . ",20";
    $rows = $wpdb->get_results($query);
    return $rows;
  }

  public function get_row_data($id, $reset) {
    global $wpdb;
    if ($id != 0 && !$reset) {
      $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'wdsslider WHERE id="%d"', $id));
      if ($row) {
        $row->enable_bullets = $row->bull_position == 'none' ? 0 : 1;
        $row->enable_filmstrip = $row->film_pos == 'none' ? 0 : 1;
        $row->enable_time_bar = $row->timer_bar_type == 'none' ? 0 : 1;
        $row->music_url = str_replace('{site_url}', site_url(), $row->music_url);
        $row->built_in_watermark_url = str_replace('{site_url}', site_url(), $row->built_in_watermark_url);
        $row->right_butt_url = str_replace('{site_url}', site_url(), $row->right_butt_url);
        $row->left_butt_url = str_replace('{site_url}', site_url(), $row->left_butt_url);
        $row->right_butt_hov_url = str_replace('{site_url}', site_url(), $row->right_butt_hov_url);
        $row->left_butt_hov_url = str_replace('{site_url}', site_url(), $row->left_butt_hov_url);
        $row->bullets_img_main_url = str_replace('{site_url}', site_url(), $row->bullets_img_main_url);
        $row->bullets_img_hov_url = str_replace('{site_url}', site_url(), $row->bullets_img_hov_url);
        $row->play_butt_url = str_replace('{site_url}', site_url(), $row->play_butt_url);
        $row->play_butt_hov_url = str_replace('{site_url}', site_url(), $row->play_butt_hov_url);
        $row->paus_butt_url = str_replace('{site_url}', site_url(), $row->paus_butt_url);
        $row->paus_butt_hov_url = str_replace('{site_url}', site_url(), $row->paus_butt_hov_url);
      }
    }
    else {
      $row = new stdClass();
      if ($reset && $id) {
        $row = $wpdb->get_row($wpdb->prepare('SELECT name FROM ' . $wpdb->prefix . 'wdsslider WHERE id="%d"', $id));
      }
      else {
        $row->name = '';
      }
      $row->id = $id;
      $row->width = 900;
      $row->height = 400;
      $row->full_width = 2;
      $row->auto_height = 0;
      $row->align = 'center';
      $row->effect = 'fade';
      $row->published = 1;
      $row->time_intervval = 5;
      $row->autoplay = 1;
      $row->shuffle = 0;
      $row->music = 0;
      $row->music_url = '';
      $row->preload_images = 1;
      $row->background_color = '000000';
      $row->background_transparent = 100;
      $row->glb_border_width = 0;
      $row->glb_border_style = 'none';
      $row->glb_border_color = '000000';
      $row->glb_border_radius = '';
      $row->glb_margin = 0;
      $row->glb_box_shadow = '';
      $row->image_right_click = 0;
      $row->layer_out_next = 0;
      $row->prev_next_butt = 1;
      $row->play_paus_butt = 0;
      $row->navigation = 'hover';
      $row->rl_butt_style = 'fa-angle';
      $row->rl_butt_size = 40;
      $row->pp_butt_size = 40;
      $row->butts_color = '000000';
      $row->hover_color = '000000';
      $row->nav_border_width = 0;
      $row->nav_border_style = 'none';
      $row->nav_border_color = 'FFFFFF';
      $row->nav_border_radius = '20px';
      $row->nav_bg_color = 'FFFFFF';
      $row->butts_transparent = 100;
      $row->enable_bullets = 1;
      $row->bull_position = 'bottom';
      $row->bull_style = 'fa-square-o';
      $row->bull_size = 20;
      $row->bull_color = 'FFFFFF';
      $row->bull_act_color = 'FFFFFF';
      $row->bull_margin = 3;
      $row->enable_filmstrip = 0;
      $row->film_pos = 'none';
      $row->film_thumb_width = 100;
      $row->film_thumb_height = 50;
      $row->film_bg_color = '000000';
      $row->film_tmb_margin = 0;
      $row->film_act_border_width = 0;
      $row->film_act_border_style = 'none';
      $row->film_act_border_color = 'FFFFFF';
      $row->film_dac_transparent = 50;
      $row->enable_time_bar = 1;
      $row->timer_bar_type = 'top';
      $row->timer_bar_size = 5;
      $row->timer_bar_color = 'BBBBBB';
      $row->timer_bar_transparent = 50;
      $row->built_in_watermark_type = 'none';
      $row->built_in_watermark_position = 'middle-center';
      $row->built_in_watermark_size = 15;
      $row->built_in_watermark_url = WD_S_URL . '/images/watermark.png';
      $row->built_in_watermark_text = 'web-dorado.com';
      $row->built_in_watermark_font_size = 20;
      $row->built_in_watermark_font = '';
      $row->built_in_watermark_color = 'FFFFFF';
      $row->built_in_watermark_opacity = 70;
      $row->stop_animation = 0;
      $row->css = '';
      $row->right_butt_url = WD_S_URL . '/images/arrow/arrow11/1/2.png';
      $row->left_butt_url = WD_S_URL . '/images/arrow/arrow11/1/1.png';
      $row->right_butt_hov_url = WD_S_URL . '/images/arrow/arrow11/1/4.png';
      $row->left_butt_hov_url = WD_S_URL . '/images/arrow/arrow11/1/3.png';
      $row->rl_butt_img_or_not = 'style';
      $row->bullets_img_main_url = WD_S_URL . '/images/bullet/bullet1/1/1.png';
      $row->bullets_img_hov_url = WD_S_URL . '/images/bullet/bullet1/1/2.png';
      $row->bull_butt_img_or_not = 'style';
      $row->play_paus_butt_img_or_not = 'style';
      $row->play_butt_url = WD_S_URL . '/images/button/button4/1/1.png';
      $row->play_butt_hov_url = WD_S_URL . '/images/button/button4/1/2.png';
      $row->paus_butt_url = WD_S_URL . '/images/button/button4/1/3.png';
      $row->paus_butt_hov_url = WD_S_URL . '/images/button/button4/1/4.png';
      $row->start_slide_num = 1;
      $row->effect_duration = 800;
      $row->carousel = 0;
      $row->carousel_image_counts = 7;
      $row->carousel_image_parameters = 0.85;
      $row->carousel_fit_containerWidth = 0;
      $row->carousel_width = 1000;
      $row->parallax_effect = 0;
      $row->mouse_swipe_nav = 0;
      $row->bull_hover = 1;
      $row->touch_swipe_nav = 1;
      $row->mouse_wheel_nav = 0;
      $row->keyboard_nav = 0;
      $row->possib_add_ffamily = '';
      $row->show_thumbnail = 0;
      $row->thumb_size = '0.3';
      $row->fixed_bg = 0;
      $row->smart_crop = 0;
      $row->crop_image_position = 'center center';
      $row->javascript = '';
      $row->carousel_degree = 0;
      $row->carousel_grayscale = 0;
      $row->carousel_transparency = 0;
      $row->bull_back_act_color = '000000';
      $row->bull_back_color = 'CCCCCC';
      $row->bull_radius = '20px';
      $row->possib_add_google_fonts = 0;
      $row->possib_add_ffamily_google = '';
      $row->slider_loop = 1;
      $row->hide_on_mobile = 0;
      $row->twoway_slideshow = 0;
      $row->full_width_for_mobile = 0;
      $row->order_dir = 'asc';
    }
    return $row;
  }

  public function page_nav() {
    global $wpdb;
    $where = ((isset($_POST['search_value']) && (esc_html(stripslashes($_POST['search_value'])) != '')) ? 'WHERE name LIKE "%' . esc_html(stripslashes($_POST['search_value'])) . '%"'  : '');
    $total = $wpdb->get_var("SELECT COUNT(*) FROM " . $wpdb->prefix . "wdsslider " . $where);
    $page_nav['total'] = $total;
    if (isset($_POST['page_number']) && $_POST['page_number']) {
      $limit = ((int) $_POST['page_number'] - 1) * 20;
    }
    else {
      $limit = 0;
    }
    $page_nav['limit'] = (int) ($limit / 20 + 1);
    return $page_nav;
  }

  /**
   * Create Preview Slider post.
   *
   * @return string $guid
   */
  public function get_slide_preview_post() {
    $post_type = 'wds-slider';
    $row = get_posts(array( 'post_type' => $post_type ));
    if ( !empty($row[0]) ) {
      return get_post_permalink($row[0]->ID);
    }
    else {
      $post_params = array(
        'post_author' => 1,
        'post_status' => 'publish',
        'post_content' => '[SliderPreview]',
        'post_title' => 'Preview',
        'post_type' => 'wds-slider',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_parent' => 0,
        'menu_order' => 0,
        'import_id' => 0,
      );
      // Create new post by fmformpreview type.
      if ( wp_insert_post($post_params) ) {
        flush_rewrite_rules();
        global $wpdb;

        return get_post_permalink($wpdb->insert_id);
      }
      else {
        return "";
      }
    }
  }

	/*
	* Create frontend js file.
	*
	* @param int int
	* @return bool
	*/
	public function create_frontend_js_file( $id ) {
		$create_js = WDW_S_Library::create_frontend_js_file( $id );
		global $wpdb;
		$update = $wpdb->update( $wpdb->prefix . 'wdsslider', array('jsversion' => rand()), array('id' => $id) );
		return $update;
	}
}