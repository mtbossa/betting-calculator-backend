<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait Multitenantable
{
  public static function bootMultitenantable()
  {
    static::creating(function ($model) {
      $model->user_id = Auth::user()->id;
    });

    static::addGlobalScope("user_id", function (Builder $builder) {
      return $builder->where("user_id", Auth::user()->id);
    });
  }
}
