<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait Multitenantable
{
  public static function bootMultitenantable()
  {
    static::creating(function ($model) {
      if (Auth::check()) {
        $model->user_id = Auth::user()->id;
      }
    });

    static::addGlobalScope("user_id", function (Builder $builder) {
      if (Auth::check()) {
        return $builder->where("user_id", Auth::user()->id);
      }
    });
  }
}
