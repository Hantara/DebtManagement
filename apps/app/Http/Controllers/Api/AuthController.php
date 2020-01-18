<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;

class AuthController extends Controller
{
    //
	public $successStatus = 200;
  
	 public function register(Request $request) {    
	 $validator = Validator::make($request->all(), 
				  [ 
				  'name' => 'required',
				  'email' => 'required|email',
				  'password' => 'required',  
				  'c_password' => 'required|same:password', 
				 ]);   
	 if ($validator->fails()) {          
		   return response()->json(['error'=>$validator->errors()], 401);                        }    
	 $input = $request->all();  
	 $input['password'] = bcrypt($input['password']);
	 $user = User::create($input); 
	 $success['token'] =  $user->createToken('AppName')->accessToken;
	 return response()->json(['success'=>$success], $this->successStatus); 
	}
	  
	   
	public function login(){ 
	if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
	   $user = Auth::user(); 
	   $success['token'] =  $user->createToken('AppName')-> accessToken; 
		return response()->json(['success' => $success], $this-> successStatus); 
	  } else{ 
	   return response()->json(['error'=>'Unauthorised'], 401); 
	   } 
	}
	  
	public function getUser() {
	 $user = Auth::user();
	 return response()->json(['success' => $user], $this->successStatus); 
	}
	
	public function forgot_password(Request $request){
		$validator = Validator::make($request->all(), 
				  [ 
				  'email' => 'required|email',
				 ]);   
		if ($validator->fails()) {          
		   return response()->json(['error'=>$validator->errors()], 401);                        
		} else{
			try{
				 $response = Password::sendResetLink($request->only('email'), function (Message $message) {
                $message->subject($this->getEmailSubject());
            });
				switch ($response) {
                case Password::RESET_LINK_SENT:
                    return \Response::json(array("status" => 200, "message" => trans($response), "data" => array()));
                case Password::INVALID_USER:
                    return \Response::json(array("status" => 400, "message" => trans($response), "data" => array()));
				}
			} catch (Exception $ex) {
				$arr = array("status" => 400, "message" => $ex->getMessage(), "data" => []);
			}
			return response()->json(['success'=>$success], $this->successStatus);
			}   
		 
	}
}
