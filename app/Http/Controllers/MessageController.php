<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteMessageRequest;
use App\Http\Requests\EditMessageRequest;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
//    public function index(){
//
//    }

    /**
     * @param DeleteMessageRequest $request
     * @param Message $message
     * @return void
     */
    public function destroy(DeleteMessageRequest $request ,Message $message){
//        abort_if($request->user()->id !== $message->sender_id , 401,"user is not allowed to delete this message");
        $message->delete();
    }
    public function edit(EditMessageRequest $request,Message $message){
//        abort_if($request->user()->id !== $message->sender_id , 401,"user is not allowed to edit this message");
        $validated = $request->validated();
        $message->update($validated);
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
        abort_if(request()->user() !== $user_id, 403);
        $message->update(['read_at' => now()]);
    }
}
