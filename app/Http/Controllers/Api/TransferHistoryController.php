<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransferHistory;
use Illuminate\Http\Request;

class TransferHistoryController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->query('limit') ? $request->query('limit') : 10;

        $sender = auth()->user();

        $transferHistories = TransferHistory::with('receiverUser:id,name,is_verified,profile_picture')
                                                ->select('receiver_id')
                                                ->where('sender_id', $sender->id)
                                                ->groupBy('receiver_id')
                                                ->paginate($limit);

        $transferHistories->getCollection()->transform(function ($item) {
            $reciverUser = $item->receiverUser;
            $reciverUser->profile_picture = $reciverUser->profile_picture ? url('storage/'.$reciverUser->profile_picture) : "";
            return $reciverUser;
        });
        return response()->json($transferHistories);
    }
}
