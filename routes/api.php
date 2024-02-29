<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\LogOut;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// ROTTE PUBBLICHE //

// Registrazione Utente Semplice
Route::post('/register', 'App\Http\Controllers\AuthController@register');

// Login
Route::post('/login', 'App\Http\Controllers\AuthController@login');

// Releases Recenti 
Route::get('/recent-releases', 'App\Http\Controllers\PublicRoutesController@recent');

// Lista Nomi Nazioni
Route::get('/nations-list', 'App\Http\Controllers\NationsController@show');

// Lista Codice Iso Nazioni
Route::get('/nations-isocodes', 'App\Http\Controllers\NationsController@showIsoCodes');

// Codice Iso Specifico Nazione
Route::get('/nation-isocode/{nation}', 'App\Http\Controllers\NationsController@showIsoCode');

// Lista Codice Telefono Nazioni
Route::get('/nations-phonecodes', 'App\Http\Controllers\NationsController@showPhoneCodes');

// Codice Telefono Specifico Nazione
Route::get('/nation-phonecode/{nation}', 'App\Http\Controllers\NationsController@showPhoneCode');

// Lista Nomi Comuni
Route::get('/comuni-list', 'App\Http\Controllers\ComuniController@showComuniNames');

// Lista Regioni
Route::get('/regions-list', 'App\Http\Controllers\ComuniController@showRegioniNames');

// Lista Città
Route::get('/cities-list', 'App\Http\Controllers\ComuniController@showCities');

// Lista Province
Route::get('/provinces-list', 'App\Http\Controllers\ComuniController@showProvinces');

// Lista Codici Postali
Route::get('/postalcodes-list', 'App\Http\Controllers\ComuniController@showPostal');


// ROTTE PER ADMIN E USERS //

Route::middleware('checkRoleAndToken:admin,user')->group(function () {

    // Logout
    Route::middleware([LogOut::class])->post('/logout', function () {
    });


    ##### PROFILO UTENTE #####

    // Visualizza profilo utente
    Route::get('/user/profile', 'App\Http\Controllers\UserProfileController@showProfile');

    // Cancella Profilo (soft delete)
    Route::delete('/user/delete-profile', 'App\Http\Controllers\UserProfileController@deleteProfile');

    // Modifica profilo utente con "fill" su tutti i campi
    Route::post('/user/profile-modify', 'App\Http\Controllers\UserProfileController@modifyProfile');

    // Cambia Username
    Route::put('/user/update-username/', 'App\Http\Controllers\UserProfileController@updateUsername');

    // Cambia E-mail
    Route::put('/user/update-email/', 'App\Http\Controllers\UserProfileController@updateEmail');

    // Cambia Password
    Route::put('/user/update-password/', 'App\Http\Controllers\UserProfileController@updatePassword');

    // Aggiungi Crediti
    Route::put('/user/update-credits/', 'App\Http\Controllers\UserProfileController@updateCredits');

    // Aggiungi ai preferiti
    Route::post('/add-favourites/{type}/{item_id}', 'App\Http\Controllers\UserFavouritesController@addItem');

    // Rimuovi Dai Preferiti
    Route::delete('/delete-favourite/{type}/{item_id}', 'App\Http\Controllers\UserFavouritesController@deleteFavourite');

    // Lista Preferiti
    Route::get('/list-favourites', 'App\Http\Controllers\UserFavouritesController@listFavourites');



    ##### CATEGORIE LIVELLO UTENTE #####

    // Visualizza tutte le categorie
    Route::get('/categories', 'App\Http\Controllers\CategoryController@index');

    // Visualizza Categoria Specifica
    Route::get('/category/{id}', 'App\Http\Controllers\CategoryController@showCategory');



    ##### FILMS #####

    // Visualizza tutti i film
    Route::get('/movies', 'App\Http\Controllers\MovieController@index');

    // Visualizza dettagli di un film specifico
    Route::get('/movies/{id}', 'App\Http\Controllers\MovieController@show');

    // Filtra film per categoria
    Route::get('/movies/category/{category}', 'App\Http\Controllers\MovieController@filterByCategory');

    // Guarda il film
    Route::post('/movies/{id}/start-watching', 'App\Http\Controllers\MovieController@startWatching');



    ##### SERIE TV #####

    // Visualizza tutte le serie TV
    Route::get('/tv-series', 'App\Http\Controllers\TvSeriesController@index');

    // Visualizza dettagli di una serie TV specifica
    Route::get('/tv-series/{id}', 'App\Http\Controllers\TvSeriesController@show');

    // Filtra Serie Tv Per Categoria
    Route::get('/tv-series/category/{category}', 'App\Http\Controllers\TvSeriesController@filterByCategory');

    // Visualizza Stagioni di una serie TV
    Route::get('/tv-series/{id}/seasons', 'App\Http\Controllers\SeasonsController@seasons');

    // Visualizza gli episodi di una stagione
    Route::get('/tv-series/{id}/{seasonsId}', 'App\Http\Controllers\SeasonsController@seasonDetails');

    // Guarda Episodio
    Route::post('/tv-series/{id}/{seasonsId}/{episodeId}/start-watching/', 'App\Http\Controllers\EpisodeController@startWatching');
});


// ROTTE ADMIN //

Route::middleware('checkRoleAndToken:admin')->group(function () {

    Route::prefix('/admin')->group(function () {


        ##### MODIFICHE SU UTENTI LIVELLO ADMIN #####

        // Lista Utenti Completa
        Route::get('/users', 'App\Http\Controllers\UserController@index');

        // Lista Utenti Bannati
        Route::get('/users/banned-list', function () {

            $bannedUsers = User::where('banned', 1)->get();

            return response()->json(['banned_users: ' => $bannedUsers]);
        });

        // Lista Utenti Cancellati
        Route::get('/deleted-users', function () {

            $deletedUsers = User::onlyTrashed()->get();

            return response()->json(['deleted_users' => $deletedUsers], 200);
        });

        // Utente Specifico Livello Admin
        Route::get('/users/user-profile/{name}', 'App\Http\Controllers\UserController@show');

        // Modifica Utente Livello Admin
        Route::put('/users/user-modify/{name}', 'App\Http\Controllers\UserController@update');

        // Banna Utente Livello Admin
        Route::post('/users/ban-user/{id}', 'App\Http\Controllers\UserController@banUser');

        // Rimuovi Ban Utente Livello Admin
        Route::post('/users/unban-user/{id}', 'App\Http\Controllers\UserController@unbanUser');

        // Elimina Utente Livello Admin
        Route::delete('/users/{id}', 'App\Http\Controllers\UserController@destroy');

        // Ripristina Utente Cancellato Livello Admin
        Route::patch('/users/{id}/restore', 'App\Http\Controllers\UserController@restore');

        // Creazione Utente Admin
        Route::post('/create-admin', 'App\Http\Controllers\AuthController@createAdmin');



        ##### MODIFICHE SU FILM LIVELLO ADMIN #####

        // Inserisci Nuovo Film
        Route::post('/upload-new-movie', 'App\Http\Controllers\MovieController@storeNewMovie');

        // Modifica Film (Anno, Attori, Descrizione etc)
        Route::post('modify-movie/{id}', 'App\Http\Controllers\MovieController@modifyMovie');

        // Cancella Film (softDelete)
        Route::delete('/delete-movie/{id}', 'App\Http\Controllers\MovieController@destroyMovie');

        // Ripristina Film
        Route::patch('/restore-movie/{id}', 'App\Http\Controllers\MovieController@restoreMovie');

        // Lista Film Cancellati
        Route::get('/deleted-movies-list', 'App\Http\Controllers\MovieController@listDeletedMovies');




        ##### MODIFICHE SU TV SERIES LIVELLO ADMIN #####

        // Inserisci Nuova Serie Tv
        Route::post('/upload-new-tvseries', 'App\Http\Controllers\TvSeriesController@storeNewTvSeries');

        // Cancella Serie Tv
        Route::delete('/delete-tvseries/{id}', 'App\Http\Controllers\TvSeriesController@destroyTvSeries');

        // Ripristina Serie Tv
        Route::patch('/restore-tvseries/{id}', 'App\Http\Controllers\TvSeriesController@restoreTvSeries');

        // Lista Serie Tv Cancellate
        Route::get('/deleted-tvseries-list', 'App\Http\Controllers\TvSeriesController@listDeletedTvSeries');

        // Modifica Serie Tv
        Route::post('/modify-tvseries/{id}', 'App\Http\Controllers\TvSeriesController@modifyTvSeries');

        // Inserisci Nuova Stagione Di Una Serie Tv
        Route::post('/upload-new-season', 'App\Http\Controllers\TvSeriesController@storeNewSeason');

        // Cancella Stagione
        Route::delete('/delete-season/{tv_series_id}/{number}', 'App\Http\Controllers\TvSeriesController@deleteSeason');

        // Ripristina Stagione
        Route::patch('/restore-season/{tv_series_id}/{number}', 'App\Http\Controllers\TvSeriesController@restoreSeason');

        // Lista Stagioni Cancellate
        Route::get('/deleted-seasons-list', 'App\Http\Controllers\TvSeriesController@listDeletedSeasons');

        // Modifica Stagione
        Route::post('/modify-season/{tv_series_id}/{number}', 'App\Http\Controllers\TvSeriesController@modifySeason');

        // Inserisci Nuovo Episodio Di Una Stagione
        Route::post('/upload-new-episode', 'App\Http\Controllers\TvSeriesController@storeNewEpisode');

        // Cancella Episodio
        Route::delete('/delete-episode/{ep_number}/{season_id}', 'App\Http\Controllers\TvSeriesController@deleteEpisode');

        // Ripristina Episodio
        Route::patch('/restore-episode/{ep_number}/{season_id}', 'App\Http\Controllers\TvSeriesController@restoreEpisode');

        // Lista Episodi Cancellati
        Route::get('/deleted-episodes-list', 'App\Http\Controllers\TvSeriesController@listDeletedEpisodes');

        // Modifica Episodio
        Route::post('/modify-episode/{ep_number}/{season_id}', 'App\Http\Controllers\TvSeriesController@modifyEpisode');



        ##### MODIFICHE CATEGORIE #####

        // Inserisci Nuova Categoria
        Route::post('/add-category', 'App\Http\Controllers\CategoryController@storeNewCategory');

        // Elimina Categoria
        Route::delete('/delete-category/{id}', 'App\Http\Controllers\CategoryController@deleteCategory');

        // Ripristina Categoria
        Route::patch('/restore-category/{id}', 'App\Http\Controllers\CategoryController@restoreCategory');

        // Lista Categorie Cancellate
        Route::get('/deleted-categories-list','App\Http\Controllers\CategoryController@listDeletedCategories');

        // Modifica Categoria
        Route::post('/modify-category/{id}', 'App\Http\Controllers\CategoryController@modifyCategory');

    });
});
