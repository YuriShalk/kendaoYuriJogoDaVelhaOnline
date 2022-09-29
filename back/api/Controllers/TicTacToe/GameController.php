<?php 

namespace App\Http\Controllers\TicTacToe;

use App\Http\Models\TicTacToe\Account;
use App\Http\Models\TicTacToe\Game;
use Illuminate\Http\Request as Request;
use App\Http\Controllers\Controller;

class GameController extends Controller
{
    // private $token = '646200d7-6511-479e-b7e3-1d0efbd23ff9';

    public function get(Request $request) {
        try {
            /* if ($request->header('Authorization') != $this->token) {
                return response("unauthorized", 401);
            } */

            if ($request->id) {
                $game = Game::find($request->id);
            } else {
                return response("invalid parameters", 400);
            }

            if (!$game) {
                return response("game not found", 404);
            }

            return response($game, 200);
        } catch (\Exception $e) {
            return response("error: " . $e->getMessage(), 500);
        }
    }

    public function post(Request $request) {
        try {
            /* if ($request->header('Authorization') != $this->token) {
                return response("unauthorized", 401);
            } */

            $bodyContent = json_decode($request->getContent());

            if (!$bodyContent) {
                return response("empty request body", 400);
            }

            if (!property_exists($bodyContent, 'id_owner')) {
                return response("invalid request body", 400);
            }

            $random_value = rand(0, 1);

            $game = new Game();
            $game->id_owner = $bodyContent->id_owner;
            $game->turn = ($random_value == 1 ? 'OWNER' : 'GUEST');
            $game->save();

            return response($game, 201);
        } catch (\Exception $e) {
            return response("error: " . $e->getMessage(), 500);
        }
    }

    public function patch(Request $request) {
        try {
            /* if ($request->header('Authorization') != $this->token) {
                return response("unauthorized", 401);
            } */

            if (!$request->id) {
                return response("invalid parameters", 400);
            }

            $bodyContent = json_decode($request->getContent());

            if (!$bodyContent) {
                return response("empty request body", 400);
            }
            
            $game = Game::find($request->id);

            if (!$game) {
                return response("game not found", 404);
            }

            if (property_exists($bodyContent, 'id_guest')) {
                $game->id_guest = $bodyContent->id_guest;
            }

            $game->save();

            return response($game, 200);
        } catch (\Exception $e) {
            return response("error: " . $e->getMessage(), 500);
        }
    }
}