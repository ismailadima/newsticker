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

Route::middleware('checkaccess')->group(function(){
    Route::get('/', 'NewstickerController@index');

    Route::group(['prefix' => 'newstickers', 'as' => 'newstickers.'], function () {
        Route::get('/', 'NewstickerController@index')->name('index');
        Route::post('/', 'NewstickerController@index')->name('index.post');

        Route::get('/create', 'NewstickerController@create')->name('view.create');
        Route::post('/store', 'NewstickerController@store')->name('store');
        Route::get('/edit/{newsticker}', 'NewstickerController@edit')->name('view.edit');
        Route::post('/edit/{newsticker}', 'NewstickerController@update')->name('update');
        Route::post('/publish', 'NewstickerController@publish')->name('publish');
        Route::post('/publish_today', 'NewstickerController@publish_today')->name('publish.today');
        Route::post('/nonactive-data/{data}', 'NewstickerController@nonactiveData')->name('publish.nonactive');
        Route::get('/check-data-exist-date/{date}', 'NewstickerController@checkExistingDateNewsticker')->name('check-data-exist-bydate');
        Route::post('/update-line/{newsticker}', 'NewstickerController@updateLine')->name('update.line');
        Route::post('/delete-line/{newsticker}', 'NewstickerController@deleteLine')->name('delete.line');
    });

    Route::group(['prefix' => 'newstickers-inews', 'as' => 'newstickers-inews.'], function () {
        Route::get('/', 'NewstickerInewsController@index')->name('index');
        Route::post('/store', 'NewstickerInewsController@store')->name('store');
        Route::get('/edit/{newsticker}', 'NewstickerInewsController@edit')->name('view.edit');
        Route::post('/edit/{newsticker}', 'NewstickerInewsController@update')->name('update');
        Route::post('/update-line/{newsticker}', 'NewstickerInewsController@updateLine')->name('update.line');
        Route::post('/delete-line/{newsticker}', 'NewstickerInewsController@deleteLine')->name('delete.line');
    });

    Route::group(['prefix' => 'rt_special', 'as' => 'rt_special.'], function () {
        Route::get('/', 'RunningTextSpecialController@index')->name('index');
        Route::post('/', 'RunningTextSpecialController@index')->name('index.post');

        Route::get('/create', 'RunningTextSpecialController@create')->name('view.create');
        Route::post('/store', 'RunningTextSpecialController@store')->name('store');
        Route::get('/edit/{newsticker}', 'RunningTextSpecialController@edit')->name('view.edit');
        Route::post('/edit/{newsticker}', 'RunningTextSpecialController@update')->name('update');
        Route::post('/publish', 'RunningTextSpecialController@publish')->name('publish');
        Route::post('/nonactive-data/{data}', 'RunningTextSpecialController@nonactiveData')->name('publish.nonactive');
        Route::get('/check-data-exist-date/{date}', 'RunningTextSpecialController@checkExistingDateNewsticker')->name('check-data-exist-bydate');
        Route::post('/update-line/{newsticker}', 'RunningTextSpecialController@updateLine')->name('update.line');
        Route::post('/delete-line/{newsticker}', 'RunningTextSpecialController@deleteLine')->name('delete.line');
    });

    Route::group(['prefix' => 'logs', 'as' => 'logs.'], function () {
        Route::get('/newstickers', 'LogController@newstickersIndex')->name('newstickers.index');
        Route::post('/newstickers', 'LogController@newstickersIndex')->name('newstickers.search');
    });

    //ACL
    Route::group(['prefix' => 'acl', 'as' => 'acl.', 'middleware' => 'cek.status.admin'], function () {
        /***** USER ******/
        Route::get('user', 'AdminAcl\UserController@index')->name('user');
        Route::get('user/{user}', 'AdminAcl\UserController@userShow')->name('user.show');
        Route::post('user', 'AdminAcl\UserController@userStore')->name('user.store');
        Route::post('user/{user}', 'AdminAcl\UserController@userUpdate')->name('user.update');
        Route::delete('user/{user}', 'AdminAcl\UserController@userDelete')->name('user.delete');
    });


    //Command Section
    Route::group(['prefix' => 'command', 'as' => 'command.'], function () {
        Route::get('/autopublishgtv', function () {
            Artisan::call('autopublish:gtv');
        });
        Route::get('/autopublishmnctv', function () {
            Artisan::call('autopublish:mnctv');
        });
        Route::get('/autopublishrcti', function () {
            Artisan::call('autopublish:rcti');
        });

        Route::get('/autodeleteline', function () {
            Artisan::call('autodeleteline');
        });
    });

});



Route::get('/login', 'UserController@login');
Route::post('/login', 'UserController@login_process');
Route::get('/logout', 'UserController@logout');


////////////////////////////////// Artisan Call via Route
require_once('artisan_routes.php');