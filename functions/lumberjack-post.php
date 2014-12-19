<?php

class LumberjackPost extends TimberPost {

  var $_disqus_identifier;
  var $_category;
  var $_tags;
  var $_snippet_thumbnail;

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

    $images = $lumberjack::get_content_images( $this->content );

    // Return if $images is empty
    if( empty( $images ) ) {
      return;
    }

    $thumbnail = null;

    if( $images[0] ) {
      $xpath = new DOMXPath( @DOMDocument::loadHTML( $images[0] ) );
      $thumbnail = $xpath->evaluate("string(//img/@src)");
    }

    $this->_snippet_thumbnail = $thumbnail;

    return $thumbnail;
  }
}