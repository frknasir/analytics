<?php

namespace StarfolkSoftware\Analytics\Contracts;

use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo};

interface Visit
{
  public function visitable(): MorphTo;

  public function visitor(): BelongsTo;
}
