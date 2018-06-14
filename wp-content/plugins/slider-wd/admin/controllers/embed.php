<?php

/**
 * Class WDSControllerembed
 */
class WDSControllerembed {
  public function execute() {
    $this->display();
  }

  /**
   * Display.
   */
  public function display() {
    require_once WD_S_DIR . "/admin/views/embed.php";
    $view = new WDSViewembed();
    $view->display();
  }
}
