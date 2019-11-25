<?php
Route::group(['as'=>'admin','prefix'=>'admin','middleware'=>'web','namespace'=>'Ogilo\PhoneBook\Http\Controllers'],function(){
    Route::group(['as'=>'-phonebook','prefix'=>'phonebook','middleware'=>'auth:admin'],function(){
        Route::get('',['as'=>'','uses'=>'PhoneBookController@getDashboard']);
        Route::post('upload',['as'=>'-upload','uses'=>'PhoneBookController@postUpload']);

        Route::group(['as'=>'-contacts','prefix'=>'contacts','middleware'=>'auth:admin'],function(){
            Route::get('',['as'=>'','uses'=>'ContactController@getContacts']);
        	Route::post('vcard',['as'=>'-vcard','uses'=>'ContactController@postVcard']);
            Route::get('delete/{id}',['as'=>'-delete','uses'=>'ContactController@postDelete']);
            Route::get('view/{id}',['as'=>'-view','uses'=>'ContactController@getContact']);
            Route::post('add',['as'=>'-add','uses'=>'ContactController@postAdd']);
        });
    });
});
