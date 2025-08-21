<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content "ie=edge">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=roboto:wght@300;400;500;700&display=swap" />
    <link rel="stylesheet" href="css/mdb.min.css" />
    <title>deneme</title>
  </head>
  <body>
    <div class="col-md-16">


      <div class="col-md-6">
        <table class="table table-sm">
          <thead>
            <tr>
                <th scope="col">Kod</th>
                <th scope="col">Ad</th>
            </tr>
          </thead>
          <tbody>
            @foreach($stok_karti as $key  => $stok_kart)
              <tr>
                  <th scope="row">{{ $stok_kart->kod }}</th>
                  <th scope="col">{{ $stok_kart->ad }}</th>
              </tr>
            @endforeach
          </tbody>
      </table>
    </div>
    <div class="col-xs-4">
      <!-- <label>Bul</label> -->
      <select id="BUL" class="form-control select2 select2-hidden-accessible" onchange="tezgah_getir()" style="width: 100%; height: 30PX" name="BUL" tabindex="-1"  >
        <option>Bul</option>
        @foreach($stok_karti as $key  => $stok_kart)
        <option value="{{ $stok_kart->kod }}">{{ $stok_kart->kod }} __ {{ $stok_kart->ad }} </option>
        @endforeach
          </div>
    <div class="contaier mt-5">
      <form method="get" action="{{url('stok_kayit')}}">
          <div class="form-group">
              <label>Stok Kodu</label>
              <input type="text" class="form-control" name="s_kod">
            </div>
            <div class="form-group">
              <label>Stok Adı</label>
              <input type="text" class="form-control" name="s_ad">
            </div>


          <button type="submit" class="btn btn-primary">Kaydet</button>
        </form>
  </div>
  </body>
  <script type="text/javascript" src="js/mdb.min.js"></script>
</html>
