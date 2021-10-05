<?php




$route = app()->router;
$route->post('/country/create','CountriesController@create');
$route->get('/country/countryIndex/{lang}','CountriesController@countryIndex');
$route->get('/country/governIndex/{lang}/{countryId}','CountriesController@governIndex');
$route->get('/country/areaIndex/{lang}/{governId}','CountriesController@areaIndex');
$route->get('/country/show/{id}','CountriesController@show');
$route->delete('/country/delete/{id}','CountriesController@delete');
$route->patch('/country/restore/{id}','CountriesController@restore');
$route->patch('/country/edit/{id}','CountriesController@edit');