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


    return view('welcome');
});
//
//Route::get('/', function () {
//
//
//$crawler = $client->request('GET', 'http://www2.aneel.gov.br/cedoc/dsp2018573ti.pdf');
////Storage::download($crawler->getBaseHref());
//$cookieJar = $client->getCookieJar();
//$guzzleClient = $client->getClient();
//$jar = GuzzleHttp\Cookie\CookieJar::fromArray($cookieJar->all(), 'http://www.unicamp.br');
//$response = $guzzleClient->get($crawler->getBaseHref(), ['cookies' => $jar, 'save_to' => storage_path('app/public').'/teste.pdf']);
//Storage::download($response);
//dump($response);
//    return view('welcome');
//});
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