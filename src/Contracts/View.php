<?php

namespace StarfolkSoftware\Analytics\Contracts;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};

interface View
{
  public function viewable(): MorphTo;

  public function viewer(): BelongsTo;
}
