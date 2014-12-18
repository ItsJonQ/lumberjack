<?php

class LumberjackPost extends LumberjackBase {

  public function set_disqus_identifier() {
    // Defining the identifier
    $identifier = $this->id . ' ' . $this->guid;

    $this->disqus_identifier = $identifier;

    return $identifier;
  }

  public function set_category() {
    global $lumberjack;

    $category = $lumberjack->get_category_meta();

    $this->category = $category;

    return $category;
  }

  public function set_tags() {
    global $lumberjack;

    $tags = $lumberjack->get_tag_meta();

    $this->tags = $tags;

    return $tags;
  }

    // Initializing the model
  public function __construct() {

    parent::__construct();

    self::set_category();
    self::set_tags();

    self::set_disqus_identifier();

  }
}