<!-- resources/views/components/environmental-widget.blade.php -->
<div class="environmental-widget">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="fas fa-leaf"></i> Données Environnementales
            </h5>
        </div>
        <div class="card-body">
            <!-- Weather Section -->
            <div id="weather-section" class="mb-4">
                <h6 class="font-weight-bold">
                    <i class="fas fa-cloud-sun"></i> Météo Actuelle
                </h6>
                <div id="weather-content">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Air Quality Section -->
            <div id="air-quality-section" class="mb-4">
                <h6 class="font-weight-bold">
                    <i class="fas fa-wind"></i> Qualité de l'Air
                </h6>
                <div id="air-quality-content">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Suitability Section -->
            <div id="activity-section">
                <h6 class="font-weight-bold">
                    <i class="fas fa-running"></i> Activités en Plein Air
                </h6>
                <div id="activity-content">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted small">
            <i class="fas fa-info-circle"></i> Données mises à jour toutes les 30 minutes
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const greenSpaceId = {{ $greenSpaceId }};

    // Load weather data
    fetch(`/greenspaces/${greenSpaceId}/weather`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('weather-content').innerHTML =
                    `<div class="alert alert-warning">${data.error}</div>`;
                return;
            }

            document.getElementById('weather-content').innerHTML = `
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <img src="https://openweathermap.org/img/wn/${data.icon}@2x.png"
                                 alt="${data.condition}" class="mr-2" style="width: 60px;">
                            <div>
                                <h3 class="mb-0">${data.temperature}°C</h3>
                                <small class="text-muted">${data.condition}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0 small">
                            <li><i class="fas fa-thermometer-half"></i> Ressenti: ${data.feels_like}°C</li>
                            <li><i class="fas fa-tint"></i> Humidité: ${data.humidity}%</li>
                            <li><i class="fas fa-wind"></i> Vent: ${data.wind_speed} km/h</li>
                            <li><i class="fas fa-sun"></i> Lever: ${data.sunrise} | Coucher: ${data.sunset}</li>
                        </ul>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading weather:', error);
            document.getElementById('weather-content').innerHTML =
                '<div class="alert alert-danger">Erreur de chargement</div>';
        });

    // Load air quality data
    fetch(`/greenspaces/${greenSpaceId}/air-quality`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('air-quality-content').innerHTML =
                    `<div class="alert alert-warning">${data.error}</div>`;
                return;
            }

            const badgeClass = `badge badge-${data.color} badge-lg`;

            document.getElementById('air-quality-content').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-center">
                            <h2 class="mb-1">${data.aqi}</h2>
                            <span class="${badgeClass}">${data.level}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0 small">
                            ${data.pollutants.pm25 ? `<li>PM2.5: ${data.pollutants.pm25}</li>` : ''}
                            ${data.pollutants.pm10 ? `<li>PM10: ${data.pollutants.pm10}</li>` : ''}
                            ${data.pollutants.o3 ? `<li>O₃: ${data.pollutants.o3}</li>` : ''}
                        </ul>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading air quality:', error);
            document.getElementById('air-quality-content').innerHTML =
                '<div class="alert alert-danger">Erreur de chargement</div>';
        });

    // Load activity suitability
    fetch(`/greenspaces/${greenSpaceId}/activity-suitability`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('activity-content').innerHTML =
                    `<div class="alert alert-warning">${data.error}</div>`;
                return;
            }

            const alertClass = data.suitable ? 'alert-success' : 'alert-warning';
            const icon = data.suitable ? 'fa-check-circle' : 'fa-exclamation-triangle';

            document.getElementById('activity-content').innerHTML = `
                <div class="alert ${alertClass} mb-0">
                    <i class="fas ${icon}"></i> ${data.message}
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading activity suitability:', error);
            document.getElementById('activity-content').innerHTML =
                '<div class="alert alert-danger">Erreur de chargement</div>';
        });
});
</script>

<style>
.environmental-widget .card {
    border-radius: 10px;
}

.environmental-widget .badge-lg {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
}

.environmental-widget h6 {
    color: #2d6a4f;
    border-bottom: 2px solid #d8f3dc;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}
</style>
