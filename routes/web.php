<?php
Route::get('mail', function () {
    Mail::to(App\User::where('email', 'riyadbmcb@gmail.com')->first())
        ->send(new \App\Mail\GroupShared(App\User::where('email', 'riyadbmcb@gmail.com')->first()));
});

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
Auth::routes([
    'verify' => true,
    'register' => env('REGISTRATION') ?? true,
]);
Route::get('/', 'WelcomeController@show');

Route::middleware('verified')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/home', 'HomeController@show');
        Route::get('/change-password', 'Auth\ChangePasswordController@showPasswordChangeForm')->name('password.change');
        Route::put('/change-password', 'Auth\ChangePasswordController@changePassword')->name('password.change.update');
//    Route::get('/dashboard/{any?}', 'HomeController@index')->where('any', '.*')->name('dashboard');
        Route::get('/app/{any?}', 'ProjectController@any')->where('any', '.*')->name('app');
    });

    Route::prefix('ajax')->middleware('auth')->name('ajax.')->namespace('Ajax')->group(function () {
        Route::get('/check-login', 'AuthController@checkLogin')->name('check.login');

        Route::get('/dashboard-data', 'HomeController@getDashboardData')->name('dashboard-data');

        Route::get('/projects', 'ProjectController@getAll')->name('projects.get.all');
        Route::delete('/projects/{project}', 'ProjectController@delete')->name('projects.delete');
        Route::get('/auth/projects', 'ProjectController@authGetAll')->name('auth.projects.get.all');
        Route::get('/auth/projects/{project}', 'ProjectController@authFind')->name('auth.projects.find');
        Route::post('/projects', 'ProjectController@store')->name('projects.store');
        Route::put('projects/{project}/status', 'ProjectController@updateStatus')->name('projects.status.update');
        Route::put('projects/{project}', 'ProjectController@update')->name('projects.update');

        Route::get('auth/groups/{group}', 'GroupController@authFind')->name('projects.groups.find');
        Route::get('/projects/{id}/groups', 'GroupController@getAll')->name('projects.groups.get.all');
        Route::get('auth/projects/{project}/groups', 'GroupController@authGetAll')->name('auth.projects.groups.get.all');
        Route::post('/projects/{project}/groups', 'GroupController@store')->name('projects.groups.store');
        Route::put('projects/{project}/groups/{group}', 'GroupController@update')->name('projects.groups.update');

        Route::get('/cred-keys', 'CredentialController@getCredKeys')->name('cred.get.keys');
        Route::get('/credential-types', 'CredentialTypeController@getAll')->name('cred-types.get.all');
        Route::get('/groups/{group_id}/credentials', 'CredentialController@getAll')->name('projects.groups.credentials');
        Route::get('/auth/groups/{group}/credentials', 'CredentialController@authGetAll')->name('auth.projects.groups.credentials');
        Route::post('/groups/{group_id}/credentials', 'CredentialController@store')->name('projects.groups.credentials.store');
        Route::put('/groups/{group_id}/credentials/{cred_id}', 'CredentialController@update')->name('projects.groups.credentials.update');
        Route::put('/groups/{group_id}/credentials/{cred_id}/accessibility', 'CredentialController@updateAccessibility')->name('projects.groups.credentials.update.accessibility');
        Route::delete('/groups/{group_id}/credentials/{cred_id}', 'CredentialController@delete')->name('projects.groups.credentials.delete');

        Route::get('/groups/{group_id}/users', 'GroupController@getGroupUsers')->name('projects.groups.users');
        Route::get('auth/groups/{group}/users', 'GroupController@getAuthGroupUsers')->name('projects.auth.groups.users');
        Route::post('/groups/{group}/users', 'GroupController@addGroupUser')->name('projects.groups.users.store');
        Route::delete('/groups/{group}/users/{id}', 'GroupController@deleteGroupUser')->name('projects.groups.users.destroy');
        Route::get('/groups/{group}/shareable-users', 'GroupController@getGroupSharableUsers')->name('projects.groups.users.shareable');
    });
});


