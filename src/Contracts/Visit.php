<?php

namespace StarfolkSoftware\Analytics\Contracts;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};

interface Visit
{
  /**
   * Scope a query to only include visits for the current logged in user.
   *
   * @param Builder $query
   * @return Builder
   */
  public function scopeForCurrentUser($query): Builder;
}
