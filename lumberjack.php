<?php
/**
* Plugin Name: Timber-Lumberjack
* Plugin URI: https://github.com/ItsJonQ/lumberjack
* Description: A Wordpress plugin that extends the Timber plugin
* Version: 1.0.1
* Author: Q
* Author URI: https://github.com/ItsJonQ/
*/

// Echo error message if Timber is missing.

function lumberjack_init() {
  if ( class_exists('TimberPost') ) {
    require_once( 'functions/lumberjack-base.php' );
    require_once( 'functions/lumberjack-post.php' );
  }
}

global $lumberjack;

class Lumberjack {

  /**
   * @return array
   */
  static function get_pager() {
    // Getting the pagination array from Timber - we're gonna modify it :)
    $pagination = Timber::get_pagination();

    // Since this is just a pager, we don't need the page numbers
    unset( $pagination['pages'] );

    // Setting the pagination type to smart-render the pagination template
    $pagination['type'] = 'pager';

    // Returning the updated Timber pagination array
    return $pagination;
  }

  /**
   * @param string
   * @return array
   */
  static function get_pagination( $active_class = "active" ) {
    // Getting the pagination array from Timber - we're gonna modify it :)
    $pagination = Timber::get_pagination();
    $pages = $pagination['pages'];

    // Modify the pagination if pages isn't empty
    if( !empty( $pages ) ) {
      foreach( $pages as $index => $page ) {

        // Timber uses the class of "current" by default for active items.
        // We're going to modify that to "active"
        if( isset( $page['current'] ) && $page['current'] == 1) {
          $pagination['pages'][$index]['class'] = str_replace( "current", $active_class, $page['class'] );
        }

      }
    }

    // Setting the pagination type to smart-render the pagination template
    $pagination['type'] = 'default';

    // Returning the updated Timber pagination array
    return $pagination;
  }
}

$lumberjack = new Lumberjack();
$GLOBALS['lumberjack'] = $lumberjack;

// Initialize Lumberjack
add_action( 'plugins_loaded', 'lumberjack_init' );
