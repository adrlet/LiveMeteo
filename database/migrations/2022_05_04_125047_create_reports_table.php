<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->point('coordinates');
            $table->enum('category', ['SUNNY', 'CLOUDY', 'STORM', 'RAINFALL', 'SNOWING', 'HAILING'])
            ->default('SUNNY');
            $table->integer('rating')->default(0);
            $table->decimal('temperature', $precision = 4, $scale =  2)->default(0.00);
            $table->enum('wind',34, ['STRONG', 'WEAK', 'NO'])->default('NO');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
};
