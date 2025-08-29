@section('evrakIslemleri')
  <table style="max-height: 40px !important;">
    <tbody style="max-height: 40px !important;">
      <tr style="max-height: 40px !important;">
        <td width="20">
        </td>
        <td width="30">
          <a data-evrak-kontrol href="@php if (isset($ilkEvrak)) { echo $ekranLink."?ID=".$ilkEvrak;  } else { echo "#"; } @endphp" title="İlk Kart"><i class="fa  fa-angle-double-left fa-2x" style="color: green;font-size: 40px"></i></a>
        </td>
        <td width="30">
          <a data-evrak-kontrol href="@php if (isset($oncekiEvrak)) { echo $ekranLink."?ID=".$oncekiEvrak;  } else { echo "#"; } @endphp" title="Önceki Kart" disabled><i class="fa fa-angle-left fa-2x green" style="color: green;font-size: 40px"></i></a>
        </td>
        <td width="30">
          <a data-evrak-kontrol href="@php if (isset($sonrakiEvrak)) { echo $ekranLink."?ID=".$sonrakiEvrak;  } else { echo "#"; } @endphp" title="Sonraki Kart"><i class="fa  fa-angle-right fa-2x" style="color: green;font-size: 40px"></i></a>
        </td>
        <td width="30">
          <a data-evrak-kontrol href="@php if (isset($sonEvrak)) { echo $ekranLink."?ID=".$sonEvrak;  } else { echo "#"; } @endphp" title="Son Kart"><i class="fa  fa-angle-double-right fa-2x" style="color: green;font-size: 40px"></i></a>
        </td>
        <td width="20">
        </td>

        @if(in_array($ekran, $kullanici_write_yetkileri))
          <td width="40">
            <div id="kartOlustur" name="kartOlustur" style="display: none;">
              <a href="#" class="pull-right" id="kartOlusturBtn" title="Kaydet"><i class="fa fa-save fa-2x" style="color: green" data-bs-toggle="modal" onclick="evrakIslemleri('evrakKaydet',{{ $ekranKayitSatirKontrol }})"></i></a>
            </div>
          </td>
          <td width="40">
            <div id="kartOlustur2" name="kartOlustur2" style="display: none;">
              <a href="#" class="pull-right" title="Vazgeç"><i class="fa-solid fa-square-xmark fa-2x" style="color: orange" onclick="buttonRollback2('{{ $ekranLink }}')"></i></a>
            </div>
          </td>
        @endif

        @if(in_array($ekran, $kullanici_write_yetkileri))
          <td width="40">
            <div id="kartDuzenleme" name="kartDuzenle"></div>
              <a href="#" id="kartDuzenle" class="pull-right" title="Yeni Kart"><i class="fa-solid fa-file-circle-plus fa-2x " style="color: purple;" onclick="inputTemizle2();  if (typeof ozelInput === 'function') { ozelInput(); }"></i></a>
            </div>
          </td>
          <td width="40">
            <div id="kartDuzenle2" name="kartDuzenle2">
              <a href="#" class="pull-right" id="kartDuzenle2Btn" title="Kaydet"><i class="fa fa-save fa-2x" style="color: green" data-bs-toggle="modal" onclick="evrakIslemleri('evrakDuzenle',{{ $ekranKayitSatirKontrol }})"></i></a>
            </div>
          </td>
        @endif

        @if(in_array($ekran, $kullanici_delete_yetkileri))
          <td width="40">
            <div id="kartDuzenle3" name="kartDuzenle3">
              <a href="#" class="pull-right" title="Sil"><i class="fa fa-trash fa-2x" style="color: red" data-bs-toggle="modal" onclick="evrakIslemleri('evrakSil')"></i></a>
            </div>
          </td>
        @endif
        <td width="40">
          <div id="log" name="log">
            <a class="pull-right" data-bs-toggle="modal" data-bs-target="#log"><i class="fa-solid fa-circle-info fa-2x"></i></a>
          </div>
        </td>
        <td width="40">
          <div id="yazdir" name="yazdir" style="display: none;">
            <button  style="border:none; outline:none; background: transparent;" type="submit" class="pull-right smbButton" title="Yazdır" name="kart_islemleri" id="smbButton" value="yazdir"><i class="fa fa-print fa-2x" style="color: green" data-bs-toggle="modal"></i></button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>

  <div style="display: none">
    <button type="submit" class="btn btn-outline" name="kart_islemleri" id="evrakKaydet" value="kart_olustur">Kaydet</button>
    <button type="submit" class="btn btn-outline" name="kart_islemleri" id="evrakDuzenle" value="kart_duzenle">Kaydet</button>
    <button type="submit" class="btn btn-outline"  name="kart_islemleri" id="evrakSil" value="kart_sil">Sil</button>
  </div>

  @if($ekranKayitSatirKontrol == "true")
    <input type="hidden" name="LAST_TRNUM" id="LAST_TRNUM" value="@php if(isset($kart_veri->LAST_TRNUM)) { echo @$kart_veri->LAST_TRNUM; }  else { echo "000000"; } @endphp">
    <input type="hidden" name="LAST_TRNUM2" id="LAST_TRNUM2" value="@php if(isset($kart_veri->LAST_TRNUM2)) { echo @$kart_veri->LAST_TRNUM2; }  else { echo "000000"; } @endphp">
    <input type="hidden" name="LAST_TRNUM3" id="LAST_TRNUM3" value="@php if(isset($kart_veri->LAST_TRNUM3)) { echo @$kart_veri->LAST_TRNUM3; }  else { echo "000000"; } @endphp">
  @endif

@show
