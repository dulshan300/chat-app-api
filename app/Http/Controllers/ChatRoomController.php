<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatRoomResource;
use App\Models\ChatRoom;
use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = auth()->user()->chat_groups()->orderBy('updated_at', 'desc')->get();
        return ChatRoomResource::collection($rooms);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validation

        $request->validate([
            'name' => 'required|string|unique:chat_rooms,name,user_id',
            'access_key' => 'required_if:is_private,true',
        ], [
            'access_key.required_if' => 'Please provide access key',
            'name.required' => 'Please provide room name',
        ]);


        $user = auth()->user();

        $chatRoom = $user->chat_rooms()->save(new ChatRoom([
            'name' => $request->name,
            'is_private' => $request->is_private,
            'access_key' => $request->access_key,
        ]));

        $chatRoom->members()->attach($user, ['role' => 'admin']);

        return new ChatRoomResource($chatRoom);
    }

    /**
     * Display the specified resource.
     */
    public function show($chatRoom)
    {
        $user = auth()->user();

        // check room exist
        $room = ChatRoom::find($chatRoom);

        if (!$room) {
            return response()->json([
                'message' => 'Room not found'
            ], 404);
        }

        $chat_room = $user->chat_groups()->where('chat_room_user.chat_room_id', $chatRoom)->first();

        if (!$chat_room) {

            // check if room is private

            if ($room->is_private) {

                return response()->json([
                    'message' => 'Room is private. Please provide access key'
                ], 403);
            } else {
                $user->chat_groups()->attach($room, ['role' => 'member']);
                $chat_room = $user->chat_groups()->where('chat_room_user.chat_room_id', $chatRoom)->first();
            }
        }


        return new ChatRoomResource($chat_room);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChatRoom $chatRoom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChatRoom $chatRoom)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChatRoom $chatRoom)
    {
        //
    }


    public function join_room(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:chat_rooms,id',
        ]);

        $room = ChatRoom::find($request->room_id);

        if ($room->is_private) {
            $request->validate([
                'access_key' => 'required',
            ]);

            if ($request->access_key != $room->access_key) {
                return response()->json([
                    'errors' => ['access_key' => ['Invalid access key']]
                ], 422);
            }
        }

        $user = auth()->user();

        $user->chat_groups()->attach($room, ['role' => 'member']);


        return response()->json(['message' => 'Joined successfully'], 200);
    }
}
