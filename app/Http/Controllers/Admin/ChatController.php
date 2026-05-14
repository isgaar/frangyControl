<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Display the chat interface.
     */
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('admin.chat.index', compact('users'));
    }

    /**
     * Fetch messages with a specific user.
     */
    public function fetchMessages($userId)
    {
        $myId = Auth::id();

        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $myId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Get messages
        $messages = Message::where(function ($query) use ($myId, $userId) {
            $query->where('sender_id', $myId)->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($myId, $userId) {
            $query->where('sender_id', $userId)->where('receiver_id', $myId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    /**
     * Send a new message.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string'
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
            'is_read' => false,
        ]);

        return response()->json($message);
    }

    /**
     * Get unread messages count for notifications.
     */
    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }
}
