<?php

namespace App\Data;

use App\Entity\Activity;
use App\Entity\Category;


class SearchData
{

  /**
   * @var int
   */
  public $page = 1;

  /**
   * @var string
   */
  public $q = '';

  /**
   * @var Category[]
   */
  public $categories = [];

  /**
   * @var Activity[]
   */
  public $activities = [];

  /**
   * @var null|float
   */
  public $max;

  /**
   * @var null|float
   */
  public $min;
  
}
