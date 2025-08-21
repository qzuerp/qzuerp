<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KartVeriRequest extends FormRequest
{

    public function authorize()
    {
        // Bu alanı true yaparak isteği yetkilendirebilirsiniz
        return true;
    }

    public function rules()
    {
        return [
            'KOD' => 'required|unique:kart_veri,KOD|max:16', // Veritabanındaki 'kart_veri' tablosunun 'KOD' sütununda benzersizlik kontrolü
        ];
    }

    public function messages()
    {
        return [
            'KOD.unique' => 'Bu kod zaten kaydedilmiş. Lütfen farklı bir kod giriniz.',
        ];
    }
}
