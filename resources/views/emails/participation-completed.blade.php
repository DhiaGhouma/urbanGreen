<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merci pour votre participation</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .email-header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .email-body {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
        }
        .message {
            font-size: 16px;
            margin-bottom: 25px;
            color: #555;
        }
        .participation-details {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .participation-details h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #28a745;
            font-size: 18px;
        }
        .detail-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            min-width: 120px;
            color: #666;
        }
        .detail-value {
            color: #333;
        }
        .status-badge {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: bold;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }
        .cta-container {
            text-align: center;
            margin: 30px 0;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
        .email-footer p {
            margin: 5px 0;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 25px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="icon">🌱</div>
            <h1>Merci pour votre participation !</h1>
            <p>Votre contribution fait la différence</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                Bonjour {{ $participation->user->name }},
            </div>

            <div class="message">
                Nous tenons à vous remercier chaleureusement pour votre participation à notre initiative UrbanGreen ! 
                Grâce à des personnes engagées comme vous, nous contribuons ensemble à un environnement plus vert et durable. 🌿
            </div>

            <!-- Participation Details -->
            <div class="participation-details">
                <h3>📋 Détails de votre participation</h3>
                
                <div class="detail-row">
                    <div class="detail-label">📍 Espace Vert:</div>
                    <div class="detail-value">{{ $participation->greenSpace->name }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">🗺️ Lieu:</div>
                    <div class="detail-value">{{ $participation->greenSpace->location }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">📅 Date:</div>
                    <div class="detail-value">{{ $participation->date->format('d/m/Y') }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">✅ Statut:</div>
                    <div class="detail-value">
                        <span class="status-badge">Terminée</span>
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            <div class="message">
                <strong>Votre avis compte !</strong><br>
                Aidez-nous à améliorer nos espaces verts en partageant votre expérience. 
                Votre feedback est précieux pour la communauté et nous permet d'organiser de meilleures activités à l'avenir.
            </div>

            <!-- Call to Action -->
            <div class="cta-container">
                <a href="{{ route('participations.show', $participation->id) }}" class="cta-button">
                    📝 Partagez votre expérience
                </a>
            </div>

            <div class="message" style="text-align: center; color: #888; font-size: 14px;">
                Le formulaire de feedback est maintenant disponible sur votre page de participation.
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p><strong>Merci encore pour votre engagement envers l'environnement !</strong></p>
            <p style="margin-top: 15px;">L'équipe UrbanGreen 🌱</p>
            <p style="margin-top: 10px; font-size: 12px; color: #999;">
                Cet email a été envoyé automatiquement. Merci de ne pas y répondre.
            </p>
        </div>
    </div>
</body>
</html>
