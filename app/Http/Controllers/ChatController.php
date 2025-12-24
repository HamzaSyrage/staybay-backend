<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Models\Message;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\Chat;

class ChatController extends Controller
{
    /**
     * @param SendMessageRequest $request
     * @return \Illuminate\Http\JsonResponse
     * send message or open a new chat
     */
    public function send(SendMessageRequest $request)
    {
        $validated = $request->validated();
        $sender = $request->user();
        $message = $validated['message'];
        $receiver_id = $validated['receiver_id'];
        $chat = Chat::where(function ($q) use ($sender, $receiver_id) {
            $q->where('sender_id', $sender->id)
                ->where('receiver_id', $receiver_id);
        })->orWhere(function ($q) use ($sender, $receiver_id) {
            $q->where('sender_id', $receiver_id)
                ->where('receiver_id', $sender->id);
        })->first();
        if (!isset($chat)) {
            $chat = Chat::create([
                'sender_id'   => $sender->id,
                'receiver_id' => $receiver_id,
            ]);
        }
        $chat->messages()->create([
            'body' => $message,
            'sender_id' => $sender->id,
        ]);
        $receiver = User::find($receiver_id);
        NotificationService::sendNotification($sender, "user {$receiver->first_name} {$receiver->last_name} sent a message}",[
            "sender_id"=>$sender->id,
        ]);
        return response()->json([
            'data' => $message,
            'message'=>'Message sent',
            'code'=>200
        ]);
    }

    /**
     * @return Chat[]|\LaravelIdea\Helper\App\Models\_IH_Chat_C
     * incoming and outcoming chat
     */
    public function index(){
        return Chat::where('sender_id',auth()->user()->id)->orWhere('receiver_id',auth()->user()->id)->get();
    }

    /**
     * @param Chat $chat
     * @return mixed
     * show a chat
     */
    public function show(Chat $chat){
        $user_id = auth()->user()->id;
        abort_if( $user_id !== $chat->receiver_id && $user_id !==$chat->sender_id, 403 ,"Unauthorized action.");
        return Chat::with('messages')->find($chat->id);
    }

    /**
     * delete all messages with the chat
     * @param Chat $chat
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Chat $chat){
        $user_id = auth()->user()->id;
        abort_if( $user_id !== $chat->receiver_id && $user_id !==$chat->sender_id, 403 ,"Unauthorized action.");
        $chat->delete();
        return response()->json([
            'data' => $chat,
            'message'=>'Message deleted',
            'code'=>200
        ]);
    }

}
