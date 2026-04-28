<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class library extends Controller
{
    public function upload(Request $request) {
        $u = Auth::user();
        $firmaDb = trim($u->firma) . '.dbo.';
        $firma   = trim($u->firma);

        $title = $request->title;
        $description = $request->description;

        $dosya = $request->file('files')[0];
        $ext   = strtolower($dosya->getClientOriginalExtension());
        $boyutByte = $dosya->getSize();

        $hedefKlasor = public_path('dosyalar/' . $firma);
        if (!file_exists($hedefKlasor)) {
            mkdir($hedefKlasor, 0777, true);
        }

        $resimExt = ['jpg','jpeg','png','gif','bmp','webp'];

        if (in_array($ext, $resimExt)) {

            $dosyaAdi = time() . '.webp';

            $manager = new ImageManager(new Driver());

            $image = $manager->read($dosya);
            $image->toWebp(80)
                ->save($hedefKlasor . '/' . $dosyaAdi);

        } else {

            $dosyaAdi = time() . '_' . $dosya->getClientOriginalName();
            $dosya->move($hedefKlasor, $dosyaAdi);
        }

        $kaydedilecekYol = "{$firma}/{$dosyaAdi}";

        DB::table($firmaDb.'LIB00')->insert([
            'BASLIK' => $title,
            'ACIKLAMA' => $description,
            'BOYUT' => $boyutByte,
            'URL' => $kaydedilecekYol,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        return ['success' => true];
    }

    public function list()
    {
        $u = Auth::user();
        $firma = trim($u->firma) . '.dbo.';
        $data = DB::table($firma.'LIB00')->get();
        return ['files' => $data];
    }

    public function delete(Request $request)
    {
        $u = Auth::user();
        $firma = trim($u->firma) . '.dbo.';
        $dosya = DB::table($firma.'LIB00')->where('ID', $request->id)->first();
        DB::table($firma.'LIB00')->where('ID', $request->id)->delete();

        $dosyaYol = public_path('dosyalar/' . $dosya->URL);
        if (file_exists($dosyaYol)) {
            unlink($dosyaYol);
        }

        return ['success' => true];
    }
}