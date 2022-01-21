<?php

use App\Models\BetableMatch;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBetsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create("bets", function (Blueprint $table) {
      $table->id();
      $table->tinyInteger("betted_team", false, true);
      $table->decimal("odd", 5, 2, true);
      $table->decimal("amount", 10, 2, true);
      $table->decimal("profit", 10, 2, true);
      $table->decimal("real_profit", 10, 2, true);
      $table
        ->foreignIdFor(BetableMatch::class, "match_id")
        ->constrained("matches")
        ->onDelete("cascade");
      $table->foreignIdFor(User::class)->constrained();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists("bets");
  }
}
