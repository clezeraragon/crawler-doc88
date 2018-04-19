<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
$teste = [
    0 => "aneel/nota_tecnica/nreh20172322.pdf",
    1 => "aneel/nota_tecnica/nreh20172323.pdf",
    2 => "aneel/nota_tecnica/nreh20172324.pdf",
    3 => "aneel/nota_tecnica/nreh20172325.pdf",
    4 => "aneel/nota_tecnica/nreh20172326.pdf",
    5 => "aneel/nota_tecnica/nreh20172327.pdf",
    6 => "aneel/nota_tecnica/nreh20172328.pdf",
    7 => "aneel/nota_tecnica/nreh20172329.pdf",
    8 => "aneel/nota_tecnica/nreh20172330.pdf",
    9 => "aneel/nota_tecnica/nreh20172331.pdf",
    10 => "aneel/nota_tecnica/nreh20172333.pdf",
    11 => "aneel/nota_tecnica/nreh20172334.pdf",
    12 => "aneel/nota_tecnica/nreh20172335.pdf",
    13 => "aneel/nota_tecnica/nreh20172352.pdf",
    14 => "aneel/nota_tecnica/nreh20172360.pdf",
    15 => "aneel/nota_tecnica/nreh20172362.pdf",
    16 => "aneel/nota_tecnica/nreh20172365.pdf",
    17 => "aneel/nota_tecnica/nreh20182384.pdf",
    18 => "aneel/nota_tecnica/reh20172331ti.pdf",
    ];

    return view('welcome');
});

Route::get('/proinfa', 'AneelController@proInfa')->name('proinfa');
Route::get('/conta-desenv-energ', 'AneelController@contaDesenvEnerg')->name('conta_desenv_energ');


Route::prefix('ons')->group(function () {
    Route::get('/sdro-semanal', 'OnsController@sdroSemanal')->name('sdro_semanal');
    Route::get('/mlt-enas', 'OnsController@mltEnas')->name('mtl_enas');
    Route::get('/sdro-diario', 'OnsController@sdroDiario')->name('sdro_diario');
    Route::get('/mlt-enas-semanal', 'OnsController@operacaoEnasSemanal')->name('mlt_enas_semanal');

});
Route::prefix('ccee')->group(function () {
    Route::get('/historico-semanal', 'CceeController@historicoPrecoSemanal')->name('historico_semanal');

});

Route::get('/arango', 'ArangoDbController@index')->name('arango');