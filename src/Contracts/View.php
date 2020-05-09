<?php

namespace StarfolkSoftware\Analytics\Contracts;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};

interface View
{
  /**
   * Scope a query to only include views for the current logged in user.
   *
   * @param Builder $query
   * @return Builder
   */
  public function scopeForCurrentUser($query): Builder;
}
