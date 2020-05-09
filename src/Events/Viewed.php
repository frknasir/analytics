<?php

namespace Starfolk\Analytics\Events;

use Illuminate\Database\Eloquent\Model;

class Viewed
{
  /**
   * The model instance.
   *
   * @var Model
   */
  public $model;

  /**
   * Create a new event instance.
   *
   * @param Model $model
   */
  public function __construct(Model $model)
  {
    $this->model = $model;
  }
}
