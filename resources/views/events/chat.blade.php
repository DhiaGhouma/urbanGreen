@extends('layouts.app')

@section('title', 'Chat - ' . $event->titre)

@section('content')
<div class="container-fluid" style="max-width: 1400px;">
    <!-- Header -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-comments me-2"></i>{{ $event->titre }}
                        </h4>
                        <small class="text-muted">
                            <i class="fas fa-users me-1"></i>{{ $participantsCount }} participants
                            <span class="ms-2">•</span>
                            <i class="fas fa-calendar ms-2 me-1"></i>{{ $event->date_debut->format('d/m/Y à H:i') }}
                        </small>
                    </div>
                </div>

                @if($event->association->user_id === Auth::id() || Auth::user()->hasRole('admin'))
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#qrCodeModal">
                            <i class="fas fa-qrcode me-2"></i>QR Code
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Chat Messages -->
        <div class="col-lg-9">
            <!-- Pinned Messages -->
            @if($pinnedMessages->count() > 0)
                <div class="card mb-3 border-warning">
                    <div class="card-body bg-warning bg-opacity-10">
                        <h6 class="text-warning mb-3">
                            <i class="fas fa-thumbtack me-2"></i>Messages épinglés
                        </h6>
                        @foreach($pinnedMessages as $pinned)
                            <div class="d-flex mb-2">
                                <strong class="me-2">{{ $pinned->user->name }}:</strong>
                                <span>{!! $pinned->formatted_message !!}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Messages Container -->
            <div class="card" style="height: calc(100vh - 300px);">
                <div class="card-body p-0 d-flex flex-column">
                    <!-- Messages -->
                    <div id="messagesContainer" class="flex-grow-1 overflow-auto p-3" style="scroll-behavior: smooth;">
                        @foreach($messages->where('is_pinned', false) as $message)
                            @include('events.partials.chat-message', ['message' => $message])
                        @endforeach
                    </div>

                    <!-- Message Input -->
                    <div class="border-top p-3">
                        <form id="messageForm" class="d-flex gap-2">
                            @csrf
                            <input type="hidden" id="replyToId" name="reply_to_id" value="">

                            <div class="flex-grow-1 position-relative">
                                <div id="replyPreview" class="alert alert-info alert-dismissible fade show mb-2" style="display: none;">
                                    <small>
                                        <i class="fas fa-reply me-1"></i>
                                        Répondre à: <strong id="replyToName"></strong>
                                    </small>
                                    <button type="button" class="btn-close" onclick="cancelReply()"></button>
                                </div>

                                <textarea
                                    id="messageInput"
                                    class="form-control"
                                    rows="2"
                                    placeholder="Votre message..."
                                    required></textarea>
                            </div>

                            <div class="d-flex flex-column gap-2">
                                <label for="attachmentInput" class="btn btn-outline-secondary mb-0">
                                    <i class="fas fa-paperclip"></i>
                                </label>
                                <input type="file" id="attachmentInput" class="d-none" accept="image/*,.pdf,.doc,.docx">

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Quick Actions -->
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="fas fa-bolt me-2"></i>Actions rapides
                    </h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="quickMessage('👋 Bonjour à tous !')">
                            👋 Saluer
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="quickMessage('✅ Je suis arrivé(e) !')">
                            ✅ J'arrive
                        </button>
                        <button class="btn btn-outline-warning btn-sm" onclick="quickMessage('⏰ Je serai en retard')">
                            ⏰ Retard
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="quickMessage('❓ Une question ?')">
                            ❓ Question
                        </button>
                    </div>
                </div>
            </div>

            <!-- Event Info -->
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="fas fa-info-circle me-2"></i>Informations
                    </h6>
                    <div class="mb-2">
                        <small class="text-muted">Lieu</small>
                        <p class="mb-0">{{ $event->lieu }}</p>
                    </div>
                    @if($event->adresse)
                        <div class="mb-2">
                            <small class="text-muted">Adresse</small>
                            <p class="mb-0">{{ $event->adresse }}</p>
                        </div>
                    @endif
                    <div class="mb-2">
                        <small class="text-muted">Association</small>
                        <p class="mb-0">{{ $event->association->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode me-2"></i>QR Code du Chat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p class="text-muted mb-3">
                    Partagez ce QR code pour permettre aux participants d'accéder directement au chat
                </p>
                <img src="{{ route('events.chat.qrcode', $event) }}"
                     alt="QR Code"
                     class="img-fluid mb-3"
                     style="max-width: 300px;">
                <div class="d-grid gap-2">
                    <a href="{{ route('events.chat.qrcode.download', $event) }}"
                       class="btn btn-primary"
                       download>
                        <i class="fas fa-download me-2"></i>Télécharger le QR Code
                    </a>
                    <button class="btn btn-outline-secondary"
                            onclick="copyToClipboard('{{ route('events.chat.token', $event->chat_token) }}')">
                        <i class="fas fa-link me-2"></i>Copier le lien
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let lastMessageId = {{ $messages->last()->id ?? 0 }};
let pollingInterval;

document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
    startPolling();

    // Form submit
    document.getElementById('messageForm').addEventListener('submit', sendMessage);

    // Attachment upload
    document.getElementById('attachmentInput').addEventListener('change', uploadAttachment);
});

// Send message
async function sendMessage(e) {
    e.preventDefault();

    const message = document.getElementById('messageInput').value.trim();
    if (!message) return;

    const formData = new FormData();
    formData.append('message', message);
    formData.append('reply_to_id', document.getElementById('replyToId').value);

    try {
        const response = await fetch('{{ route("events.chat.send", $event) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            document.getElementById('messageInput').value = '';
            cancelReply();
            appendMessage(data.message);
            scrollToBottom();
            lastMessageId = data.message.id;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Erreur lors de l\'envoi du message');
    }
}

// Upload attachment
async function uploadAttachment(e) {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('attachment', file);

    try {
        const response = await fetch('{{ route("events.chat.upload", $event) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            appendMessage(data.message);
            scrollToBottom();
            lastMessageId = data.message.id;
            e.target.value = '';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Erreur lors de l\'upload');
    }
}

// Poll for new messages
async function pollMessages() {
    try {
        const response = await fetch(`{{ route("events.chat.poll", $event) }}?last_id=${lastMessageId}`);
        const data = await response.json();

        if (data.success && data.messages.length > 0) {
            data.messages.forEach(message => {
                appendMessage(message);
                lastMessageId = message.id;
            });
            scrollToBottom();
        }
    } catch (error) {
        console.error('Polling error:', error);
    }
}

// Start polling
function startPolling() {
    pollingInterval = setInterval(pollMessages, 3000); // Every 3 seconds
}

// Stop polling
function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
}

// Append message to container
function appendMessage(message) {
    const container = document.getElementById('messagesContainer');
    const messageHtml = createMessageHtml(message);
    container.insertAdjacentHTML('beforeend', messageHtml);
}

// Create message HTML
function createMessageHtml(message) {
    const isOwn = message.user.id === {{ Auth::id() }};
    const alignClass = isOwn ? 'justify-content-end' : '';
    const bgClass = isOwn ? 'bg-primary text-white' : 'bg-light';

    let html = `<div class="d-flex ${alignClass} mb-3" id="message-${message.id}">`;

    if (!isOwn) {
        html += `
            <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                 style="width: 40px; height: 40px; min-width: 40px;">
                ${message.user.name.charAt(0).toUpperCase()}
            </div>
        `;
    }

    html += `
        <div class="message-bubble ${bgClass} rounded p-3" style="max-width: 70%;">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <strong class="me-2">${message.user.name}</strong>
                <small class="opacity-75">${formatTime(message.created_at)}</small>
            </div>
    `;

    // Reply preview
    if (message.reply_to) {
        html += `
            <div class="reply-preview bg-white bg-opacity-25 rounded p-2 mb-2">
                <small>
                    <i class="fas fa-reply me-1"></i>
                    <strong>${message.reply_to.user.name}:</strong>
                    ${truncate(message.reply_to.message, 50)}
                </small>
            </div>
        `;
    }

    // Message content
    if (message.type === 'image' && message.attachment_path) {
        html += `
            <a href="/storage/${message.attachment_path}" target="_blank">
                <img src="/storage/${message.attachment_path}"
                     class="img-fluid rounded mb-2"
                     style="max-width: 100%;">
            </a>
        `;
    } else if (message.type === 'file' && message.attachment_path) {
        html += `
            <a href="/storage/${message.attachment_path}"
               class="btn btn-sm btn-light"
               target="_blank">
                <i class="fas fa-file me-2"></i>Télécharger
            </a>
        `;
    }

    html += `<div>${message.message.replace(/\n/g, '<br>')}</div>`;

    // Actions
    html += `
            <div class="message-actions mt-2 d-flex gap-2">
                <button class="btn btn-sm btn-link p-0" onclick="replyToMessage(${message.id}, '${message.user.name}')">
                    <i class="fas fa-reply"></i>
                </button>
                <button class="btn btn-sm btn-link p-0" onclick="addReaction(${message.id}, '👍')">👍</button>
                <button class="btn btn-sm btn-link p-0" onclick="addReaction(${message.id}, '❤️')">❤️</button>
                <button class="btn btn-sm btn-link p-0" onclick="addReaction(${message.id}, '😊')">😊</button>
    `;

    if (isOwn) {
        html += `
                <button class="btn btn-sm btn-link p-0 text-danger" onclick="deleteMessage(${message.id})">
                    <i class="fas fa-trash"></i>
                </button>
        `;
    }

    html += `
            </div>
        </div>
    </div>
    `;

    return html;
}

// Reply to message
function replyToMessage(messageId, userName) {
    document.getElementById('replyToId').value = messageId;
    document.getElementById('replyToName').textContent = userName;
    document.getElementById('replyPreview').style.display = 'block';
    document.getElementById('messageInput').focus();
}

// Cancel reply
function cancelReply() {
    document.getElementById('replyToId').value = '';
    document.getElementById('replyPreview').style.display = 'none';
}

// Quick message
function quickMessage(text) {
    document.getElementById('messageInput').value = text;
    document.getElementById('messageInput').focus();
}

// Add reaction
async function addReaction(messageId, emoji) {
    try {
        const response = await fetch(`{{ route("events.chat.show", $event) }}/${messageId}/reaction`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ emoji })
        });

        const data = await response.json();
        if (data.success) {
            // Update reactions display
            console.log('Reaction ' + data.action);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Delete message
async function deleteMessage(messageId) {
    if (!confirm('Supprimer ce message ?')) return;

    try {
        const response = await fetch(`{{ route("events.chat.show", $event) }}/${messageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        });

        const data = await response.json();
        if (data.success) {
            document.getElementById(`message-${messageId}`).remove();
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Lien copié !');
    });
}

// Scroll to bottom
function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    container.scrollTop = container.scrollHeight;
}

// Format time
function formatTime(datetime) {
    const date = new Date(datetime);
    const now = new Date();
    const diff = now - date;

    if (diff < 60000) return 'À l\'instant';
    if (diff < 3600000) return Math.floor(diff / 60000) + ' min';
    if (diff < 86400000) return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
}

// Truncate text
function truncate(text, length) {
    return text.length > length ? text.substring(0, length) + '...' : text;
}

// Cleanup on page unload
window.addEventListener('beforeunload', stopPolling);
</script>

<style>
.message-bubble {
    position: relative;
    word-wrap: break-word;
}

.message-actions {
    opacity: 0;
    transition: opacity 0.2s;
}

.message-bubble:hover .message-actions {
    opacity: 1;
}

#messagesContainer::-webkit-scrollbar {
    width: 8px;
}

#messagesContainer::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#messagesContainer::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

#messagesContainer::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.reply-preview {
    border-left: 3px solid currentColor;
}

.avatar {
    font-weight: bold;
}
</style>
@endsection
