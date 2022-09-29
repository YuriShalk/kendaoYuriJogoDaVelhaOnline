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
            } /* else if ($request->id_player) { // player can be in more than one game at the same time
                $game = Game::where('status', '!=', 'DONE')->where('id_owner', '=', $request->id_player)->first();

                if (!$game) {
                    $game = Game::where('status', '!=', 'DONE')->where('id_guest', '=', $request->id_player)->first();
                }
            } */ else {
                return response("invalid parameters", 400);
            }

            if (!$game) {
                return response("game not found", 404);
            }

            // set and hide values

            if ($game->id_owner) {
                $owner = Account::find($game->id_owner);
                unset($owner->id);
                unset($owner->password);

                $game['owner'] = $owner;
                unset($game->id_owner);
            }

            if ($game->id_guest) {
                $guest = Account::find($game->id_guest);
                unset($guest->id);
                unset($guest->password);

                $game['guest'] = $guest;
                unset($game->id_guest);
            }

            if ($game->id_winner) {
                $winner = Account::find($game->id_winner);
                unset($winner->id);
                unset($winner->password);

                $game['winner'] = $winner;
                unset($game->id_winner);
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

            $owner = Account::find($bodyContent->id_owner);

            if (!$owner) {
                return response("owner not found", 422);
            }

            $random_value = rand(0, 1);

            $game = new Game();
            $game->id_owner = $bodyContent->id_owner;
            $game->turn = ($random_value == 1 ? 'OWNER' : 'GUEST');
            $game->status = 'CREATED';
            $game->save();

            // set and hide values

            unset($owner->id);
            unset($owner->password);

            $game['owner'] = $owner;
            unset($game->id_owner);

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

            if ($game->status == 'DONE') {
                return response("game is done", 422);
            }

            if (property_exists($bodyContent, 'id_guest')) {
                // when guest enter in the room

                if ($bodyContent->id_guest == $game->id_owner) {
                    return response("guest is equals to owner", 422);
                }

                $guest = Account::find($bodyContent->id_guest);

                if (!$guest) {
                    return response("guest not found", 422);
                }

                $game->id_guest = $bodyContent->id_guest;
                $game->status = 'STARTED';
            } else if ($request->player_id) {
                // when player marks X or O

                if ($game->status != 'STARTED') {
                    return response("game is not started", 422);
                }

                if ($request->player_id == $game->id_owner) { // when is the owner
                    if ($game->turn != 'OWNER') {
                        return response("invalid turn", 422);
                    }

                    $player = Account::find($game->id_owner);
                } else if ($request->player_id == $game->id_guest) { // when is the guest
                    if ($game->turn != 'GUEST') {
                        return response("invalid turn", 422);
                    }

                    $player = Account::find($game->id_guest);
                } else {
                    return response("invalid player id", 400);
                }

                // TO DO: set and validate X/O position (wins/losses)

                // change turn when game is not DONE
                if ($game->status != 'DONE') {
                    $game->turn = ($game->turn == 'OWNER' ? 'GUEST' : 'OWNER');
                }
            }

            $game->save();

            // set and hide values

            if ($game->id_owner) {
                $owner = Account::find($game->id_owner);
                unset($owner->id);
                unset($owner->password);

                $game['owner'] = $owner;
                unset($game->id_owner);
            }

            if ($game->id_guest) {
                if (!$guest) {
                    $guest = Account::find($game->id_guest);
                }
                unset($guest->id);
                unset($guest->password);

                $game['guest'] = $guest;
                unset($game->id_guest);
            }

            if ($game->id_winner) {
                $winner = Account::find($game->id_winner);
                unset($winner->id);
                unset($winner->password);

                $game['winner'] = $winner;
                unset($game->id_winner);
            }

            return response($game, 200);
        } catch (\Exception $e) {
            return response("error: " . $e->getMessage(), 500);
        }
    }
}