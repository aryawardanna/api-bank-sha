<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;
use Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'pin' => 'required|digits:6',
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        // using db transaction when creating data exist in db
        DB::beginTransaction();

        try {
            $profilePicture = null;
            $ktp = null;

            if($request->profile_picture){
                $profilePicture = $this->uploadBase64Image($request->profile_picture);
            }
            if($request->ktp){
                $ktp = $this->uploadBase64Image($request->ktp);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'username' => $request->username,
                'pin' => $request->pin,
                'profile_picture' => $profilePicture,
                'ktp' => $ktp,
                'verified' => ($ktp) ? true : false,
            ]);

            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'pin' => $request->pin,
                'card_number' => $this->generateCardNumber(16),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }

    private function generateCardNumber($length)
    {
        $result = '';
        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }

        $wallet = Wallet::where('card_number', $result)->exists();

        if($wallet){
            return $this->generateCardNumber($length);
        }
        return $result;
    }

    private function uploadBase64Image($base64)
    {
        $decoder = new Base64ImageDecoder($base64, $allowedFormats = ['jpeg', 'png', 'jpg']);

        $decodedImage = $decoder->getDecodedContent();
        $format = $decoder->getFormat();
        $image = Str::random(10) . '.' . $format;
        Storage::disk('public')->put($image, $decodedImage);

        return $image;
    }
}
