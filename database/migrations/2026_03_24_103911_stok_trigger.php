<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StokTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            ALTER TRIGGER trg_stok10a_PreventDelete
            ON stok10a
            INSTEAD OF DELETE
            AS
            BEGIN
                SET NOCOUNT ON;

                IF EXISTS (
                    SELECT 1 
                    FROM stok68t s68 WITH (NOLOCK)
                    INNER JOIN deleted d ON s68.EVRAKNO = d.EVRAKNO AND s68.TRNUM = d.TRNUM
                    WHERE d.EVRAKTIPI LIKE '%STOK68T%'
                )
                BEGIN
                    RAISERROR ('Bu kayıt stok68t tablosunda kullanılıyor, silemezsin', 16, 1);
                    ROLLBACK TRANSACTION;
                    RETURN;
                END

                DELETE s10
                FROM stok10a s10
                INNER JOIN deleted d ON s10.id = d.id; 
            END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}


