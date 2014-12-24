<?php

class LumberjackPost extends TimberPost {

  var $_disqus_identifier;
  var $_category;
  var $_tags;
  var $_snippet_thumbnail;

  var $PostClass = 'LumberjackPost';
  var $ImageClass = 'TimberImage';


  public function disqus_identifier() {
    // Defining the identifier
    $identifier = $this->id . ' ' . $this->guid;

    $this->_disqus_identifier = $identifier;

    return $identifier;
  }

  public function category() {
    global $lumberjack;

    $category = $lumberjack->get_category();

    $this->_category = $category;

    return $category;
  }

  public function tags() {
    global $lumberjack;
    global $post;

    $tags = $lumberjack->get_tags();

    $this->_tags = $tags;

    return $tags;
  }

  public function snippet_thumbnail() {
    global $lumberjack;

    $image = null;

    // Use the post's thumbnail if possible
    if ( !function_exists('get_post_thumbnail_id') ) {
      return;
    }

    $tid = get_post_thumbnail_id( $this->ID );

    if ($tid) {
      // Use the thumbnail as the snippet thumbnail
      $image = $tid;
    } else {
      // Parse the content for images
      $images = $lumberjack::get_content_images( $this->content );

      // Return if $images is empty
      if( empty( $images ) ) {
        return;
      }

      if( $images[0] ) {
        $xpath = new DOMXPath( @DOMDocument::loadHTML( $images[0] ) );
        $image = $xpath->evaluate("string(//img/@src)");
      }

    }

    // Creating the thumbnail based on TimberImage
    $thumbnail = new $this->ImageClass( $image );
    $this->_snippet_thumbnail = $thumbnail;

    return $thumbnail;
  }
}