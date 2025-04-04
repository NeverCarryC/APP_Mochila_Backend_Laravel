<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-email', function () {
    $email = 'nishangzhi95@gmail.com'; // send email to nishangzhi95@gmail.com
    Mail::to($email)->send(new PasswordResetMail('123456'));
    return 'Correo enviado exitosamente.';
});
