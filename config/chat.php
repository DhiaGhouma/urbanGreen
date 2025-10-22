<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Chat Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour le système de chat d'événements UrbanGreen
    |
    */

    // Intervalle de polling en millisecondes
    'polling_interval' => env('CHAT_POLLING_INTERVAL', 3000),

    // Taille maximale des fichiers uploadés (en KB)
    'max_file_size' => env('CHAT_MAX_FILE_SIZE', 5120), // 5MB

    // Types MIME autorisés pour les uploads
    'allowed_mimes' => [
        'jpeg', 'jpg', 'png', 'gif', 'webp',  // Images
        'pdf',                                 // PDF
        'doc', 'docx',                        // Word
        'xls', 'xlsx',                        // Excel
        'txt',                                // Texte
    ],

    // Longueur maximale d'un message texte
    'max_message_length' => env('CHAT_MAX_MESSAGE_LENGTH', 2000),

    // Nombre de messages à charger initialement
    'initial_messages_limit' => env('CHAT_INITIAL_MESSAGES', 100),

    // Durée de conservation des messages supprimés (jours)
    'soft_delete_retention_days' => 30,

    // Emojis disponibles pour les réactions
    'reaction_emojis' => [
        '👍', '❤️', '😊', '🎉', '👏',
        '🔥', '✅', '💚', '🌱', '🌍'
    ],

    // Messages rapides prédéfinis
    'quick_messages' => [
        [
            'emoji' => '👋',
            'text' => 'Bonjour à tous !',
            'label' => 'Saluer'
        ],
        [
            'emoji' => '✅',
            'text' => 'Je suis arrivé(e) !',
            'label' => 'J\'arrive'
        ],
        [
            'emoji' => '⏰',
            'text' => 'Je serai en retard',
            'label' => 'Retard'
        ],
        [
            'emoji' => '❓',
            'text' => 'Une question ?',
            'label' => 'Question'
        ],
        [
            'emoji' => '🌱',
            'text' => 'Prêt(e) à planter !',
            'label' => 'Prêt'
        ],
    ],

    // Taille du QR code (en pixels)
    'qrcode_size' => env('CHAT_QRCODE_SIZE', 500),

    // Format du QR code
    'qrcode_format' => env('CHAT_QRCODE_FORMAT', 'png'),

    // Activer les notifications (pour future implémentation)
    'notifications_enabled' => env('CHAT_NOTIFICATIONS_ENABLED', false),

    // Modération automatique
    'auto_moderation' => [
        'enabled' => env('CHAT_AUTO_MODERATION', false),
        'banned_words' => [
            // Ajoutez ici les mots à bannir
        ],
    ],

    // Permissions
    'permissions' => [
        'can_delete_any_message' => ['admin', 'organizer'],
        'can_pin_messages' => ['admin', 'organizer'],
        'can_send_important_messages' => ['admin', 'organizer'],
    ],
];
