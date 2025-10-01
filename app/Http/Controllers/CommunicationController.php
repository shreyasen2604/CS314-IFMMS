<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Announcement;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewMessageNotification;
use App\Notifications\AnnouncementNotification;

class CommunicationController extends Controller
{
    /**
     * Display the communication dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get unread messages count
        $unreadMessages = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        // Get recent messages
        $recentMessages = Message::inbox($user->id)
            ->with(['sender', 'receiver'])
            ->latest()
            ->take(5)
            ->get();
        
        // Get active announcements for user's role
        $announcements = Announcement::active()
            ->forAudience($user->role)
            ->latest()
            ->take(5)
            ->get();
        
        // Get user's notification preferences
        $preferences = UserPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email_notifications' => true,
                'push_notifications' => true,
                'maintenance_alerts' => true,
                'incident_updates' => true,
                'announcement_notifications' => true,
                'message_notifications' => true,
                'timezone' => 'UTC',
                'preferred_language' => 'en'
            ]
        );
        
        // Get unread notifications
        $notifications = $user->unreadNotifications()->take(10)->get();
        
        return view('communication.index', compact(
            'unreadMessages',
            'recentMessages',
            'announcements',
            'preferences',
            'notifications'
        ));
    }

    /**
     * Display messages inbox
     */
    public function messages(Request $request)
    {
        $user = Auth::user();
        $type = $request->get('type', 'inbox');
        
        $query = Message::with(['sender', 'receiver']);
        
        if ($type === 'sent') {
            $query->sent($user->id);
        } else {
            $query->inbox($user->id);
        }
        
        if ($request->has('priority')) {
            $query->priority($request->get('priority'));
        }
        
        if ($request->has('unread')) {
            $query->unread();
        }
        
        $messages = $query->latest()->paginate(20);
        
        return view('communication.messages', compact('messages', 'type'));
    }

    /**
     * Show message compose form
     */
    public function compose()
    {
        $users = User::where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();
        
        return view('communication.compose', compact('users'));
    }

    /**
     * Send a new message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'nullable|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'type' => 'required|in:direct,broadcast,system',
            'priority' => 'required|in:low,normal,high,urgent',
            'attachments.*' => 'nullable|file|max:10240' // 10MB max
        ]);
        
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('message-attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ];
            }
        }
        
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'body' => $request->body,
            'type' => $request->type,
            'priority' => $request->priority,
            'attachments' => $attachments,
            'status' => 'sent'
        ]);
        
        // Send notification to receiver
        if ($message->receiver) {
            $message->receiver->notify(new NewMessageNotification($message));
        }
        
        return redirect()->route('communication.messages')
            ->with('success', 'Message sent successfully');
    }

    /**
     * View a specific message
     */
    public function viewMessage($id)
    {
        $message = Message::with(['sender', 'receiver', 'replies.sender'])
            ->findOrFail($id);
        
        // Check if user has permission to view this message
        $user = Auth::user();
        if ($message->receiver_id !== $user->id && 
            $message->sender_id !== $user->id &&
            $message->type !== 'broadcast') {
            abort(403, 'Unauthorized access to message');
        }
        
        // Mark as read if user is the receiver
        if ($message->receiver_id === $user->id) {
            $message->markAsRead();
        }
        
        return view('communication.view-message', compact('message'));
    }

    /**
     * Reply to a message
     */
    public function replyMessage(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string'
        ]);
        
        $originalMessage = Message::findOrFail($id);
        
        $reply = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $originalMessage->sender_id,
            'subject' => 'Re: ' . $originalMessage->subject,
            'body' => $request->body,
            'type' => 'direct',
            'priority' => $originalMessage->priority,
            'parent_id' => $originalMessage->id,
            'status' => 'sent'
        ]);
        
        // Send notification
        if ($reply->receiver) {
            $reply->receiver->notify(new NewMessageNotification($reply));
        }
        
        return redirect()->back()->with('success', 'Reply sent successfully');
    }

    /**
     * Display announcements
     */
    public function announcements()
    {
        $user = Auth::user();
        
        $announcements = Announcement::active()
            ->forAudience($user->role)
            ->with('creator')
            ->latest()
            ->paginate(10);
        
        return view('communication.announcements', compact('announcements'));
    }

    /**
     * Create announcement (Admin only)
     */
    public function createAnnouncement()
    {
        // Check if user is admin
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }
        
        return view('communication.create-announcement');
    }

    /**
     * Store announcement
     */
    public function storeAnnouncement(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role !== 'Admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,alert,success',
            'target_audience' => 'required|in:all,drivers,technicians,admins',
            'publish_at' => 'nullable|date',
            'expire_at' => 'nullable|date|after:publish_at',
            'attachments.*' => 'nullable|file|max:10240'
        ]);
        
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('announcement-attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ];
            }
        }
        
        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'created_by' => Auth::id(),
            'type' => $request->type,
            'target_audience' => $request->target_audience,
            'publish_at' => $request->publish_at,
            'expire_at' => $request->expire_at,
            'attachments' => $attachments,
            'is_active' => true
        ]);
        
        // Send notifications to target audience
        $this->notifyUsersAboutAnnouncement($announcement);
        
        return redirect()->route('communication.announcements')
            ->with('success', 'Announcement created successfully');
    }

    /**
     * View announcement details
     */
    public function viewAnnouncement($id)
    {
        $announcement = Announcement::with('creator')->findOrFail($id);
        
        // Check if user can view this announcement
        $user = Auth::user();
        $userRole = strtolower($user->role);
        
        // Map singular roles to plural for target_audience
        $roleMap = [
            'admin' => 'admins',
            'driver' => 'drivers',
            'technician' => 'technicians'
        ];
        
        $targetRole = $roleMap[$userRole] ?? $userRole;
        
        if (!in_array($announcement->target_audience, ['all', $targetRole])) {
            abort(403, 'You are not authorized to view this announcement');
        }
        
        // Increment view count
        $announcement->incrementViews();
        
        return view('communication.view-announcement', compact('announcement'));
    }

    /**
     * Update user notification preferences
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'maintenance_alerts' => 'boolean',
            'incident_updates' => 'boolean',
            'announcement_notifications' => 'boolean',
            'message_notifications' => 'boolean',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
            'timezone' => 'nullable|timezone',
            'preferred_language' => 'nullable|string|in:en,es,fr'
        ]);
        
        $preferences = UserPreference::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'email_notifications' => $request->boolean('email_notifications'),
                'sms_notifications' => $request->boolean('sms_notifications'),
                'push_notifications' => $request->boolean('push_notifications'),
                'maintenance_alerts' => $request->boolean('maintenance_alerts'),
                'incident_updates' => $request->boolean('incident_updates'),
                'announcement_notifications' => $request->boolean('announcement_notifications'),
                'message_notifications' => $request->boolean('message_notifications'),
                'quiet_hours' => $request->quiet_hours_start && $request->quiet_hours_end ? [
                    'start' => $request->quiet_hours_start,
                    'end' => $request->quiet_hours_end
                ] : null,
                'timezone' => $request->timezone ?? 'UTC',
                'preferred_language' => $request->preferred_language ?? 'en'
            ]
        );
        
        return redirect()->back()->with('success', 'Notification preferences updated successfully');
    }

    /**
     * Mark notifications as read
     */
    public function markNotificationsRead(Request $request)
    {
        $user = Auth::user();
        
        if ($request->has('notification_id')) {
            $user->notifications()
                ->where('id', $request->notification_id)
                ->update(['read_at' => now()]);
        } else {
            $user->unreadNotifications->markAsRead();
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Get notification count for header badge
     */
    public function getNotificationCount()
    {
        $user = Auth::user();
        
        $counts = [
            'messages' => Message::where('receiver_id', $user->id)
                ->where('is_read', false)
                ->count(),
            'notifications' => $user->unreadNotifications()->count(),
            'total' => 0
        ];
        
        $counts['total'] = $counts['messages'] + $counts['notifications'];
        
        return response()->json($counts);
    }

    /**
     * Send notification to users about new announcement
     */
    private function notifyUsersAboutAnnouncement($announcement)
    {
        $query = User::query();
        
        if ($announcement->target_audience !== 'all') {
            $role = str_replace('s', '', ucfirst($announcement->target_audience));
            $query->where('role', $role);
        }
        
        $users = $query->get();
        
        Notification::send($users, new AnnouncementNotification($announcement));
    }

    /**
     * Search users for message compose
     */
    public function searchUsers(Request $request)
    {
        $search = $request->get('q');
        
        $users = User::where('id', '!=', Auth::id())
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->select('id', 'name', 'email', 'role')
            ->limit(10)
            ->get();
        
        return response()->json($users);
    }

    /**
     * Delete message
     */
    public function deleteMessage($id)
    {
        $message = Message::findOrFail($id);
        
        // Check if user can delete this message
        if ($message->sender_id !== Auth::id()) {
            abort(403, 'You can only delete your own messages');
        }
        
        $message->delete();
        
        return redirect()->back()->with('success', 'Message deleted successfully');
    }

    /**
     * Archive message
     */
    public function archiveMessage($id)
    {
        $message = Message::findOrFail($id);
        
        // Check if user can archive this message
        if ($message->receiver_id !== Auth::id() && $message->sender_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }
        
        $message->update(['status' => 'archived']);
        
        return redirect()->back()->with('success', 'Message archived successfully');
    }
}