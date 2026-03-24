<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EksiKontrol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        INSTEAD OF DELETE, INSERT, UPDATE -- Hepsini burada yakalıyoruz
        AS
        BEGIN
            SET NOCOUNT ON;

            -- 2. BÖLÜM: STOK KONTROLÜ (INSERT & UPDATE)
            IF EXISTS (SELECT 1 FROM inserted)
            BEGIN
                -- Eksi stok kontrolü
                IF EXISTS (
                    SELECT 1
                    FROM inserted i
                    INNER JOIN vw_stok01 v WITH (NOLOCK) ON 
                        ISNULL(i.NUM1, 0) = ISNULL(v.NUM1, 0) AND 
                        ISNULL(i.NUM2, 0) = ISNULL(v.NUM2, 0) AND 
                        ISNULL(i.NUM3, 0) = ISNULL(v.NUM3, 0) AND 
                        ISNULL(i.NUM4, 0) = ISNULL(v.NUM4, 0) AND
                        ISNULL(i.TEXT1, '') = ISNULL(v.TEXT1, '') AND 
                        ISNULL(i.TEXT2, '') = ISNULL(v.TEXT2, '') AND 
                        ISNULL(i.TEXT3, '') = ISNULL(v.TEXT3, '') AND 
                        ISNULL(i.TEXT4, '') = ISNULL(v.TEXT4, '') AND
                        ISNULL(i.LOCATION1, '') = ISNULL(v.LOCATION1, '') AND 
                        ISNULL(i.LOCATION2, '') = ISNULL(v.LOCATION2, '') AND 
                        ISNULL(i.LOCATION3, '') = ISNULL(v.LOCATION3, '') AND 
                        ISNULL(i.LOCATION4, '') = ISNULL(v.LOCATION4, '') AND
                        i.AMBCODE = v.AMBCODE AND 
                        i.KOD = v.KOD AND 
                        i.LOTNUMBER = v.LOTNUMBER AND 
                        i.SERINO = v.SERINO
                    WHERE (i.SF_MIKTAR < 0 OR v.MIKTAR < 0)
                )
                BEGIN
                    RAISERROR ('Stok eksiye düşüyor! İşlem iptal edildi Eren.', 16, 1);
                    ROLLBACK TRANSACTION;
                    RETURN;
                END

                -- Eğer her şey yolundaysa işlemi gerçekleştir
                -- UPDATE işlemi mi?
                IF EXISTS (SELECT 1 FROM deleted)
                BEGIN
                    UPDATE s10
                    SET s10.SF_MIKTAR = i.SF_MIKTAR, s10.KOD = i.KOD, s10.LOTNUMBER = i.LOTNUMBER -- Buraya güncellenecek tüm kolonları eklemelisin!
                    FROM stok10a s10 INNER JOIN inserted i ON s10.id = i.id;
                END
                ELSE -- INSERT işlemi
                BEGIN
                    INSERT INTO stok10a (EVRAKNO, TRNUM, KOD, SF_MIKTAR, ...) -- Tüm kolonları yaz
                    SELECT EVRAKNO, TRNUM, KOD, SF_MIKTAR, ... FROM inserted;
                END
            END
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
        DB::unprepared("DROP TRIGGER trg_stok10a_StockControl");
    }
}
