<?php

/**
 * Class WDSControllerposts
 */
class WDSControllerposts {
  public function execute() {
    $this->display();
  }

  /**
   * Display.
   */
  public function display() {
    require_once WD_S_DIR . "/admin/models/posts.php";
    $model = new WDSModelposts();

    require_once WD_S_DIR . "/admin/views/posts.php";
    $view = new WDSViewposts($model);
    $view->display();
  }
}
