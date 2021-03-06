<?php

Route::get('/', 'PagesController@upcoming');
Route::get('upcoming', 'PagesController@upcoming'); //some redundancy
Route::get('results/{cardpool?}/{type?}/{country?}', 'PagesController@results');
Route::get('organize', 'PagesController@organize')->name('organize');
Route::get('personal', 'PagesController@personal');
Route::get('profile/{id}', 'PagesController@profile')->name('profile.show');
Route::post('profile/{id}', 'PagesController@updateProfile');
Route::get('about', 'PagesController@about');
Route::get('faq', 'PagesController@faq');
Route::get('markdown', 'PagesController@markdown');
Route::get('badges', 'BadgeController@badges');

Route::get('admin', 'AdminController@lister')->name('admin');
Route::get('admin/identities/update', 'NetrunnerDBController@requestIdentities');
Route::get('admin/cycles/update', 'NetrunnerDBController@requestCycles');
Route::get('admin/packs/update', 'NetrunnerDBController@requestPacks');
Route::get('admin/badges/refresh', 'BadgeController@refreshBadges');

Route::resource('tournaments', 'TournamentsController');
Route::get('tournaments/{id}/approve', 'AdminController@approveTournament');
Route::get('tournaments/{id}/reject', 'AdminController@rejectTournament');
Route::get('tournaments/{id}/restore', 'AdminController@restoreTournament');
Route::get('packs/{id}/enable', 'AdminController@enablePack');
Route::get('packs/{id}/disable', 'AdminController@disablePack');

Route::patch('tournaments/{id}/transfer', 'TournamentsController@transfer');
Route::get('tournaments/{id}/register', 'EntriesController@register');
Route::get('tournaments/{id}/unregister', 'EntriesController@unregister');
Route::post('tournaments/{id}/claim', 'EntriesController@claim');
Route::delete('tournaments/{id}/purge', 'TournamentsController@purge');
Route::delete('entries/{id}', 'EntriesController@unclaim');
Route::delete('entries/anonym/{id}', 'EntriesController@deleteAnonym');
Route::post('entries/anonym', 'EntriesController@addAnonym');
Route::delete('tournaments/{id}/clearanonym', 'TournamentsController@clearAnonym');
Route::post('tournaments/{id}/conclude/manual', 'TournamentsController@concludeManual');
Route::post('tournaments/{id}/conclude/nrtm', 'TournamentsController@concludeNRTM');
Route::get('tournaments/{id}/{slug}', 'TournamentsController@show')->name('tournaments.show.slug');

Route::post('videos', 'VideosController@store');
Route::delete('videos/{id}', 'VideosController@destroy');

Route::get('/oauth2/redirect', 'NetrunnerDBController@login');
Route::get('/logout', 'NetrunnerDBController@logout');

Route::get('/api/tournaments', 'TournamentsController@tournamentJSON');
Route::get('/api/userdecks', 'NetrunnerDBController@getUserDecksJSON');
Route::get('/api/entries', 'EntriesController@entriesJSON');
Route::post('/api/nrtm', 'TournamentsController@NRTMEndpoint');
Route::get('/api/useralert', 'PagesController@getAlertCount');
Route::get('/api/as/{id}', 'ASController@index');
Route::post('/api/badgesseen/{id}', 'BadgeController@changeBadgesToSeen');
Route::get('/api/adminstats', 'AdminController@adminStats');
// iframe for double elimination
Route::get('/elimination', function () { return view('layout.bracket'); });
// https proxies for loading KnowTheMeta data
Route::get('/api/ktmproxy/cardpoolnames', 'KTMProxy@getCardpoolNames');
Route::get('/api/ktmproxy/cardpool/{side}/{pack}', 'KTMProxy@getCardpoolStat');
