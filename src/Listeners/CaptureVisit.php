<?php

namespace StarfolkSoftware\Analytics\Listeners;

use StarfolkSoftware\Analytics\Events\Viewed;
use Illuminate\Database\Eloquent\Model;

class CaptureVisit
{
  /**
   * A visit is captured when a user loads a model for the first time
   * in a given day. The ID of the model and the IP associated with
   * the request are stored in session to be validated against.
   *
   * @param \StarfolkSoftware\Analytics\Events\Viewed
   * @return void
   */
  public function handle(Viewed $event)
  {
    $ip = request()->getClientIp();

    if ($this->visitIsUnique($event->model, $ip)) {
      $visit_data = [
        'visitable_type' => get_class($event->model),
        'visitable_id' => $event->model->id,
        'ip' => $ip,
        'agent' => request()->header('user_agent'),
        'referer' => $this->validUrl((string) request()->header('referer')),
        'user_id' => request()->user()->id || NULL,
      ];

      $event->model->visits()->create($visit_data);

      $this->storeInSession($event->model, $ip);
    }
  }

  /**
   * Check if a given model and IP are unique to the session.
   *
   * @param Model $model
   * @param string $ip
   * @return bool
   */
  private function visitIsUnique(Model $model, string $ip): bool
  {
    $visit = session()->get("visited_models.{$model->id}");

    if ($visit == NULL) {
      return true;
    }

    return $visit['ip'] != $ip;
  }

  /**
   * Add a given model and IP to the session.
   *
   * @param Model $model
   * @param string $ip
   * @return void
   */
  private function storeInSession(Model $model, string $ip)
  {
    session()->put("visited_models.{$model->id}", [
      'timestamp' => now()->timestamp,
      'ip' => $ip,
    ]);
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
