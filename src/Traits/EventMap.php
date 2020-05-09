<?php

namespace StarfolkSoftware\Analytics\Traits;

use StarfolkSoftware\Analytics\Events\Viewed;
use StarfolkSoftware\Analytics\Listeners\CaptureView;
use StarfolkSoftware\Analytics\Listeners\CaptureVisit;

trait EventMap
{
  /**
   * All of the event / listener mappings.
   *
   * @var array
   */
  protected $events = [
    Viewed::class => [
      CaptureView::class,
      CaptureVisit::class,
    ],
  ];
}
