<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('headline')->nullable();
            $table->string('sub_headline')->nullable();
            $table->text('details')->nullable();
            $table->string('address')->nullable();
            $table->string('short_name')->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('logo')->nullable();
            $table->enum('menu_position', ['top', 'left', 'right', 'bottom'])->default('top');
            $table->text('contact_person')->nullable();
            $table->string('banner')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('hotline_number')->nullable();
            $table->string('host_url')->nullable();
            $table->string('asset_host')->nullable();
            $table->text('color_theme')->nullable();
            $table->integer('template_id')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->boolean('is_active')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
