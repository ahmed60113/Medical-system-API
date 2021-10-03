<?php




$route = app()->router;
$route->post('/country/create','CountriesController@create');
$route->get('/country/index/{lang}/{type}','CountriesController@index');
$route->get('/country/show/{id}','CountriesController@show');
$route->delete('/country/delete/{id}','CountriesController@delete');
$route->patch('/country/restore/{id}','CountriesController@restore');
$route->patch('/country/edit/{id}','CountriesController@edit');