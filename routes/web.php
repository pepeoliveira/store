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

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});


// HOME PAGE
Route::get('/','IndexController@index');

Route::match(['get','post'],'/admin','AdminController@login');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// CATEGORY / LISTING PAGE
Route::get('products/{url}','ProductsController@products');

//Product Detail Page
Route::get('product/{id}','ProductsController@product');

//Get Product Attribute PRice
Route::any('/get-product-price','ProductsController@getProductPrice');

Route::group(['middleware' => ['auth']], function(){

    Route::get('/admin/dashboard','AdminController@dashboard');
    Route::get('/admin/settings','AdminController@settings');
    Route::get('/admin/check-pwd','AdminController@chkPassword');
    Route::match(['get','post'],'/admin/update-pwd','AdminController@updatePassword');

    //Categories Routes(Admin)
    Route::match(['get','post'],'/admin/add-category','CategoryController@addCategory');
    Route::match(['get','post'],'/admin/edit-category/{id}','CategoryController@editCategory');
    Route::match(['get','post'],'/admin/delete-category/{id}','CategoryController@deleteCategory');
    Route::get('/admin/view-categories','CategoryController@viewCategories');

    //Products Routes(Admin)
    Route::match(['get','post'],'/admin/edit-product/{id}','ProductsController@editProduct');
    Route::match(['get','post'],'/admin/add-product','ProductsController@addProduct');
    Route::get('/admin/view-products','ProductsController@viewProducts');
    Route::get('/admin/delete-product-image/{id}','ProductsController@deleteProductImage');
    Route::match(['get','post'],'/admin/delete-product/{id}','ProductsController@deleteProduct');

    //Products Attributes Routes
    Route::match(['get','post'],'/admin/add-attributes/{id}','ProductsController@addAttribute');
    Route::get('/admin/delete-attribute/{id}','ProductsController@deleteAttribute');


} );

Route::get('/logout','AdminController@logout');


