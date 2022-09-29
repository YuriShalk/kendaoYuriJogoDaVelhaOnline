<?php

// TicTacToe

Route::group(['prefix' => 'tictactoe'], function() {
	// Account

	Route::get('/accounts', [
		'as' => 'tictactoe.account.get', 'uses' => 'TicTacToe\AccountController@get'
	])->middleware('cors');

	Route::get('/accounts/{id}', [
		'as' => 'tictactoe.account.get', 'uses' => 'TicTacToe\AccountController@get'
	])->middleware('cors');

	Route::post('/accounts', [
		'as' => 'tictactoe.account.post', 'uses' => 'TicTacToe\AccountController@post'
	])->middleware('cors');

	Route::patch('/accounts', [
		'as' => 'tictactoe.account.patch', 'uses' => 'TicTacToe\AccountController@patch'
	])->middleware('cors');

	Route::patch('/accounts/{id}', [
		'as' => 'tictactoe.account.patch', 'uses' => 'TicTacToe\AccountController@patch'
	])->middleware('cors');

	Route::delete('/accounts', [
		'as' => 'tictactoe.account.delete', 'uses' => 'TicTacToe\AccountController@delete'
	])->middleware('cors');
    
	Route::delete('/accounts/{id}', [
		'as' => 'tictactoe.account.delete', 'uses' => 'TicTacToe\AccountController@delete'
	])->middleware('cors');

	// Game

	Route::get('/games', [
		'as' => 'tictactoe.game.get', 'uses' => 'TicTacToe\GameController@get'
	])->middleware('cors');

	Route::get('/games/{id}', [
		'as' => 'tictactoe.game.get', 'uses' => 'TicTacToe\GameController@get'
	])->middleware('cors');

	Route::post('/games', [
		'as' => 'tictactoe.game.post', 'uses' => 'TicTacToe\GameController@post'
	])->middleware('cors');

	Route::patch('/games', [
		'as' => 'tictactoe.game.patch', 'uses' => 'TicTacToe\GameController@patch'
	])->middleware('cors');

	Route::patch('/games/{id}', [
		'as' => 'tictactoe.game.patch', 'uses' => 'TicTacToe\GameController@patch'
	])->middleware('cors');
});
