<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteMessageRequest;
use App\Http\Requests\EditMessageRequest;
use App\Models\Message;

class MessageController extends Controller
{
//    public function index(){
//
//    }

    /**
     * @param DeleteMessageRequest $request
     * @param Message $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteMessageRequest $request ,Message $message){
//        abort_if($request->user()->id !== $message->sender_id , 401,"user is not allowed to delete this message");
        $message->delete();
        return response()->json([
            "data"=>null,
            "code"=>200,
            "message"=>"message deleted successfully"
        ]);
    }
    public function edit(EditMessageRequest $request,Message $message){
//        abort_if($request->user()->id !== $message->sender_id , 401,"user is not allowed to edit this message");
        $validated = $request->validated();
        $message->update([
            "body"=>$validated['message']
        ]);
        return response()->json([
            "data"=>$message,
            "code"=>200,
            "message"=>"Message updated successfully"
            ]);
    }
    public function read(Message $message)
    {
        if($message->chat->receiver_id === $message->sender_id)
        {
            $user_id = $message->chat->sender_id;
        }
        else
        {
            $user_id = $message->chat->receiver_id;
        }
        abort_if(request()->user()->id !== $user_id, 403);
        $message->update(['read_at' => now()]);
        return response()->json([
            "data"=>$message,
            "code"=>200,
            "message"=>"Message read successfully"
        ]);
    }
}
