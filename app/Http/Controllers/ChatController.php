<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Apartment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * بدء محادثة جديدة
     */
    public function startConversation(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'message' => 'required|string|max:1000',
            'subject' => 'nullable|string|max:255'
        ]);

        $senderId = Auth::id();
        $apartment = Apartment::with('user')->findOrFail($request->apartment_id);
        $receiverId = $apartment->user_id;

        if ($senderId == $receiverId) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن بدء محادثة مع نفسك'
            ], 400);
        }

        DB::beginTransaction();
        
        try {
            $existingConversation = Conversation::findExisting(
                $apartment->id,
                $senderId,
                $receiverId
            );

            if ($existingConversation) {
                $message = Message::create([
                    'conversation_id' => $existingConversation->id,
                    'sender_id' => $senderId,
                    'message' => $request->message,
                    'is_read' => false
                ]);

                $existingConversation->update([
                    'last_message_at' => now(),
                    'is_receiver_read' => false
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'تم إرسال الرسالة بنجاح',
                    'conversation' => $existingConversation->load(['apartment', 'sender', 'receiver']),
                    'new_message' => $message,
                    'is_new_conversation' => false
                ]);
            }

            $conversation = Conversation::create([
                'apartment_id' => $apartment->id,
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'subject' => $request->subject ?? "استفسار عن شقة: {$apartment->title}",
                'is_sender_read' => true,
                'is_receiver_read' => false,
                'last_message_at' => now()
            ]);

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $senderId,
                'message' => $request->message,
                'is_read' => false
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم بدء المحادثة بنجاح',
                'conversation' => $conversation->load(['apartment', 'sender', 'receiver']),
                'new_message' => $message,
                'is_new_conversation' => true
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في بدء المحادثة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إرسال رسالة في محادثة موجودة
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120'
        ]);

        $userId = Auth::id();
        $conversation = Conversation::findOrFail($conversationId);

        if (!$conversation->isParticipant($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للمشاركة في هذه المحادثة'
            ], 403);
        }

        DB::beginTransaction();
        
        try {
            $attachmentPath = null;
            $attachmentType = null;
            
           

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $userId,
                'message' => $request->message,
                'attachment' => $attachmentPath,
                'attachment_type' => $attachmentType,
                'is_read' => false
            ]);

            $conversation->update([
                'last_message_at' => now()
            ]);

            $otherUserId = ($userId == $conversation->sender_id) 
                ? $conversation->receiver_id 
                : $conversation->sender_id;
            
            $role = $conversation->getUserRole($otherUserId);
            if ($role === 'sender') {
                $conversation->update(['is_sender_read' => false]);
            } else {
                $conversation->update(['is_receiver_read' => false]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إرسال الرسالة بنجاح',
                'data' => $message->load('sender')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إرسال الرسالة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   
    public function getUserConversations(Request $request)
    {
        $userId = Auth::id();
        
        $type = $request->get('type', 'all'); // all, sent, received
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);

        $query = Conversation::with([
                'apartment' => function($query) {
                    $query->select('id', 'title', 'address', 'price', 'images');
                },
                'sender' => function($query) {
                    $query->select('id', 'name', 'profile_image');
                },
                'receiver' => function($query) {
                    $query->select('id', 'name', 'profile_image');
                },
                'lastMessage'
            ]);

        if ($type === 'sent') {
            $query->where('sender_id', $userId);
        } elseif ($type === 'received') {
            $query->where('receiver_id', $userId);
        } else {
            $query->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
        }

        $conversations = $query->orderBy('last_message_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $unreadCount = Conversation::where(function($q) use ($userId) {
            $q->where('sender_id', $userId)->where('is_sender_read', false)
              ->orWhere('receiver_id', $userId)->where('is_receiver_read', false);
        })->count();

        return response()->json([
            'success' => true,
            'conversations' => $conversations,
            'unread_count' => $unreadCount,
            'stats' => [
                'sent' => Conversation::where('sender_id', $userId)->count(),
                'received' => Conversation::where('receiver_id', $userId)->count(),
                'total' => $conversations->total()
            ]
        ]);
    }

  
    public function getConversation($conversationId)
    {
        $userId = Auth::id();
        $conversation = Conversation::with(['apartment', 'sender', 'receiver'])
            ->findOrFail($conversationId);

        if (!$conversation->isParticipant($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية لعرض هذه المحادثة'
            ], 403);
        }

        $conversation->markAsRead($userId);

        $messages = Message::with('sender')
            ->where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        $otherUser = $conversation->getOtherParticipant($userId);
        $isSender = $conversation->isSender($userId);

        return response()->json([
            'success' => true,
            'conversation' => $conversation,
            'messages' => $messages,
            'other_user' => $otherUser,
            'is_sender' => $isSender,
            'unread_count' => $conversation->unreadMessagesCount($userId)
        ]);
    }

    
    public function findConversation($apartmentId)
    {
        $userId = Auth::id();
        $apartment = Apartment::with('user')->findOrFail($apartmentId);
        $receiverId = $apartment->user_id;

        $conversation = Conversation::with(['apartment', 'sender', 'receiver', 'lastMessage'])
            ->where('apartment_id', $apartmentId)
            ->where('sender_id', $userId)
            ->where('receiver_id', $receiverId)
            ->first();

        if ($conversation) {
            return response()->json([
                'success' => true,
                'conversation' => $conversation,
                'exists' => true,
                'can_message' => true
            ]);
        }

        return response()->json([
            'success' => true,
            'conversation' => null,
            'exists' => false,
            'apartment' => $apartment,
            'receiver' => $apartment->user,
            'can_message' => true
        ]);
    }

    
    public function deleteConversation($conversationId)
    {
        $userId = Auth::id();
        $conversation = Conversation::findOrFail($conversationId);

        if (!$conversation->isParticipant($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية لحذف هذه المحادثة'
            ], 403);
        }

        DB::beginTransaction();
        
        try {
            $messagesWithAttachments = $conversation->messages()->whereNotNull('attachment')->get();
            foreach ($messagesWithAttachments as $message) {
                if (Storage::disk('public')->exists($message->attachment)) {
                    Storage::disk('public')->delete($message->attachment);
                }
            }

            $conversation->messages()->delete();
            $conversation->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المحادثة بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في حذف المحادثة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function getUnreadCount()
    {
        $userId = Auth::id();

        $unreadCount = Conversation::where(function($q) use ($userId) {
            $q->where('sender_id', $userId)->where('is_sender_read', false)
              ->orWhere('receiver_id', $userId)->where('is_receiver_read', false);
        })->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }

  
    public function markConversationAsRead($conversationId)
    {
        $userId = Auth::id();
        $conversation = Conversation::findOrFail($conversationId);

        if (!$conversation->isParticipant($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية'
            ], 403);
        }

        $conversation->markAsRead($userId);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة القراءة'
        ]);
    }

    /**
     * جلب آخر رسائل كل محادثة
     */
    public function getLatestMessages()
    {
        $userId = Auth::id();

        $conversations = Conversation::with(['apartment', 'sender', 'receiver'])
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('last_message_at', 'desc')
            ->take(10)
            ->get();

        $latestMessages = [];
        foreach ($conversations as $conversation) {
            $lastMessage = $conversation->messages()->latest()->first();
            if ($lastMessage) {
                $latestMessages[] = [
                    'conversation' => $conversation,
                    'last_message' => $lastMessage,
                    'has_unread' => $conversation->hasUnreadMessages($userId)
                ];
            }
        }

        return response()->json([
            'success' => true,
            'messages' => $latestMessages
        ]);
    }

   
   
}