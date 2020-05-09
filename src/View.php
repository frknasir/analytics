<?php

namespace StarfolkSoftware\Analytics;

use Exception;
use Illuminate\Database\Eloquent\{Model, Builder};
use StarfolkSoftware\Analytics\Contracts\View as ViewContract;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};

class View extends Model implements ViewContract
{
  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'views';

  /**
   * The attributes that aren't mass assignable.
   *
   * @var array
   */
  protected $guarded = [];

  public function viewable(): MorphTo
  {
    return $this->morphTo();
  }

  public function viewer(): BelongsTo
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
