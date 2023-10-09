<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function show()
    {
        $user = getUser(auth()->user()->id);
        return response()->json($user);
    }

    public function getUserByUsername(Request $request, $username)
    {
        $users = User::select('id', 'name', 'username', 'is_verified', 'profile_picture')
                    ->where('username', 'LIKE', '%' . $username . '%')
                    ->where('id', '<>', auth()->user()->id)
                    ->get();

        $users->map(function ($user) {
            $user->profile_picture = $user->profile_picture ? url('storage/' . $user->profile_picture) : "";

            return $user;
        });

        return response()->json($users);
    }

    public function update(Request $request)
    {
        try {
            $user = User::find(auth()->user()->id);

            $data = $request->only('name','username','email', 'password', 'ktp');
            if($request->username != $user->username){
                $isExistUsername = User::where('username', $request->username)->exists();
                if($isExistUsername){
                    return response()->json(['message' => 'Username already exist'], 409);
                }
            }

            if($request->email != $user->email){
                $isExistEmail = User::where('email', $request->email)->exists();
                if($isExistEmail){
                    return response()->json(['message' => 'Email already exist'], 409);
                }
            }

            if($request->password){
                $data['password'] = bcrypt($request->password);
            }

            if($request->profile_picture){
                $data['profile_picture'] = uploadBase64Image($request->profile_picture);
                if($user->profile_picture){
                    Storage::disk('public')->delete($user->profile_picture);
                }
            }
            if($request->ktp){
                $data['ktp'] = uploadBase64Image($request->ktp);
                $data['is_verified'] = true;
                if($user->ktp){
                    Storage::disk('public')->delete($user->ktp);
                }
            }

            $user->update($data);
            return response()->json(['message' => 'success'], 200);

        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function isEmailExist(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email',
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        $isExist = User::where('email', $request->email)->exists();
        return response()->json(['is_email_exist' => $isExist], 200);
    }
}
