<?php

namespace StarfolkSoftware\Analytics\Tests\Models;

use StarfolkSoftware\Analytics\Traits\{HasViews, HasVisits};
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
  use HasViews, HasVisits;

  protected $guarded = [];
}
