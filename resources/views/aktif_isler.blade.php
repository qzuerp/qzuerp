@php
    if(Auth::check()) {
      $u = Auth::user();
    }
    $firma = trim($u->firma).'.dbo.';

    $sonIslem = DB::table($firma.'sfdc31t')
    ->select('EVRAKNO', DB::raw('MAX(id) as SON_ID'))
    ->groupBy('EVRAKNO');

    $isler = DB::table($firma.'imlt00 as I00')
    ->leftJoin($firma.'sfdc31e as S31E', 'I00.KOD', '=', 'S31E.TO_ISMERKEZI')

    ->leftJoinSub($sonIslem, 'SON', function ($join) {
        $join->on('S31E.EVRAKNO', '=', 'SON.EVRAKNO');
    })

    ->leftJoin($firma.'sfdc31t as S31T', function ($join) {
        $join->on('S31T.id', '=', 'SON.SON_ID')
             ->where('S31T.ISLEM_TURU', '!=', 'D');
    })

    ->leftJoin($firma.'stok00 as S00', 'S31E.STOK_CODE', '=', 'S00.KOD')

    ->select(
        'I00.AD as TEZGAH_AD',
        'S00.AD as STOK_AD',
        'S31T.*',
        'S31E.*'
    )
    ->get();

@endphp
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tezgah Durum</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes breathe {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.7; transform: scale(1.3); }
    }
    .breathe {
      animation: breathe 2s ease-in-out infinite;
    }
    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .card-enter {
      animation: slideIn 0.4s ease-out forwards;
    }
  </style>
</head>

<body class="bg-gray-50 min-h-screen p-6">

  <div class=" mx-auto">
    <!-- Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5">
      @foreach ($isler as $is)
      @php
          $durum = '';
          $statusColor = '';
          $statusBg = '';
          $dotColor = '';
          $borderColor = '';
          $cardBg = '';
          
          if($is->BITIS_SAATI == null && $is->BITIS_TARIHI == null) {
              if($is->ISLEM_TURU == 'A') {
                  $durum = 'Ayar';
                  $statusColor = 'text-amber-700';
                  $statusBg = 'bg-amber-100';
                  $dotColor = 'bg-amber-500';
                  $borderColor = 'border-amber-300';
                  $cardBg = 'bg-amber-50/30';
              }
              else if($is->ISLEM_TURU == 'U') {
                  $durum = 'Üretim';
                  $statusColor = 'text-emerald-700';
                  $statusBg = 'bg-emerald-100';
                  $dotColor = 'bg-emerald-500';
                  $borderColor = 'border-emerald-300';
                  $cardBg = 'bg-emerald-50/30';
              }
              else if($is->ISLEM_TURU == 'D') {
                  $durum = 'Duruş';
                  $statusColor = 'text-red-700';
                  $statusBg = 'bg-red-100';
                  $dotColor = 'bg-red-500';
                  $borderColor = 'border-red-300';
                  $cardBg = 'bg-red-50/30';
              }
          }
      @endphp
      
      <div class="card-enter {{ $cardBg }} rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border-2 {{ $borderColor }}">
        
        <!-- Header Section -->
        <div class="p-5 pb-4">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
              <div class="{{ $dotColor }} w-3 h-3 rounded-full breathe shadow-lg"></div>
              <span class="{{ $statusColor }} text-sm font-bold uppercase tracking-wide">{{ $durum }}</span>
            </div>
            <div class="{{ $statusBg }} px-3.5 py-1.5 rounded-full {{ $borderColor }} border">
              <span class="{{ $statusColor }} font-bold text-sm">{{ $is->TO_ISMERKEZI }}</span>
            </div>
          </div>

          <!-- Stok Bilgisi -->
          <div class="mb-4">
            <div class="text-gray-900 text-lg font-bold mb-1">{{ $is->STOK_CODE }}</div>
            <div class="text-gray-600 text-sm leading-relaxed line-clamp-2">
              {{ $is->STOK_AD ?? 'Stok bilgisi yok' }}
            </div>
          </div>

          <!-- Divider -->
          <div class="border-t-2 {{ $borderColor }} my-4"></div>

          <!-- Job No -->
          <div class="{{ $statusBg }} rounded-xl p-3 {{ $borderColor }} border">
            <div class="text-gray-500 text-xs font-semibold mb-1 uppercase">Job No</div>
            <div class="{{ $statusColor }} text-base font-bold">{{ $is->JOBNO }}</div>
          </div>
        </div>

      </div>
      @endforeach
    </div>

  </div>

</body>
</html>