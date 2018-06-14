<?PHP

/**
 * Class WDSViewembed.
 */
class WDSViewembed {
  public function __construct() {
    // Register and include styles and scripts.
    wds_register_iframe_scripts();
    wp_print_styles(WD_S_PREFIX . '_tables');
    wp_print_scripts(WD_S_PREFIX . '_admin');
  }

  /**
   * Display.
   */
  public function display() {
    echo WDW_S_Library::message_id(0, __('This functionality is disabled in free version.', 'wds'), 'error');

    die();
  }
}
