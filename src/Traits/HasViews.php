<?php

namespace StarfolkSoftware\Analytics\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasViews
{
  /**
   * Return all views for this model.
   *
   * @return MorphMany
   */
  public function views(): MorphMany
  {
    return $this->morphMany(config('analytics.view_class'), 'viewable');
  }
}
