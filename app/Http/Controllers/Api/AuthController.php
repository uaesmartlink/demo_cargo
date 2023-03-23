<?php /** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api;

use App\Models\BusinessSetting;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\UserClient;
use App\Events\AddClient;
use App\Notifications\EmailVerificationNotification;
use App\Client;
use App\Captain;
use DB;
use Validator;
use App\Http\Helpers\UserRegistrationHelper;
use App\Transaction;
use App\Http\Helpers\ApiHelper;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        try{	
			DB::beginTransaction();

            
            $validator = Validator::make($request->all(), [ 
                'name'               => 'required|string',
                'email'              => 'required|string|email|unique:users',
                'password'           => 'required|string|min:6',
                'responsible_mobile' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

			$model = new Client();

			$model->name = $request->name;
			$model->email = $request->email;
			$model->responsible_mobile = $request->responsible_mobile;
			$model->code = -1;
	      
			if (!$model->save()){
                return response()->json(['message' => new \Exception()]);
			}
            
            $model->created_by_type = 'client';
            $model->created_by      = -1;
            $model->pickup_cost     = \App\ShipmentSetting::getVal('def_pickup_cost');
            $model->supply_cost     = \App\ShipmentSetting::getVal('def_supply_cost');
			$model->code            = $model->id;
			if (!$model->save()){
				return response()->json(['message' => new \Exception()] );
			}
			$userRegistrationHelper = new UserRegistrationHelper();
			$userRegistrationHelper->setEmail($model->email); 
			$userRegistrationHelper->setName($model->name);
			$userRegistrationHelper->setApiToken();
            $userRegistrationHelper->setPassword($request->password);
			$userRegistrationHelper->setRoleID(UserRegistrationHelper::MAINCLIENT);
			$response = $userRegistrationHelper->save();
			if(!$response['success']){
                return response()->json(['message' => new \Exception($response['error_msg'])]);
			}
			$userClient = new UserClient();
			$userClient->user_id = $response['user_id'];
			$userClient->client_id = $model->id;
			if (!$userClient->save()){
				return response()->json( new \Exception());
			}

            event(new AddClient($model));
			DB::commit();
            
            flash(translate("Your account has been created successfully"))->success();

            Auth::loginUsingId($response['user_id']);

            $user = User::where('id', $response['user_id'])->first();

            if($request->device_token && $request->device_token != null) {
                $user->device_token = $request->device_token;
                $user->save();
            }
            
            $userClient = Client::where('id', $user->userClient->client_id )->first();
            return response()->json([
                'api_token' => $user->api_token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->userClient->client_id,
                    'type' => $user->user_type,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'avatar_original' => $user->avatar_original,
                    'address' => $user->address,
                    'country'  => $user->country,
                    'city' => $user->city,
                    'postal_code' => $user->postal_code,
                    'phone' => $userClient->responsible_mobile,
                    'balance' =>$user->balance,
                ]
            ]);
		}catch(\Exception $e){
			DB::rollback();
			print_r($e->getMessage());
			exit;
			
			flash(translate("Error"))->error();
            return back();
		}
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials))
            return response()->json(['message' => 'Unauthorized', 'user' => null], 401);
        $user = $request->user();
        
        if($request->device_token && $request->device_token != null) {
            $user->device_token = $request->device_token;
            $user->save();
        }
        
        $tokenResult = $user->createToken('Personal Access Token');
        return $this->loginSuccess($tokenResult, $user);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['Successfully logged out']);
    }

    public function socialLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email'
        ]);
        if (User::where('email', $request->email)->first() != null) {
            $user = User::where('email', $request->email)->first();
        } else {
            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'provider_id' => $request->provider,
                'email_verified_at' => Carbon::now()
            ]);
            $user->save();
            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->save();
        }
        $tokenResult = $user->createToken('Personal Access Token');
        return $this->loginSuccess($tokenResult, $user);
    }

    protected function loginSuccess($tokenResult, $user)
    {
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(100);
        $token->save();
      	if(Auth::user()->user_type == 'customer'){
            $userlogin = Client::where('id', $user->userClient->client_id )->first();
            $transactions = Transaction::where('client_id', $user->userClient->client_id)->orderBy('created_at','desc')->sum('value');
        }elseif(Auth::user()->user_type == 'captain'){
          	$userlogin = Captain::where('id', $user->userCaptain->captain_id )->first();
            $transactions = Transaction::where('captain_id', $user->userCaptain->captain_id)->orderBy('created_at','desc')->sum('value');
        }
        $transactions = abs($transactions); // Converting the transactions from negative to positive
        return response()->json([
            'api_token' => $user->api_token,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'user' => [
                'id' => $userlogin->id,
                'type' => $user->user_type,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'avatar_original' => $user->avatar_original,
                'address' => $user->address,
                'country'  => $user->country,
                'city' => $user->city,
                'postal_code' => $user->postal_code,
                'phone' => $userlogin->responsible_mobile,
                'balance' => $transactions,
            ]
        ]);
    }

    public function getWallet(Request $request)
    {
      	
        $apihelper = new ApiHelper();
        $user = $apihelper->checkUser($request);

        if($user){
            if($request->type == 'client')
            {
                $transactions = Transaction::where('client_id', $user->userClient->client_id)->orderBy('created_at','desc')->sum('value');
            }elseif ($request->type == 'captain') {
                $transactions = Transaction::where('captain_id', $user->userCaptain->captain_id)->orderBy('created_at','desc')->sum('value');
            }else{
              	return response()->json(['message' => 'Enter a valid type'] );
            }
            $transactions = abs($transactions); // Converting the transactions from negative to positive
            return response()->json($transactions);
        }else{
            return response()->json('Not Authorized');
        }
    }
}
