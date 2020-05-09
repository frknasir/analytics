<?php

namespace StarfolkSoftware\Analytics\Listeners;

use StarfolkSoftware\Analytics\Events\Viewed;
use Illuminate\Database\Eloquent\Model;

class CaptureView
{
  /**
   * A view is captured when a user loads a model for the first time
   * in a given hour. The ID of the model is stored in session to
   * be validated against until it "expires" and is pruned.
   * @param  \StarfolkSoftware\Analytics\Events\Viewed
   * @return void
   */
  public function handle(Viewed $event)
  {
    if (!$this->wasRecentlyViewed($event->model)) {
      $view_data = [
        'viewable_type' => get_class($event->model),
        'viewable_id' => $event->model->id,
        'ip' => request()->getClientIp(),
        'agent' => request()->header('user_agent'),
        'referer' => $this->validUrl((string) request()->header('referer')),
        'user_id' => auth()->user()->id ?? NULL,
      ];

      $event->model->views()->create($view_data);

      $this->storeInSession($event->model);
    }
  }

  /**
   * Check if a given model exists in the session.
   *
   * @param Model $model
   * @return bool
   */
  private function wasRecentlyViewed(Model $model): bool
  {
    $viewed = session()->get('viewed_models', []);

    return array_key_exists($model->id, $viewed);
  }

  /**
   * Add a given model to the session.
   *
   * @param Model $model
   * @return void
   */
  private function storeInSession(Model $model)
  {
    session()->put("viewed_models.{$model->id}", now()->timestamp);
  }

  /**
   * Return only value URLs.
   *
   * @param string $url
   * @return mixed
   */
  private function validUrl(string $url)
  {
    return filter_var($url, FILTER_VALIDATE_URL) ?? null;
  }
}
