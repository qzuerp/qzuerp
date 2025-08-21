<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RaporlamaController extends Controller
{
    public function index(Request $request)
    {
        $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' order by  TABLE_NAME ASC");

        $tableList = [];
        foreach ($tables as $table) {
            $tableList[] = $table->TABLE_NAME;
        }
        $selectedTable = $request->get('ana_tablo');

        $alanlar = [];
        if ($selectedTable) {
         $columns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? order by  TABLE_NAME ASC", [$selectedTable]);

         $alanlar = array_map(fn($col) => $col->COLUMN_NAME, $columns);
     }

     return view('raporlama.index', [
        'tableList' => $tableList,
        'selectedTable' => $selectedTable,
        'alanlar' => $alanlar,
    ]);
 }

 public function kriter(Request $request)
 {
    $anaTablo = $request->ana_tablo;
    $joins = $request->joins;

    $baglantiliTablolar = collect($joins)->pluck('table')->filter()->unique()->toArray();

    $tumAlanlar = [];

    // Ana tablo kolonları
    $anaAlanlar = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?", [$anaTablo]);
    $tumAlanlar[$anaTablo] = array_map(fn($col) => $col->COLUMN_NAME, $anaAlanlar);

    // Bağlantılı tabloların kolonları
    foreach ($baglantiliTablolar as $btablo) {
        $cols = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?", [$btablo]);
        $tumAlanlar[$btablo] = array_map(fn($col) => $col->COLUMN_NAME, $cols);
    }

    return view('raporlama.kriter', [
        'anaTablo' => $anaTablo,
        'joins' => $joins,
        'tumAlanlar' => $tumAlanlar,
    ]);
}


public function alanlar(Request $request)
{
    $anaTablo = $request->ana_tablo;
    $joins = json_decode($request->joins_json, true);
    $kriterler = $request->kriterler ?? [];

    $baglantiliTablolar = collect($joins)->pluck('table')->filter()->unique()->toArray();

    $tumAlanlar = [];

    $anaAlanlar = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?", [$anaTablo]);
    $tumAlanlar[$anaTablo] = array_map(fn($col) => $col->COLUMN_NAME, $anaAlanlar);
    foreach ($baglantiliTablolar as $btablo) {
        $cols = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?", [$btablo]);
        $tumAlanlar[$btablo] = array_map(fn($col) => $col->COLUMN_NAME, $cols);
    }

    return view('raporlama.alanlar', [
        'anaTablo' => $anaTablo,
        'joins' => $joins,
        'kriterler' => $kriterler,
        'tumAlanlar' => $tumAlanlar,
    ]);
}

public function run(Request $request)
{
    $anaTablo = $request->ana_tablo;
    $joins = json_decode($request->joins_json, true);
    $kriterler = json_decode($request->kriterler_json, true);
    $alanlar = $request->alanlar;

    $selectFields = [];
    foreach ($alanlar as $tablo => $fields) {
        foreach ($fields as $field) {
            $selectFields[] = "$tablo.$field as {$tablo}_{$field}";
        }
    }
       // dd($selectFields);
    $query = DB::table($anaTablo);

    if ($joins) {
        foreach ($joins as $join) {
            if (empty($join['table']) || empty($join['main_column']) || empty($join['linked_column'])) {
                continue;
            }
            $joinType = strtolower($join['type']) === 'left' ? 'leftJoin' : 'join';

            $query = $query->$joinType(
                $join['table'],
                "$anaTablo.{$join['main_column']}",
                '=',
                "{$join['table']}.{$join['linked_column']}"
            );
        }
    }

    if ($kriterler) {
        foreach ($kriterler as $crit) {
            if (empty($crit['table']) || empty($crit['column']) || !isset($crit['operator']) || !isset($crit['value'])) {
                continue;
            }
            $col = "{$crit['table']}.{$crit['column']}";
            $op = strtoupper($crit['operator']);
            $val = $crit['value'];

            if ($op === 'LIKE') {
                $val = "%$val%";
                $query->where($col, 'LIKE', $val);
            } else {
                $query->where($col, $op, $val);
            }
        }
    }

    $query->select($selectFields);

    $results = $query->get();

    return view('raporlama.sonuc', compact('results', 'selectFields'));
}

public function saveTemplate(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'ana_tablo' => 'required|string',
        'joins_json' => 'nullable',
        'alanlar' => 'required|array',
        'kriterler_json' => 'nullable',
    ]);

    $bareFilters = [];
    if ($request->kriterler_json) {
        $allFilters = json_decode($request->kriterler_json, true);
        foreach ($allFilters as $f) {
            $bareFilters[] = [
                'table' => $f['table'],
                'column' => $f['column'],
                'operator' => $f['operator'],
            ];
        }
    }

    DB::table('report_templates')->insert([
        'name' => $request->name,
        'main_table' => $request->ana_tablo,
        'joins_json' => $request->joins_json,
        'fields_json' => json_encode($request->alanlar),
        'filters_json' => json_encode($bareFilters),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('raporlama.index')->with('success', 'Rapor şablonu başarıyla kaydedildi.');
}

public function loadTemplate($id)
{
    $template = DB::table('report_templates')->where('id', $id)->first();

    if (!$template) {
        return redirect()->route('raporlama.index')->with('error', 'Şablon bulunamadı.');
    }

    $anaTablo = $template->main_table;
    $joins = json_decode($template->joins_json, true);
    $kriterler = json_decode($template->filters_json, true);
    $alanlar = json_decode($template->fields_json, true);

    // Join yapılacak tablolardaki alanları çek
    $tumAlanlar = [];

  $anaAlanlar = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? ", [$anaTablo]);

    $tumAlanlar[$anaTablo] = array_map(fn($col) => $col->COLUMN_NAME, $anaAlanlar);

    foreach ($joins as $join) {
        $tbl = $join['table'];
        $cols = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? ", [$tbl]);
        $tumAlanlar[$tbl] = array_map(fn($col) => $col->COLUMN_NAME, $cols);
    }

    return view('raporlama.alanlar', [
        'anaTablo' => $anaTablo,
        'joins' => $joins,
        'kriterler' => $kriterler,
        'tumAlanlar' => $tumAlanlar,
        'selectedFields' => $alanlar
    ]);
}


public function listTemplates()
{
    $templates = DB::table('report_templates')->orderBy('created_at', 'desc')->get();

    return view('raporlama.templates', compact('templates'));
}

public function deleteTemplate($id)
{
    DB::table('report_templates')->where('id', $id)->delete();

    return redirect()->route('raporlama.template.list')->with('success', 'Şablon silindi.');
}

public function editTemplate($id)
{
    $template = DB::table('report_templates')->where('id', $id)->first();

    if (!$template) {
        return redirect()->route('raporlama.template.list')->with('error', 'Şablon bulunamadı.');
    }

    $joins = json_decode($template->joins_json, true);
    $fields = json_decode($template->fields_json, true);
    $filters = json_decode($template->filters_json, true);

    // Kullanıcı seçimlerini gösterebilmek için tablo adlarını ve alanlarını alalım
    $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
    $tableList = array_map(fn($t) => $t->TABLE_NAME, $tables);

    $tumAlanlar = [];
    foreach ($tableList as $table) {
        $cols = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?", [$table]);
        $tumAlanlar[$table] = array_map(fn($c) => $c->COLUMN_NAME, $cols);
    }

    return view('raporlama.edit_full', compact('template', 'joins', 'fields', 'filters', 'tableList', 'tumAlanlar'));
}


}
