<?php
Route::group(['as'=>'admin','prefix'=>'admin','middleware'=>'web','namespace'=>'Ogilo\PhoneBook\Http\Controllers'],function(){
    Route::group(['as'=>'-phonebook','prefix'=>'phonebook','middleware'=>'auth:admin'],function(){
        Route::get('',['as'=>'','uses'=>'PhoneBookController@getDashboard']);
    });
});
