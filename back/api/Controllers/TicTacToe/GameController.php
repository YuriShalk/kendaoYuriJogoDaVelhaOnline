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
                return $this->makeResponseError("unauthorized", 401);
            } */

            if ($request->id) {
                $game = Game::find($request->id);
            } /* else if ($request->id_player) { // player can be in more than one game at the same time
                $game = Game::where('status', '!=', 'DONE')->where('id_owner', '=', $request->id_player)->first();

                if (!$game) {
                    $game = Game::where('status', '!=', 'DONE')->where('id_guest', '=', $request->id_player)->first();
                }
            } */ else {
                return $this->makeResponseError("invalid parameters", 400);
            }

            if (!$game) {
                return $this->makeResponseError("game not found", 404);
            }

            // set and hide values

            $players = [];

            if ($game->id_owner) {
                $owner = Account::find($game->id_owner);
                $owner['myself'] = ($request->id_player ? ($owner->id == $request->id_player) : null);
                $owner['winner'] = ($game->id_winner ? ($owner->id == $game->id_winner) : null);
                $owner['turn'] = ($game->status != 'STARTED' ? null : ($game->turn == 'OWNER'));
                unset($owner->id);
                unset($owner->password);

                $players[] = $owner;
            }
            unset($game->id_owner);

            if ($game->id_guest) {
                $guest = Account::find($game->id_guest);
                $guest['myself'] = ($request->id_player ? ($guest->id == $request->id_player) : null);
                $guest['winner'] = ($game->id_winner ? ($guest->id == $game->id_winner) : null);
                $guest['turn'] = ($game->status != 'STARTED' ? null : ($game->turn == 'GUEST'));
                unset($guest->id);
                unset($guest->password);

                $players[] = $guest;
            }
            unset($game->id_guest);

            unset($game->id_winner);

            unset($game->turn);

            $game['players'] = $players;

            return response($game, 200);
        } catch (\Exception $e) {
            return $this->makeResponseError("error: " . $e->getMessage(), 500);
        }
    }

    public function create(Request $request) {
        try {
            /* if ($request->header('Authorization') != $this->token) {
                return $this->makeResponseError("unauthorized", 401);
            } */

            $bodyContent = json_decode($request->getContent());

            if (!$bodyContent) {
                return $this->makeResponseError("empty request body", 400);
            }

            if (!property_exists($bodyContent, 'id_player')) {
                return $this->makeResponseError("invalid request body", 400);
            }

            $owner = Account::find($bodyContent->id_player);

            if (!$owner) {
                return $this->makeResponseError("player does not exist", 422);
            }

            $random_value = rand(0, 1);

            $game = new Game();
            $game->id_owner = $bodyContent->id_player;
            $game->turn = ($random_value == 1 ? 'OWNER' : 'GUEST');
            $game->status = 'CREATED';
            $game->save();

            // set and hide values

            $players = [];

            $owner['myself'] = true;
            $owner['winner'] = null;
            $owner['turn'] = null;

            unset($owner->id);
            unset($owner->password);

            $players[] = $owner;

            unset($game->id_owner);
            unset($game->id_guest);
            unset($game->id_winner);

            unset($game->turn);

            $game['players'] = $players;

            return response($game, 201);
        } catch (\Exception $e) {
            return $this->makeResponseError("error: " . $e->getMessage(), 500);
        }
    }

    // when player enter in the room
    public function join(Request $request) {
        try {
            /* if ($request->header('Authorization') != $this->token) {
                return $this->makeResponseError("unauthorized", 401);
            } */

            if (!$request->id) {
                return $this->makeResponseError("invalid parameters", 400);
            }

            $bodyContent = json_decode($request->getContent());

            if (!$bodyContent) {
                return $this->makeResponseError("empty request body", 400);
            }

            if (!property_exists($bodyContent, 'id_player')) {
                return $this->makeResponseError("invalid request body", 400);
            }

            $game = Game::find($request->id);

            if (!$game) {
                return $this->makeResponseError("game not found", 404);
            }

            if ($game->status == 'DONE') {
                return $this->makeResponseError("game is done", 422);
            }

            $guest = null;

            if ($bodyContent->id_player != $game->id_owner) {
                if ($game->id_guest == null) {
                    $guest = Account::find($bodyContent->id_player);

                    if (!$guest) {
                        return $this->makeResponseError("player does not exist", 422);
                    }

                    $game->id_guest = $guest->id;
                    $game->status = 'STARTED';

                    $game->save();
                } else if ($game->id_guest != $bodyContent->id_player) {
                    return $this->makeResponseError("game is full", 422);
                }
            }

            // set and hide values

            $players = [];

            if ($game->id_owner) {
                $owner = Account::find($game->id_owner);
                $owner['myself'] = ($owner->id == $bodyContent->id_player);
                $owner['winner'] = null;
                $owner['turn'] = ($game->turn == 'OWNER');
                unset($owner->id);
                unset($owner->password);

                $players[] = $owner;
            }
            unset($game->id_owner);

            if ($game->id_guest) {
                if (!$guest) {
                    $guest = Account::find($game->id_guest);
                }
                $guest['myself'] = ($guest->id == $bodyContent->id_player);
                $guest['winner'] = null;
                $guest['turn'] = ($game->turn == 'GUEST');
                unset($guest->id);
                unset($guest->password);

                $players[] = $guest;
            }
            unset($game->id_guest);

            unset($game->id_winner);

            unset($game->turn);

            $game['players'] = $players;

            return response($game, 200);
        } catch (\Exception $e) {
            return $this->makeResponseError("error: " . $e->getMessage(), 500);
        }
    }

    // when player marks X or O
    public function play(Request $request) {
        try {
            /* if ($request->header('Authorization') != $this->token) {
                return $this->makeResponseError("unauthorized", 401);
            } */

            if (!$request->id) {
                return $this->makeResponseError("invalid parameters", 400);
            }

            $bodyContent = json_decode($request->getContent());

            if (!$bodyContent) {
                return $this->makeResponseError("empty request body", 400);
            }

            if (!property_exists($bodyContent, 'id_player') ||
                !property_exists($bodyContent, 'position')) {
                return $this->makeResponseError("invalid request body", 400);
            }

            $game = Game::find($request->id);

            if (!$game) {
                return $this->makeResponseError("game not found", 404);
            }

            if ($game->status == 'DONE') {
                return $this->makeResponseError("game is done", 422);
            }

            if ($game->status != 'STARTED') {
                return $this->makeResponseError("game is not started", 422);
            }

            if ($bodyContent->id_player == $game->id_owner) { // when is the owner
                if ($game->turn != 'OWNER') {
                    return $this->makeResponseError("invalid turn", 422);
                }
            } else if ($bodyContent->id_player == $game->id_guest) { // when is the guest
                if ($game->turn != 'GUEST') {
                    return $this->makeResponseError("invalid turn", 422);
                }
            } else {
                return $this->makeResponseError("invalid player id", 400);
            }

            $value = ($game->turn == 'OWNER' ? 'X' : 'O');

            if ($bodyContent->position == 1) {
                if ($game->first_position) {
                    return $this->makeResponseError("position already marked", 409);
                }
                $game->first_position = $value;
            } else if ($bodyContent->position == 2) {
                if ($game->second_position) {
                    return $this->makeResponseError("position already marked", 409);
                }
                $game->second_position = $value;
            } else if ($bodyContent->position == 3) {
                if ($game->third_position) {
                    return $this->makeResponseError("position already marked", 409);
                }
                $game->third_position = $value;
            } else if ($bodyContent->position == 4) {
                if ($game->fourth_position) {
                    return $this->makeResponseError("position already marked", 409);
                }
                $game->fourth_position = $value;
            } else if ($bodyContent->position == 5) {
                if ($game->fifth_position) {
                    return $this->makeResponseError("position already marked", 409);
                }
                $game->fifth_position = $value;
            } else if ($bodyContent->position == 6) {
                if ($game->sixth_position) {
                    return $this->makeResponseError("position already marked", 409);
                }
                $game->sixth_position = $value;
            } else if ($bodyContent->position == 7) {
                if ($game->seventh_position) {
                    return $this->makeResponseError("position already marked", 409);
                }
                $game->seventh_position = $value;
            } else if ($bodyContent->position == 8) {
                if ($game->eighth_position) {
                    return $this->makeResponseError("position already marked", 409);
                }
                $game->eighth_position = $value;
            } else if ($bodyContent->position == 9) {
                if ($game->nineth_position) {
                    return $this->makeResponseError("position already marked", 409);
                }
                $game->nineth_position = $value;
            } else {
                return $this->makeResponseError("invalid position", 400);
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

            // draw game
            if (
                $game->status != 'DONE' && (
                    $game->first_position && $game->second_position && $game->third_position &&
                    $game->fourth_position && $game->fifth_position && $game->sixth_position &&
                    $game->seventh_position && $game->eighth_position && $game->nineth_position
                )
            ) {
                $game->status = 'DONE';
            }

            // change turn when game is not DONE
            if ($game->status != 'DONE') {
                $game->turn = ($game->turn == 'OWNER' ? 'GUEST' : 'OWNER');
            }

            $game->save();

            // set and hide values

            $players = [];

            $owner = Account::find($game->id_owner);

            // update owner player
            if ($game->id_winner != null) {
                if ($owner->id == $game->id_winner) {
                    $owner->wins = ($owner->wins + 1);
                } else {
                    $owner->losses = ($owner->losses + 1);
                }
                $owner->save();
            }

            $owner['myself'] = ($owner->id == $bodyContent->id_player);
            $owner['winner'] = ($game->id_winner ? ($owner->id == $game->id_winner) : null);
            $owner['turn'] = ($game->status != 'STARTED' ? null : ($game->turn == 'OWNER'));
            unset($owner->id);
            unset($owner->password);

            $players[] = $owner;

            unset($game->id_owner);

            $guest = Account::find($game->id_guest);

            // update guest player
            if ($game->id_winner != null) {
                if ($guest->id == $game->id_winner) {
                    $guest->wins = ($guest->wins + 1);
                } else {
                    $guest->losses = ($guest->losses + 1);
                }
                $guest->save();
            }

            $guest['myself'] = ($guest->id == $bodyContent->id_player);
            $guest['winner'] = ($game->id_winner ? ($guest->id == $game->id_winner) : null);
            $guest['turn'] = ($game->status != 'STARTED' ? null : ($game->turn == 'GUEST'));
            unset($guest->id);
            unset($guest->password);

            $players[] = $guest;

            unset($game->id_guest);

            unset($game->id_winner);

            unset($game->turn);

            $game['players'] = $players;

            return response($game, 200);
        } catch (\Exception $e) {
            return $this->makeResponseError("error: " . $e->getMessage(), 500);
        }
    }

    private function makeResponseError($message, $status) {
        $object = (object) [
            'status' => $status,
            'error' => $message
        ];

        return response(
            json_encode($object, JSON_UNESCAPED_UNICODE), $status
        )->header('Content-Type', 'application/json');;
    }
}