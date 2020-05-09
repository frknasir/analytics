<?php

namespace StarfolkSoftware\Analytics;

use Exception;
use Illuminate\Database\Eloquent\{Model, Builder};
use StarfolkSoftware\Analytics\Traits\HasVisits;
use StarfolkSoftware\Analytics\Contracts\Visit as VisitContract;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};

class Visit extends Model implements VisitContract
{
  use HasVisits;

  protected $guarded = [];

  protected $casts = [];

  public function visitable(): MorphTo
  {
    return $this->morphTo();
  }

  public function visitor(): BelongsTo
  {
    return $this->belongsTo($this->getAuthModelName(), 'user_id');
  }

  /**
   * get authentication model name
   *
   * @return String
   */
  protected function getAuthModelName(): String
  {
    if (config('analytics.user_model')) {
      return config('analytics.user_model');
    }

    if (!is_null(config('auth.providers.users.model'))) {
      return config('auth.providers.users.model');
    }

    throw new Exception('Could not determine the user model name.');
  }
}
