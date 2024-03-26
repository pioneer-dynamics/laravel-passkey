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
        Schema::create($this->getPasskeysTable(), function (Blueprint $table) {
            $table->id();
            $table->morphs('passkeyable');
            $table->string('name');
            $table->text('credential_id');
            $table->text('public_key');
            $table->timestamps();
        });
    }

    private function getPasskeysTable()
    {
        return 'passkeys';
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->getPasskeysTable());
    }
};
