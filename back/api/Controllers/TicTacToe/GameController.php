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
            }
            unset($game->id_owner);

            if ($game->id_guest) {
                $guest = Account::find($game->id_guest);
                unset($guest->id);
                unset($guest->password);

                $game['guest'] = $guest;
            }
            unset($game->id_guest);

            if ($game->id_winner) {
                $winner = Account::find($game->id_winner);
                unset($winner->id);
                unset($winner->password);

                $game['winner'] = $winner;
            }
            unset($game->id_winner);

            return response($game, 200);
        } catch (\Exception $e) {
            return response("error: " . $e->getMessage(), 500);
        }
    }

    public function create(Request $request) {
        try {
            /* if ($request->header('Authorization') != $this->token) {
                return response("unauthorized", 401);
            } */

            $bodyContent = json_decode($request->getContent());

            if (!$bodyContent) {
                return response("empty request body", 400);
            }

            if (!property_exists($bodyContent, 'id_player')) {
                return response("invalid request body", 400);
            }

            $owner = Account::find($bodyContent->id_player);

            if (!$owner) {
                return response("owner not found", 422);
            }

            $random_value = rand(0, 1);

            $game = new Game();
            $game->id_owner = $bodyContent->id_player;
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

    // when player enter in the room
    public function join(Request $request) {
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

            if (!property_exists($bodyContent, 'id_player')) {
                return response("invalid request body", 400);
            }

            $game = Game::find($request->id);

            if (!$game) {
                return response("game not found", 404);
            }

            if ($game->status == 'DONE') {
                return response("game is done", 422);
            }

            if ($bodyContent->id_player != $game->id_owner) {
                if ($game->id_guest == null) {
                    $guest = Account::find($bodyContent->id_player);

                    if (!$guest) {
                        return response("player not found", 422);
                    }

                    $game->id_guest = $guest->id;
                    $game->status = 'STARTED';

                    $game->save();
                } else if ($game->id_guest != $bodyContent->id_player) {
                    return response("game is full", 422);
                }
            }

            // set and hide values

            if ($game->id_owner) {
                $owner = Account::find($game->id_owner);
                unset($owner->id);
                unset($owner->password);

                $game['owner'] = $owner;
            }
            unset($game->id_owner);

            if ($game->id_guest) {
                if (!$guest) {
                    $guest = Account::find($game->id_guest);
                }
                unset($guest->id);
                unset($guest->password);

                $game['guest'] = $guest;
            }
            unset($game->id_guest);

            /* if ($game->id_winner) {
                $winner = Account::find($game->id_winner);
                unset($winner->id);
                unset($winner->password);

                $game['winner'] = $winner;
            }
            unset($game->id_winner); */

            return response($game, 200);
        } catch (\Exception $e) {
            return response("error: " . $e->getMessage(), 500);
        }
    }

    // when player marks X or O
    public function play(Request $request) {
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

            if (!property_exists($bodyContent, 'id_player') ||
                !property_exists($bodyContent, 'position')) {
                return response("invalid request body", 400);
            }

            $game = Game::find($request->id);

            if (!$game) {
                return response("game not found", 404);
            }

            if ($game->status == 'DONE') {
                return response("game is done", 422);
            }

            if ($game->status != 'STARTED') {
                return response("game is not started", 422);
            }

            if ($bodyContent->id_player == $game->id_owner) { // when is the owner
                if ($game->turn != 'OWNER') {
                    return response("invalid turn", 422);
                }
            } else if ($bodyContent->id_player == $game->id_guest) { // when is the guest
                if ($game->turn != 'GUEST') {
                    return response("invalid turn", 422);
                }
            } else {
                return response("invalid player id", 400);
            }

            $value = ($game->turn == 'OWNER' ? 'X' : 'O');

            if ($bodyContent->position == 1) {
                if ($game->first_position) {
                    return response("position already marked", 409);
                }
                $game->first_position = $value;
            } else if ($bodyContent->position == 2) {
                if ($game->second_position) {
                    return response("position already marked", 409);
                }
                $game->second_position = $value;
            } else if ($bodyContent->position == 3) {
                if ($game->third_position) {
                    return response("position already marked", 409);
                }
                $game->third_position = $value;
            } else if ($bodyContent->position == 4) {
                if ($game->fourth_position) {
                    return response("position already marked", 409);
                }
                $game->fourth_position = $value;
            } else if ($bodyContent->position == 5) {
                if ($game->fifth_position) {
                    return response("position already marked", 409);
                }
                $game->fifth_position = $value;
            } else if ($bodyContent->position == 6) {
                if ($game->sixth_position) {
                    return response("position already marked", 409);
                }
                $game->sixth_position = $value;
            } else if ($bodyContent->position == 7) {
                if ($game->seventh_position) {
                    return response("position already marked", 409);
                }
                $game->seventh_position = $value;
            } else if ($bodyContent->position == 8) {
                if ($game->eighth_position) {
                    return response("position already marked", 409);
                }
                $game->eighth_position = $value;
            } else if ($bodyContent->position == 9) {
                if ($game->nineth_position) {
                    return response("position already marked", 409);
                }
                $game->nineth_position = $value;
            } else {
                return response("invalid position", 400);
            }

            if (
                ($game->first_position == $value && $game->second_position == $value && $game->third_position == $value) || // first row
                ($game->fourth_position == $value && $game->fifth_position == $value && $game->sixth_position == $value) || // second row
                ($game->seventh_position == $value && $game->eighth_position == $value && $game->nineth_position == $value) || // third row
                ($game->first_position == $value && $game->fourth_position == $value && $game->seventh_position == $value) || // first column
                ($game->second_position == $value && $game->fifth_position == $value && $game->eighth_position == $value) || // second column
                ($game->third_position == $value && $game->sixth_position == $value && $game->nineth_position == $value) || // third column
                ($game->first_position == $value && $game->fifth_position == $value && $game->nineth_position == $value) || // first diagonal
                ($game->third_position == $value && $game->fifth_position == $value && $game->seventh_position == $value) // second diagonal
            ) {
                $game->id_winner = $bodyContent->id_player;
                $game->status = 'DONE';
            }

            // change turn when game is not DONE
            if ($game->status != 'DONE') {
                $game->turn = ($game->turn == 'OWNER' ? 'GUEST' : 'OWNER');
            }

            $game->save();

            // set and hide values

            if ($game->id_owner) {
                $owner = Account::find($game->id_owner);

                // update player
                if ($game->status == 'DONE') {
                    if ($owner->id == $game->id_winner) {
                        $owner->wins = ($owner->wins + 1);
                    } else {
                        $owner->losses = ($owner->losses + 1);
                    }
                    $owner->save();
                }

                unset($owner->id);
                unset($owner->password);

                $game['owner'] = $owner;
            }
            unset($game->id_owner);

            if ($game->id_guest) {
                $guest = Account::find($game->id_guest);

                // update player
                if ($game->status == 'DONE') {
                    if ($guest->id == $game->id_winner) {
                        $guest->wins = ($guest->wins + 1);
                    } else {
                        $guest->losses = ($guest->losses + 1);
                    }
                    $guest->save();
                }

                unset($guest->id);
                unset($guest->password);

                $game['guest'] = $guest;
            }
            unset($game->id_guest);

            if ($game->id_winner) {
                $winner = Account::find($game->id_winner);
                unset($winner->id);
                unset($winner->password);

                $game['winner'] = $winner;
            }
            unset($game->id_winner);

            return response($game, 200);
        } catch (\Exception $e) {
            return response("error: " . $e->getMessage(), 500);
        }
    }
}