<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToAllTables extends Migration
{
    public function up()
    {
        $tables = [
            'pers00z', 'tekl20e', 'tekl20tı', 'stdm10t', 'stdm10e', 'TMUSTRE', 'TMUSTRT', 'INFO', 
            'stok48e', 'stok48t', 'FKKE', 'stok40t', 'FKKT', 'MMPS10S_E', 'sessions', 'MMPS10S_T',
            'stok47e', 'stok47t', 'bomu01e', 'bomu01t', 'stok60t', 'cari00', 'mmos10e', 'dosyalar00',
            'mmos10t', 'stok60ti', 'failed_jobs', 'gdef00', 'sfdc31e', 'gecouse', 'gecoust', 'ilceler',
            'iller', 'imlt00', 'imlt01', 'kalip00', 'dys00', 'kontakt00', 'migrations', 'stok20e',
            'mmps10e', 'sfdc31t', 'stok20t', 'mmps10t', 'password_resets', 'pers00', 
            'personal_access_tokens', 'stok00', 'stok10a', 'stok21e', 'LOGX', 'stok21t', 'plan_e',
            'plan_t', 'stok26e', 'stok26t', 'stok29e', 'stok20tı', 'stok29t', 'table00', 'stok40e',
            'FIRMA_TANIMLARI', 'stok46e', 'D7KIDSLB', 'stok46t', 'ULOG00', 'stok60e', 'report_templates',
            'stok63e', 'srv00', 'stok63t', 'QVAL10E', 'stok68e', 'QVAL10T', 'stok68t', 'stok69e',
            'QVAL02T', 'stok69t', 'QVAL02E', 'users', 'SRVKC0', 'stok25e', 'excrate', 'excratt', 'stok25t'
        ];

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->timestamp('deleted_at')->nullable();
                });
            }
        }
    }

    public function down()
    {
        $tables = [
            'pers00z', 'tekl20e', 'tekl20tı', 'stdm10t', 'stdm10e', 'TMUSTRE', 'TMUSTRT', 'INFO', 
            'stok48e', 'stok48t', 'FKKE', 'stok40t', 'FKKT', 'MMPS10S_E', 'sessions', 'MMPS10S_T',
            'stok47e', 'stok47t', 'bomu01e', 'bomu01t', 'stok60t', 'cari00', 'mmos10e', 'dosyalar00',
            'mmos10t', 'stok60ti', 'failed_jobs', 'gdef00', 'sfdc31e', 'gecouse', 'gecoust', 'ilceler',
            'iller', 'imlt00', 'imlt01', 'kalip00', 'dys00', 'kontakt00', 'migrations', 'stok20e',
            'mmps10e', 'sfdc31t', 'stok20t', 'mmps10t', 'password_resets', 'pers00', 
            'personal_access_tokens', 'stok00', 'stok10a', 'stok21e', 'LOGX', 'stok21t', 'plan_e',
            'plan_t', 'stok26e', 'stok26t', 'stok29e', 'stok20tı', 'stok29t', 'table00', 'stok40e',
            'FIRMA_TANIMLARI', 'stok46e', 'D7KIDSLB', 'stok46t', 'ULOG00', 'stok60e', 'report_templates',
            'stok63e', 'srv00', 'stok63t', 'QVAL10E', 'stok68e', 'QVAL10T', 'stok68t', 'stok69e',
            'QVAL02T', 'stok69t', 'QVAL02E', 'users', 'SRVKC0', 'stok25e', 'excrate', 'excratt', 'stok25t'
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('deleted_at');
                });
            }
        }
    }
}