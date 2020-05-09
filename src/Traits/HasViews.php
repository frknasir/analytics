<?php

namespace StarfolkSoftware\Analytics\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\JsonResponse;
use StarfolkSoftware\Analytics\Traits\Trends;
use StarfolkSoftware\Analytics\View;

trait HasViews
{
  use Trends;

  /**
   * Return all views for this model.
   *
   * @return MorphMany
   */
  public function views(): MorphMany
  {
    return $this->morphMany(config('analytics.view_class'), 'viewable');
  }

  /**
   * Get all the stats.
   *
   * @return JsonResponse
   */
  public static function viewStats(): JsonResponse {
    $className = get_class();

    $views = View::select('created_at')
      ->where('viewable_type', $className)
      ->whereBetween('created_at', [
        today()->subDays(config('analytics.days_prior)'))->startOfDay()->toDateTimeString(),
        today()->endOfDay()->toDateTimeString(),
      ])->get();

    return response()->json([
      'view_count' => $views->count(),
      'view_trend' => json_encode(self::getDataPoints($views, config('analytics.days_prior)'))),
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
    $views = $this->views();
    $previousMonthlyViews = $views->whereBetween('created_at', [
      today()->subMonth()->startOfMonth()->startOfDay()->toDateTimeString(),
      today()->subMonth()->endOfMonth()->endOfDay()->toDateTimeString(),
    ]);
    $currentMonthlyViews = $views->whereBetween('created_at', [
      today()->startOfMonth()->startOfDay()->toDateTimeString(),
      today()->endOfMonth()->endOfDay()->toDateTimeString(),
    ]);
    $lastThirtyDays = $views->whereBetween('created_at', [
      today()->subDays(config('analytics.days_prior)'))->startOfDay()->toDateTimeString(),
      today()->endOfDay()->toDateTimeString(),
    ]);

    return response()->json([
      'model' => $this,
      'read_time' => $this->read_time ?? NULL,
      'popular_reading_times' => $this->popular_reading_times,
      'traffic' => $this->top_referers,
      'view_count' => $currentMonthlyViews->count(),
      'view_trend' => json_encode(self::getDataPoints($lastThirtyDays, config('analytics.days_prior)'))),
      'view_month_over_month' => self::compareMonthToMonth($currentMonthlyViews, $previousMonthlyViews),
      'view_count_lifetime' => $views->count(),
    ]);
  }

  /**
   * Get the 10 most popular reading times rounded to the nearest 30 minutes.
   *
   * @return array
   */
  public function getPopularReadingTimesAttribute(): array
  {
    // Get the views associated with the model
    $data = $this->views;

    // Filter the view data to only include hours:minutes
    $collection = collect();
    $data->each(function ($item, $key) use ($collection) {
        $collection->push($item->created_at->minute(0)->format('H:i'));
    });

    // Count the unique values and assign to their respective keys
    $filtered = array_count_values($collection->toArray());

    $popular_reading_times = collect();
    foreach ($filtered as $key => $value) {
      // Use each given time to create a 60 min range
      $start_time = Carbon::createFromTimeString($key);
      $end_time = $start_time->copy()->addMinutes(60);

      // Find the percentage based on the value
      $percentage = number_format($value / $data->count() * 100, 2);

      // Get a human-readable hour range and floating percentage
      $popular_reading_times->put(
        sprintf('%s - %s', $start_time->format('g:i A'), $end_time->format('g:i A')),
        $percentage
      );
    }

    // Cast the collection to an array
    $array = $popular_reading_times->toArray();

    // Only return the top 5 reading times and percentages
    $sliced = array_slice($array, 0, 5, true);

    // Sort the array in a descending order
    arsort($sliced);

    return $sliced;
  }

  /**
   * Get the top referring websites for a post.
   *
   * @return array
   */
  public function getTopReferersAttribute(): array
  {
    // Get the views associated with the post
    $data = $this->views;

    // Filter the view data to only include referrers
    $collection = collect();
    $data->each(function ($item, $key) use ($collection) {
      if (empty(parse_url($item->referer)['host'])) {
        $collection->push(__('app.other'));
      } else {
        $collection->push(parse_url($item->referer)['host']);
      }
    });

    // Count the unique values and assign to their respective keys
    $array = array_count_values($collection->toArray());

    // Only return the top N referrers with their view count
    $sliced = array_slice($array, 0, 10, true);

    // Sort the array in a descending order
    arsort($sliced);

    return $sliced;
  }
}
