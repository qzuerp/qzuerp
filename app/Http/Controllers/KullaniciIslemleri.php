<?php

namespace App\Http\Controllers;
use App\Helpers\FunctionHelpers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KullaniciIslemleri extends Controller
{
  public function index()//açılır pencere ve listelere veri çekmek
  {

    // $query = DB::table('stok00')->select('kod','IUNIT','B_EN','B_BOY','B_YUKSELIK','B_HACIM','B_AGIRLIK','B_ICCAP','B_YOGUNLUK','NORM','GK_1','GK_2','GK_3','GK_4','GK_5','GK_6','GK_7','GK_8','GK_9','GK_10');
    
    // $stok_karti = $query->addSelect('ad')->get();

    $sonID=DB::table('users')->min('id');

    // return view('user');
    return view('user')->with('sonID', $sonID);
  }

  public function logout_user(Request $request)
  {
      $u = Auth::user();
      if(!$u) return response()->json(['error' => 'not_authenticated']);

      $firma = trim($u->firma).'.dbo.';
      $affected1 = DB::table($firma.'sessions')
        ->where('user_id', $request->userID)
        ->delete();


      $affected2 = DB::table('users')
          ->where('id', $request->userID)
          ->update(['is_logged_in' => 0,'remember_token' => null]);

      return response()->json([
          'status' => 'ok',
          'updated_sessions' => $affected1,
          'updated_users' => $affected2
      ]);
  }

  // veri kayıt güncelleme işlemleri
  public function kullaniciOlustur(Request $request) {

    // dd($request->all());
    $name = $request->input('name');
    $name = $request->input('name');
    $email = $request->input('email');
    $password = $request->input('password');
    $perm = $request->input('perm');
    $firma = $request->input('firma');
    
    FunctionHelpers::Logla('USERS',$name,'C');

    DB::table('users')->insert([
      'name' => $name,
      'email' => $email,
      'password' => Hash::make($password),
      'perm' => $perm,
      'read_perm' => 'YOK',
      'write_perm' => 'YOK',
      'delete_perm' => 'YOK',
      'created_at' => date("Y-m-d H:i:s"),
      'firma' => $firma,
    ]);

    $sonID=DB::table('users')->max('id');
    return redirect()->route('user', ['ID' => $sonID]);
  }

  public function kullaniciIslemleri(Request $request) {

    // dd(request()->all());

    $islem_turu = $request->kullanici_islemleri;

    $id = $request->input('kullanici_id_hid');
    $name = $request->input('kullanici_isim');
    $email = $request->input('kullanici_email');
    $password = $request->input('kullanici_sifre');
    $password_hash = Hash::make($password);
    $firma = $request->input('firma').'.dbo.';

    $sifre_durumu = "YOK";

    if(isset($request->kullanici_sifre) && !empty($request->kullanici_sifre)){
      $sifre_durumu = "VAR";
    }

    $perm = $request->kullanici_yetki;

    $read_perm = "YOK";
    $write_perm = "YOK";
    $delete_perm = "YOK";

    $read_perm_arr = $request->input('yetki_read');
    if (isset($read_perm_arr)) {
     $read_perm = implode('|', $read_perm_arr);
    }

    $write_perm_arr = $request->input('yetki_write');
    if (isset($write_perm_arr)) {
      $write_perm = implode('|', $write_perm_arr);
    }

    $delete_perm_arr = $request->input('yetki_delete');
    if (isset($delete_perm_arr)) {
      $delete_perm = implode('|', $delete_perm_arr);
    }

    //echo $id."<br>";
    //echo $read_perm."<br>";
    //echo $write_perm."<br>";
    //echo $delete_perm."<br>";
    //echo $islem_turu."<br>";

    $sonID=DB::table('users')->min('id');

    switch($islem_turu) {

      case 'kullanici_sil':
        
        FunctionHelpers::Logla('USERS',$name,'D');
        DB::table('users')->where('id',$id)->delete();

        print_r("Silme işlemi başarılı.");

        return redirect()->route('user', ['silme' => 'ok']);

        // break;

      case 'kullanici_duzenle':


        FunctionHelpers::Logla('USERS',$name,'W');
        if ($sifre_durumu == "YOK") {
          DB::table('users')->where('id',$id)->update([
            'name'=>$name,
            'email'=>$email,
            'perm'=>$perm,
            'read_perm'=>$read_perm,
            'write_perm'=>$write_perm,
            'delete_perm'=>$delete_perm,
            // 'firma' => $firma,
          ]);

        }

        else {
          DB::table('users')->where('id',$id)->update([
            'name'=>$name,
            'email'=>$email,
            'password'=>$password_hash,
            'perm'=>$perm,
            'read_perm'=>$read_perm,
            'write_perm'=>$write_perm,
            'delete_perm'=>$delete_perm,
            // 'firma' => $firma,
          ]);

        }

        echo "Düzenleme işlemi başarılı.";

        return redirect()->route('user', ['ID' => $id, 'duzenleme' => 'ok']);

        // break;
    }

  }

  public function sifreDegistir(Request $request) {
    $id = auth::id();
    $password = $request->input('kullanici_sifre');
    $password_hash = Hash::make($password);

    DB::table('users')->where('id',$id)->update([
      'password'=>$password_hash,
    ]);

    return view('change_password');

  }


}
