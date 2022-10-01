<?php 

namespace App\Http\Controllers\TicTacToe;

use App\Http\Models\TicTacToe\Account;
use Illuminate\Http\Request as Request;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    // private $token = '646200d7-6511-479e-b7e3-1d0efbd23ff9';

    public function get(Request $request) {
        try {
            /* if ($request->header('Authorization') != $this->token) {
                return $this->makeResponseError("unauthorized", 401);
            } */

            if ($request->id) {
                $account = Account::find($request->id);
            } else if ($request->username) {
                $account = Account::where('username', '=', $request->username)->first();
            } else {
                return $this->makeResponseError("invalid parameters", 400);
            }

            if (!$account) {
                return $this->makeResponseError("account not found", 404);
            }

            unset($account->password);

            return response($account, 200);
        } catch (\Exception $e) {
            return $this->makeResponseError("error: " . $e->getMessage(), 500);
        }
    }

    public function post(Request $request) {
        try {
            /* if ($request->header('Authorization') != $this->token) {
                return $this->makeResponseError("unauthorized", 401);
            } */

            $bodyContent = json_decode($request->getContent());

            if (!$bodyContent) {
                return $this->makeResponseError("empty request body", 400);
            }

            if (!property_exists($bodyContent, 'username')) {
                return $this->makeResponseError("invalid request body", 400);
            }

            $existing_account = Account::where('username', '=', $bodyContent->username)->first();
            
            if ($existing_account) {
                return $this->makeResponseError("username already exists", 409);
            }

            $rows = \DB::select("SELECT UUID() as id");
            $new_id = $rows[0]->id;

            $account = new Account();
            $account->id = $new_id;
            $account->username = $bodyContent->username;
            if (property_exists($bodyContent, 'password')) {
                $account->password = $bodyContent->password;
            }
            $account->save();

            unset($account->password);

            return response($account, 201);
        } catch (\Exception $e) {
            return $this->makeResponseError("error: " . $e->getMessage(), 500);
        }
    }

    public function patch(Request $request) {
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
            
            $account = Account::find($request->id);

            if (!$account) {
                return $this->makeResponseError("account not found", 404);
            }

            if (property_exists($bodyContent, 'username')) {
                $existing_account = Account::where('username', '=', $bodyContent->username)->first();
            
                if ($existing_account && $existing_account->id != $request->id) {
                    return $this->makeResponseError("username already exists", 409);
                }

                $account->username = $bodyContent->username;
            }

            if (property_exists($bodyContent, 'password')) {
                $account->password = $bodyContent->password;
            }

            $account->save();

            unset($account->password);

            return response($account, 200);
        } catch (\Exception $e) {
            return $this->makeResponseError("error: " . $e->getMessage(), 500);
        }
    }

    public function delete(Request $request) {
        try {
            /* if ($request->header('Authorization') != $this->token) {
                return $this->makeResponseError("unauthorized", 401);
            } */

            if (!$request->id) {
                return $this->makeResponseError("invalid parameters", 400);
            }

            $account = Account::find($request->id);

            if (!$account) {
                return $this->makeResponseError("account not found", 404);
            }
            
            $account->delete();

            return response("", 204);
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