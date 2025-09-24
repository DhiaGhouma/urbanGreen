@extends('layouts.app')

@section('title', 'UrbanGreen - Végétalisons Nos Villes Ensemble')

@section('content')
<style>
    .gradient-hero {
        background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .gradient-primary {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .gradient-subtle {
        background: radial-gradient(ellipse at top, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
    }

    .hover-scale {
        transition: transform 0.2s ease;
    }

    .hover-scale:hover {
        transform: scale(1.05);
    }

    .shadow-elegant {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .shadow-glow {
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
    }

    .animate-fade-in {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="container-fluid px-4 space-y-5" style="margin-top: -2rem;">

    <!-- Hero Section -->
    <section class="position-relative py-5 px-4 text-center rounded-4 overflow-hidden"
             style="background: linear-gradient(135deg, rgba(5, 150, 105, 0.1) 0%, rgba(255, 255, 255, 1) 50%, rgba(52, 211, 153, 0.05) 100%); min-height: 60vh; display: flex; align-items: center;">
        <div class="gradient-subtle position-absolute top-0 start-0 w-100 h-100" style="opacity: 0.5;"></div>
        <div class="position-relative mx-auto" style="max-width: 800px;">
            <div class="d-flex justify-content-center mb-4">
                <div class="d-flex align-items-center justify-content-center rounded-4 gradient-primary shadow-glow"
                     style="height: 64px; width: 64px;">
                    <i class="fas fa-leaf text-white" style="font-size: 2rem;"></i>
                </div>
            </div>
            <h1 class="display-3 fw-bold mb-4 gradient-hero">
                Végétalisons Nos Villes Ensemble
            </h1>
            <p class="fs-5 text-muted mb-4 mx-auto lh-lg" style="max-width: 600px;">
                Rejoignez le mouvement UrbanGreen pour transformer les espaces urbains en oasis de verdure.
                Collaborez avec des associations locales et participez à des projets qui améliorent notre qualité de vie.
            </p>
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <a href="{{ route('projects.index') }}" class="btn btn-lg gradient-primary text-white hover-scale shadow-elegant">
                    Découvrir les Projets
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
                <a href="{{ route('associations.index') }}" class="btn btn-outline-primary btn-lg hover-scale">
                    Rejoindre une Association
                </a>
            </div>
        </div>
    </section>

    <!-- Statistics -->
    <section class="row g-4 my-5">
        <div class="col-6 col-md-3">
            <div class="card text-center hover-scale border-primary border-opacity-25 h-100"
                 style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                <div class="card-body pt-4">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10"
                             style="height: 48px; width: 48px;">
                            <i class="fas fa-tree text-primary"></i>
                        </div>
                    </div>
                    <div class="display-6 fw-bold text-dark mb-1">24</div>
                    <div class="small text-muted">Projets Actifs</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-center hover-scale border-primary border-opacity-25 h-100"
                 style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                <div class="card-body pt-4">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10"
                             style="height: 48px; width: 48px;">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                    </div>
                    <div class="display-6 fw-bold text-dark mb-1">12</div>
                    <div class="small text-muted">Associations Partenaires</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-center hover-scale border-primary border-opacity-25 h-100"
                 style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                <div class="card-body pt-4">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10"
                             style="height: 48px; width: 48px;">
                            <i class="fas fa-building text-primary"></i>
                        </div>
                    </div>
                    <div class="display-6 fw-bold text-dark mb-1">8</div>
                    <div class="small text-muted">Espaces Transformés</div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card text-center hover-scale border-primary border-opacity-25 h-100"
                 style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                <div class="card-body pt-4">
                    <div class="d-flex justify-content-center mb-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10"
                             style="height: 48px; width: 48px;">
                            <i class="fas fa-lightbulb text-primary"></i>
                        </div>
                    </div>
                    <div class="display-6 fw-bold text-dark mb-1">156</div>
                    <div class="small text-muted">Contributions</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Projects -->
    <section class="my-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3 gradient-hero">
                Projets Phares
            </h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">
                Découvrez nos initiatives les plus impactantes qui transforment concrètement nos espaces urbains.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card hover-scale border-primary border-opacity-25 animate-fade-in h-100"
                     style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                    <div class="card-header border-0 bg-transparent">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">Jardin Communautaire Belvedère</h5>
                            <span class="badge bg-warning">En cours</span>
                        </div>
                        <p class="small text-primary fw-medium mb-0">Verts Horizons</p>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-4 lh-base">
                            Transformation d'un terrain vague en jardin partagé avec potager urbain et aire de compostage.
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 fw-semibold mb-0">15,000 €</span>
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-primary btn-sm">
                                En savoir plus
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card hover-scale border-primary border-opacity-25 animate-fade-in h-100"
                     style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                    <div class="card-header border-0 bg-transparent">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">Toitures Végétales Centre-Ville</h5>
                            <span class="badge bg-success">Terminé</span>
                        </div>
                        <p class="small text-primary fw-medium mb-0">Eco Citoyens</p>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-4 lh-base">
                            Installation de toitures végétalisées sur 5 bâtiments publics pour améliorer l'isolation thermique.
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 fw-semibold mb-0">25,000 €</span>
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-primary btn-sm">
                                En savoir plus
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card hover-scale border-primary border-opacity-25 animate-fade-in h-100"
                     style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                    <div class="card-header border-0 bg-transparent">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">Corridor Vert Lafayette</h5>
                            <span class="badge bg-secondary">Proposé</span>
                        </div>
                        <p class="small text-primary fw-medium mb-0">Nature Urbaine</p>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-4 lh-base">
                            Création d'un corridor végétal reliant deux parcs existants avec plantation d'arbres indigènes.
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 fw-semibold mb-0">18,000 €</span>
                            <a href="{{ route('projects.index') }}" class="btn btn-outline-primary btn-sm">
                                En savoir plus
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Remarkable Contributions -->
    <section class="py-5 px-4 rounded-4 my-5"
             style="background: linear-gradient(135deg, rgba(52, 211, 153, 0.05) 0%, rgba(5, 150, 105, 0.05) 100%);">
        <div class="text-center mb-5">
            <div class="d-flex justify-content-center mb-3">
                <i class="fas fa-award text-primary" style="font-size: 2rem;"></i>
            </div>
            <h2 class="display-5 fw-bold mb-3 gradient-hero">
                Contributions Remarquables
            </h2>
        </div>

        <div class="row g-5 mx-auto" style="max-width: 800px;">
            <div class="col-md-6 text-center">
                <div class="display-4 fw-bold text-primary mb-2">2,500+</div>
                <div class="text-muted mb-3">Arbres Plantés</div>
                <p class="small text-muted lh-base">
                    Grâce à nos associations partenaires, nous avons contribué à la plantation de plus de 2,500 arbres
                    dans la région, améliorant significativement la qualité de l'air urbain.
                </p>
            </div>
            <div class="col-md-6 text-center">
                <div class="display-4 fw-bold text-primary mb-2">15,000 m²</div>
                <div class="text-muted mb-3">Espaces Végétalisés</div>
                <p class="small text-muted lh-base">
                    Transformation de zones urbaines délaissées en espaces verts fonctionnels,
                    créant des îlots de fraîcheur et des lieux de rencontre pour les habitants.
                </p>
            </div>
        </div>
    </section>

    <!-- Before/After Transformations -->
    <section class="my-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3 gradient-hero">
                Transformations Réalisées
            </h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">
                Découvrez l'impact concret de nos projets sur l'environnement urbain.
            </p>
        </div>

        <div class="row g-5">
            <div class="col-12">
                <div class="card overflow-hidden border-primary border-opacity-25"
                     style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                    <div class="card-body p-5">
                        <h3 class="h2 fw-bold mb-4 text-center">Place de la République</h3>
                        <div class="row g-4 align-items-center">
                            <div class="col-md-4 text-center">
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mb-3"
                                     style="height: 128px;">
                                    <div class="text-muted">Avant</div>
                                </div>
                                <p class="fw-medium text-muted">Parking asphalté</p>
                            </div>

                            <div class="col-md-4 d-flex justify-content-center">
                                <i class="fas fa-arrow-right text-primary" style="font-size: 2rem;"></i>
                            </div>

                            <div class="col-md-4 text-center">
                                <div class="gradient-primary rounded-3 d-flex align-items-center justify-content-center mb-3"
                                     style="height: 128px;">
                                    <div class="text-white">Après</div>
                                </div>
                                <p class="fw-medium">Jardin urbain avec fontaine</p>
                            </div>
                        </div>

                        <div class="text-center mt-4 p-3 bg-primary bg-opacity-10 rounded-3">
                            <p class="text-primary fw-semibold mb-0">Impact: 80% de réduction de température</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card overflow-hidden border-primary border-opacity-25"
                     style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                    <div class="card-body p-5">
                        <h3 class="h2 fw-bold mb-4 text-center">Rue des Oliviers</h3>
                        <div class="row g-4 align-items-center">
                            <div class="col-md-4 text-center">
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mb-3"
                                     style="height: 128px;">
                                    <div class="text-muted">Avant</div>
                                </div>
                                <p class="fw-medium text-muted">Murs aveugles</p>
                            </div>

                            <div class="col-md-4 d-flex justify-content-center">
                                <i class="fas fa-arrow-right text-primary" style="font-size: 2rem;"></i>
                            </div>

                            <div class="col-md-4 text-center">
                                <div class="gradient-primary rounded-3 d-flex align-items-center justify-content-center mb-3"
                                     style="height: 128px;">
                                    <div class="text-white">Après</div>
                                </div>
                                <p class="fw-medium">Murs végétalisés</p>
                            </div>
                        </div>

                        <div class="text-center mt-4 p-3 bg-primary bg-opacity-10 rounded-3">
                            <p class="text-primary fw-semibold mb-0">Impact: Amélioration qualité de l'air</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="text-center py-5 px-4 rounded-4 my-5"
             style="background: linear-gradient(135deg, rgba(5, 150, 105, 0.1) 0%, rgba(255, 255, 255, 1) 50%, rgba(52, 211, 153, 0.05) 100%);">
        <h2 class="display-5 fw-bold mb-3 gradient-hero">
            Rejoignez le Mouvement
        </h2>
        <p class="text-muted mb-4 mx-auto lh-base" style="max-width: 600px;">
            Ensemble, créons des villes plus vertes et plus durables.
            Chaque action compte pour améliorer notre environnement urbain.
        </p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="{{ route('associations.create') }}" class="btn btn-lg gradient-primary text-white hover-scale shadow-elegant">
                Créer une Association
            </a>
            <a href="{{ route('projects.create') }}" class="btn btn-outline-primary btn-lg hover-scale">
                Proposer un Projet
            </a>
        </div>
    </section>

</div>

<script>
    // Add some interactive animations
    document.addEventListener('DOMContentLoaded', function() {
        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards
        document.querySelectorAll('.animate-fade-in, .card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    });
</script>
@endsection
