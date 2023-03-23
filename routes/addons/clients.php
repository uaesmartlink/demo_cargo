<?php
Route::get('client/register','ClientController@register')->name('client.register');
Route::post('client/register','ClientController@save')->name('client.save');
Route::post('client/new-address','ClientController@addNewAddress')->name('client.add.new.address');
Route::get('client/get-address','ClientController@getOneAddress')->name('client.get.one.address');

Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'user_role:admin|staff|branch']], function(){
	//Update Routes
    Route::resource('clients','ClientController',[
        'as' => 'admin'
    ]);
    Route::get('/client/get','ClientController@getClientByCode')->name('client.get.byCode');
    Route::get('/client/get/state','ClientController@getState')->name('client.get.state');
    Route::get('/client/get/area','ClientController@getArea')->name('client.get.area');

});

Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'user_role:admin|staff']], function(){
    Route::get('clients/delete/{client}','ClientController@destroy')->name('admin.clients.delete-client');
});
