<?php

class WDSControllerUninstall_wds {
  public function __construct() {
    if ( WD_S_FREE ) {
      global $wds_options;
      if ( !class_exists("DoradoWebConfig") ) {
        include_once(WD_S_DIR . "/wd/config.php");
      }
      $config = new DoradoWebConfig();
      $config->set_options($wds_options);
      $deactivate_reasons = new DoradoWebDeactivate($config);
      $deactivate_reasons->submit_and_deactivate();
    }
  }

  public function execute() {
    $task = ((isset($_POST['task'])) ? esc_html(stripslashes($_POST['task'])) : '');
    if (method_exists($this, $task)) {
      check_admin_referer('nonce_wd', 'nonce_wd');
      $this->$task();
    }
    else {
      $this->display();
    }
  }

  public function display() { 
    require_once WD_S_DIR . "/admin/models/WDSModelUninstall_wds.php";
    $model = new WDSModelUninstall_wds();

    require_once WD_S_DIR . "/admin/views/WDSViewUninstall_wds.php";
    $view = new WDSViewUninstall_wds($model);
    $view->display();
  }

  public function uninstall() { 
    require_once WD_S_DIR . "/admin/models/WDSModelUninstall_wds.php";
    $model = new WDSModelUninstall_wds();

    require_once WD_S_DIR . "/admin/views/WDSViewUninstall_wds.php";
    $view = new WDSViewUninstall_wds($model);
    $view->uninstall();
  }
}
