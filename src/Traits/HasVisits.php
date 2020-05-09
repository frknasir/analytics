<?php

namespace StarfolkSoftware\Analytics\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasVisits
{
  /**
   * Return all visits for this model.
   *
   * @return MorphMany
   */
  public function visits(): MorphMany
  {
    return $this->morphMany(config('analytics.visit_class'), 'visitable');
  }
}
