<?php namespace App\Http\Models\TicTacToe;

use Illuminate\Database\Eloquent\Model;

class Account extends Model {
	protected $table = 'tictactoe_account';
	protected $fillable = ['id', 'username', 'password', 'created_at', 'updated_at'];
    protected $casts = [
        'id' => 'string'
    ];
}
