<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventMessage;
use App\Models\EventMessageReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EventChatController extends Controller
{
    /**
     * Show the chat interface for an event.
     */
    public function show(Event $event)
    {
        // Check if user has access (registered or organizer)
        if (!$this->canAccessChat($event)) {
            return redirect()->route('events.show', $event)
                ->with('error', 'Vous devez être inscrit à cet événement pour accéder au chat.');
        }

        // Generate chat token if not exists
        if (!$event->chat_token) {
            $event->update(['chat_token' => Str::random(32)]);
        }

        // Get messages with pagination
        $messages = EventMessage::where('event_id', $event->id)
            ->with(['user', 'replyTo.user', 'reactions', 'reads'])
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        foreach ($messages as $message) {
            $message->markAsReadBy(Auth::id());
        }

        // Get pinned messages
        $pinnedMessages = $messages->where('is_pinned', true);

        // Get participants count
        $participantsCount = $event->registrations()
            ->where('statut', 'confirmee')
            ->count();

        return view('events.chat', compact('event', 'messages', 'pinnedMessages', 'participantsCount'));
    }

    /**
     * Show chat via QR code token.
     */
    public function showByToken(string $token)
    {
        $event = Event::where('chat_token', $token)->firstOrFail();

        // Redirect to regular chat
        return redirect()->route('events.chat', $event);
    }

    /**
     * Send a message.
     */
    public function sendMessage(Request $request, Event $event)
    {
        if (!$this->canAccessChat($event)) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'reply_to_id' => 'nullable|exists:event_messages,id',
            'is_important' => 'boolean',
        ]);

        $message = EventMessage::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'type' => 'text',
            'reply_to_id' => $validated['reply_to_id'] ?? null,
            'is_important' => $validated['is_important'] ?? false,
        ]);

        $message->load(['user', 'replyTo.user']);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Upload an image/file.
     */
    public function uploadAttachment(Request $request, Event $event)
    {
        if (!$this->canAccessChat($event)) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        $request->validate([
            'attachment' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:5120',
        ]);

        $path = $request->file('attachment')->store('chat-attachments', 'public');
        $type = str_starts_with($request->file('attachment')->getMimeType(), 'image/') ? 'image' : 'file';

        $message = EventMessage::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'message' => '📎 Fichier partagé',
            'type' => $type,
            'attachment_path' => $path,
        ]);

        $message->load('user');

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Delete a message.
     */
    public function deleteMessage(Event $event, EventMessage $message)
    {
        // Only message owner or event organizer can delete
        if ($message->user_id !== Auth::id() && $event->association->user_id !== Auth::id()) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        // Delete attachment if exists
        if ($message->attachment_path) {
            Storage::disk('public')->delete($message->attachment_path);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Pin/unpin a message (organizer only).
     */
    public function togglePin(Event $event, EventMessage $message)
    {
        if (!$this->isEventOrganizer($event)) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        $message->update(['is_pinned' => !$message->is_pinned]);

        return response()->json([
            'success' => true,
            'is_pinned' => $message->is_pinned,
        ]);
    }

    /**
     * Add reaction to a message.
     */
    public function addReaction(Request $request, Event $event, EventMessage $message)
    {
        if (!$this->canAccessChat($event)) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        $validated = $request->validate([
            'emoji' => 'required|string|max:10',
        ]);

        // Toggle reaction
        $existing = EventMessageReaction::where([
            'event_message_id' => $message->id,
            'user_id' => Auth::id(),
            'emoji' => $validated['emoji'],
        ])->first();

        if ($existing) {
            $existing->delete();
            $action = 'removed';
        } else {
            EventMessageReaction::create([
                'event_message_id' => $message->id,
                'user_id' => Auth::id(),
                'emoji' => $validated['emoji'],
            ]);
            $action = 'added';
        }

        $reactions = $message->reactions()
            ->selectRaw('emoji, COUNT(*) as count')
            ->groupBy('emoji')
            ->get();

        return response()->json([
            'success' => true,
            'action' => $action,
            'reactions' => $reactions,
        ]);
    }

    /**
     * Get new messages (polling).
     */
    public function getNewMessages(Request $request, Event $event)
    {
        if (!$this->canAccessChat($event)) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        $lastId = $request->query('last_id', 0);

        $messages = EventMessage::where('event_id', $event->id)
            ->where('id', '>', $lastId)
            ->with(['user', 'replyTo.user', 'reactions'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        foreach ($messages as $message) {
            $message->markAsReadBy(Auth::id());
        }

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Generate QR code for event chat.
     */
    public function generateQrCode(Event $event)
    {
        if (!$this->isEventOrganizer($event)) {
            abort(403);
        }

        // Generate token if not exists
        if (!$event->chat_token) {
            $event->update(['chat_token' => Str::random(32)]);
        }

        $url = route('events.chat.token', $event->chat_token);

        // Generate QR code
        $qrCode = QrCode::size(300)
            ->format('png')
            ->generate($url);

        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'inline; filename="event-chat-qr-' . $event->id . '.png"');
    }

    /**
     * Download QR code as image.
     */
    public function downloadQrCode(Event $event)
    {
        if (!$this->isEventOrganizer($event)) {
            abort(403);
        }

        if (!$event->chat_token) {
            $event->update(['chat_token' => Str::random(32)]);
        }

        $url = route('events.chat.token', $event->chat_token);

        $qrCode = QrCode::size(500)
            ->format('png')
            ->generate($url);

        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="urbangreen-event-' . $event->id . '-qr.png"');
    }

    /**
     * Check if user can access chat.
     */
    private function canAccessChat(Event $event): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // Organizer always has access
        if ($this->isEventOrganizer($event)) {
            return true;
        }

        // Registered participants have access
        return $event->registrations()
            ->where('user_id', Auth::id())
            ->whereIn('statut', ['confirmee', 'en_attente'])
            ->exists();
    }

    /**
     * Check if user is event organizer.
     */
    private function isEventOrganizer(Event $event): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Si vous avez un système de rôles
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }

        // Si l'association a un user_id (propriétaire)
        if (isset($event->association->user_id)) {
            return $event->association->user_id === $user->id;
        }

        // Sinon, adaptez cette logique selon votre structure
        // Par exemple, si vous avez une table pivot users_associations:
        // return $user->associations()->where('associations.id', $event->association_id)->exists();

        return false;
    }
}
