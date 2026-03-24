<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Redirect;
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (QueryException $e, $request) {
            // SQL Server'dan gelen o özel trigger mesajını yakala
            if (str_contains($e->getMessage(), 'Stok eksiye düşüyor')) {
                dd('Buraya girdi!');
                // Eğer AJAX/API isteği ise JSON dön
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Stok yetersiz, işlem iptal edildi!'], 422);
                }
    
                // Normal web isteği ise geldiği sayfaya hata mesajıyla dön
                return Redirect::back()
                    ->withErrors(['stok_hatasi' => 'Dikkat! Stok eksiye düşüyor, işlem engellendi.'])
                    ->withInput();
            }
        });
    }
}
