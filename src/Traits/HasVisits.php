<?php

namespace StarfolkSoftware\Analytics\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\JsonResponse;
use StarfolkSoftware\Analytics\Traits\Trends;
use StarfolkSoftware\Analytics\Visit;

trait HasVisits
{
  use Trends;

  /**
   * Return all visits for this model.
   *
   * @return MorphMany
   */
  public function visits(): MorphMany
  {
    return $this->morphMany(config('analytics.visit_class'), 'visitable');
  }

  /**
   * Get all the stats.
   *
   * @return JsonResponse
   */
  public static function viewStats(): JsonResponse {
    $className = get_class();

    $visits = Visit::select('created_at')
      ->where('visitable_type', $className)
      ->whereBetween('created_at', [
        today()->subDays(config('analytics.days_prior)'))->startOfDay()->toDateTimeString(),
        today()->endOfDay()->toDateTimeString(),
      ])->get();

    return response()->json([
      'visit_count' => $visits->count(),
      'visit_trend' => json_encode(self::getDataPoints($visits, config('analytics.days_prior)'))),
      'count' => $className::count(),
    ]);
  }

  /**
   * Get stats for a single post.
   *
   * @return JsonResponse
   */
  public function viewStat(): JsonResponse
  {
    $visits = $this->visits();
    $previousMonthlyVisits = $visits->whereBetween('created_at', [
      today()->subMonth()->startOfMonth()->startOfDay()->toDateTimeString(),
      today()->subMonth()->endOfMonth()->endOfDay()->toDateTimeString(),
    ]);
    $currentMonthlyVisits = $visits->whereBetween('created_at', [
      today()->startOfMonth()->startOfDay()->toDateTimeString(),
      today()->endOfMonth()->endOfDay()->toDateTimeString(),
    ]);

    return response()->json([
      'model' => $this,
      'visit_count' => $currentMonthlyVisits->count(),
      'visit_trend' => json_encode(self::getDataPoints($visits, config('analytics.days_prior)'))),
      'visit_month_over_month' => self::compareMonthToMonth($currentMonthlyVisits, $previousMonthlyVisits),
    ]);
  }
}
