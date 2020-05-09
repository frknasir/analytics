<?php

namespace StarfolkSoftware\Analytics\Tests;

use StarfolkSoftware\Analytics\Tests\Models\Post;
use Illuminate\Foundation\Auth\User;
use StarfolkSoftware\Analytics\Listeners\{CaptureView, CaptureVisit};
use StarfolkSoftware\Analytics\Events\Viewed;
use Illuminate\Support\Facades\Event;

class AnalyticsTest extends TestCase
{
  /** @test */
  public function models_can_fire_up_viewed_event() {
    $post = Post::create([
      'title' => 'Some post'
    ]);

    Event::fake([
      Viewed::class
    ]);

    event(new Viewed($post));

    Event::assertDispatched(Viewed::class);
  }

  /** @test */
  public function capture_view_listener_should_be_called_after_viewed_is_fired() {
    $post = Post::create([
      'title' => 'Some post'
    ]);

    $viewListener = new CaptureView();

    $viewListener->handle(new Viewed($post));

    $this->assertSame(1, $post->views()->count());
  }

  /** @test */
  public function capture_visit_listener_should_be_called_after_viewed_is_fired() {
    $post = Post::create([
      'title' => 'Some post'
    ]);

    $visitListener = new CaptureVisit();

    $visitListener->handle(new Viewed($post));

    $this->assertSame(1, $post->visits()->count());
  }

  /** @test */
  public function only_one_view_is_captured_every_hour() {
    $post = Post::create([
      'title' => 'Some post'
    ]);

    $viewListener = new CaptureView();

    $viewListener->handle(new Viewed($post));
    $viewListener->handle(new Viewed($post));
    $viewListener->handle(new Viewed($post));
    $viewListener->handle(new Viewed($post));

    $this->assertSame(1, $post->views()->count());
  }

  /** @test */
  public function only_one_visit_is_captured_in_a_day() {
    $post = Post::create([
      'title' => 'Some post'
    ]);

    $visitListener = new CaptureVisit();

    $visitListener->handle(new Viewed($post));
    $visitListener->handle(new Viewed($post));
    $visitListener->handle(new Viewed($post));

    $this->assertSame(1, $post->visits()->count());
  }

  /** @test */
  public function authenticated_user_view_should_be_captured() {
    $user = User::first();
    auth()->login($user);

    $post = Post::create([
      'title' => 'Some post'
    ]);

    $viewListener = new CaptureView();

    $viewListener->handle(new Viewed($post));
    $this->assertSame($user->id, (integer) $post->views()->latest()->first()->user_id);
  }

  /** @test */
  public function authenticated_user_visit_should_be_captured() {
    $user = User::first();
    auth()->login($user);

    $post = Post::create([
      'title' => 'Some post'
    ]);

    $visitListener = new CaptureVisit();

    $visitListener->handle(new Viewed($post));
    $this->assertSame($user->id, (integer) $post->visits()->latest()->first()->user_id);
  }

  /** @test */
  public function views_resolve_the_viewable_model() {
    $user = User::first();

    $post = Post::create([
      'title' => 'Some post'
    ]);

    $viewListener = new CaptureView();

    $viewListener->handle(new Viewed($post));
    $view = $post->views()->latest()->first();

    $this->assertSame($view->viewable->id, $post->id);
    $this->assertSame($view->viewable->title, $post->title);
  }

  /** @test */
  public function visits_resolve_the_visitable_model() {
    $user = User::first();

    $post = Post::create([
      'title' => 'Some post'
    ]);

    $visitListener = new CaptureVisit();

    $visitListener->handle(new Viewed($post));
    $visit = $post->visits()->latest()->first();

    $this->assertSame($visit->visitable->id, $post->id);
    $this->assertSame($visit->visitable->title, $post->title);
  }

  /** @test */
  public function views_with_users_have_viewer_relation() {
    $user = User::first();
    auth()->login($user);

    $post = Post::create([
      'title' => 'Some post'
    ]);

    $viewListener = new CaptureView();
    $viewListener->handle(new Viewed($post));
    $view = $post->views()->latest()->first();

    $this->assertSame($user->id, (integer) $view->viewer->id);
  }

  /** @test */
  public function visits_with_users_have_visitor_relation() {
    $user = User::first();
    auth()->login($user);

    $post = Post::create([
      'title' => 'Some post'
    ]);

    $visitListener = new CaptureVisit();
    $visitListener->handle(new Viewed($post));
    $visit = $post->visits()->latest()->first();

    $this->assertSame($user->id, (integer) $visit->visitor->id);
  }
}
