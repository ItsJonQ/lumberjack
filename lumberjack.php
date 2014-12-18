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

global $lumberjack;

class Lumberjack {

  static function get_category_meta() {
    global $post;

    $post_categories = wp_get_post_categories( $post->ID );

    if( !$post_categories ) {
      return null;
    }

    $categories = array();

    foreach( $post_categories as $cat ) {
      $cat = get_category( $cat );

        // Create a new category object
      $category = new stdClass();

        // Set category data
      $category->id = $cat->term_id;
      $category->name = $cat->name;
      $category->slug = $cat->slug;
      $category->link = get_category_link( $cat );

        // Push the category object to the categories array
      $categories[] = $category;
    }

    return $categories;
  }

  static function get_tag_meta() {
    global $post;

    $post_tags = get_the_tags( $post->ID );

    if( !$post_tags ) {
      return null;
    }

    $tags = array();

    foreach( $post_tags as $t ) {

        // Create a new tag object
      $tag = new stdClass();

        // Set tag data
      $tag->id = $t->term_id;
      $tag->name = $t->name;
      $tag->slug = $t->slug;
      $tag->link = get_tag_link( $t->term_id );

        // Push the tag object to the tags array
      $tags[] = $tag;

    }

    return $tags;
  }

  static function get_content_media( $selector = null, $content = null ) {
    global $post;

    // Return the $content if $args are not defined
    if( !isset( $selector ) ) {
      return;
    }

    // Defining the Media ($1 as default to work with preg_replace)
    $media = '$1';

    // Regex filter to find/locate the <p> tags and the $selector
    $filter = '/<p[^>]*>\\s*?(<a .*?><'.$selector.'.*?><\\/a>|<'.$selector.'.*?>)?\\s*<\/p>/';
      // Adjust the class if the tag is an iFrame
    if( $selector === 'iframe' ) {
        // Defining the video sites to filter for
      $video_sites = array(
        'vimeo',
        'youtube'
        );
        // Looping through the video sites
      foreach($video_sites as $site) {
          // If the content contains the $site's key name
        if( strpos($content, $site) ) {
            // Add video and the $site name
          $args['class'] = $args['class'] . ' video ' . $site;
            // Break the foreach loop
          break;
        }
      }
    }

    // Defining the content
    if( !isset( $content ) ) {
      $content = $post->post_content;
    }

    // Parsing the $content for matches via preg_match_all
    preg_match_all( $filter, $content, $matches );

    return $matches[1];
  }

  static function get_content_images( $content ) {
    return self::get_content_media( 'img', $content );
  }

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

  public static function get_posts($query = false, $PostClass = 'LumberjackPost', $return_collection = false ){
      return TimberPostGetter::get_posts($query, $PostClass, $return_collection);
  }

  /**
   * @param number
   * @return object
   */
  static function get_related( $posts_per_page = 5 ) {
    global $post;

    // Get the categories
    $categories = self::get_category_meta();

    // Get the tags
    $tags = self::get_tag_meta();

    // Return if the post does not have any categories
    if( empty( $categories ) ) {
      return false;
    }

    // Adding the IDs for each category to the $cat_ids array
    $cat_ids = array();
    $tag_ids = array();

    foreach( $categories as $cat ) {
      array_push( $cat_ids, $cat->id );
    }

    foreach( $tags as $tag ) {
      array_push( $tag_ids, $tag->id );
    }

    // Ensure $posts_per_page is set correctly
    if( isset( $posts_per_page ) && !is_numeric( $posts_per_page ) ) {
      $posts_per_page = 5;
    }

    // Setting up the $args for querying
    $args = array(
      'category__in'    => $cat_ids,
      'tag__in'         => $tag_ids,
      'post__not_in'    => array( $post->ID ),

      'orderby'         => 'rand',

      'date_query' => array(
        array(
          'column' => 'post_date_gmt',
          'after'  => '60 days ago'
          )
        ),

      'posts_per_page'  => $posts_per_page
      );

    // Getting the posts
    $posts = Timber::get_posts( $args );

    // Returning the posts
    return $posts;
  }
}

$lumberjack = new Lumberjack();
$GLOBALS['lumberjack'] = $lumberjack;

function lumberjack_init() {
  if ( class_exists('TimberPost') ) {
    require_once( 'functions/lumberjack-post.php' );
  }
}

// Initialize Lumberjack
add_action( 'plugins_loaded', 'lumberjack_init' );
