<?php

use App\Models\Apartment;
use App\Models\City;
use App\Models\Governorate;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            // $table->foreignIdFor(Governorate::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(City::class)->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->float('price');
            $table->float('rating')->default(0);
            // $table->int('rating_count')->default(0);

            $table->integer('bathrooms');
            $table->integer('bedrooms');
            $table->integer('size');
            $table->boolean('has_pool');
            $table->boolean('has_wifi');

            $table->timestamps();
        });
        Schema::create('favorite_apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Apartment::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
        Schema::dropIfExists('favorite_apartments');
    }
};
