<?php namespace App\Http\Models\TicTacToe;

use Illuminate\Database\Eloquent\Model;

class Game extends Model {
    protected $table = 'tictactoe_game';
    protected $fillable = ['id', 'id_owner', 'id_guest', 'id_winner', 'first_position', 'second_position', 'third_position', 'fourth_position', 'fifth_position', 'sixth_position', 'seventh_position', 'eighth_position', 'nineth_position', 'turn', 'status', 'created_at', 'updated_at'];
    protected $casts = [
        'id' => 'integer'
    ];
}
