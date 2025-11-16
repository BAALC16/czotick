<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventSlugToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('tenant')->table('events', function (Blueprint $table) {
            if (!Schema::connection('tenant')->hasColumn('events', 'event_slug')) {
                $table->string('event_slug', 255)->nullable()->after('event_title');
                $table->index('event_slug');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('tenant')->table('events', function (Blueprint $table) {
            if (Schema::connection('tenant')->hasColumn('events', 'event_slug')) {
                $table->dropIndex(['event_slug']);
                $table->dropColumn('event_slug');
            }
        });
    }
}
