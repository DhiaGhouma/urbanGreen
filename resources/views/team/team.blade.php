@extends('layouts.app')

@section('title', 'Notre Équipe - UrbanGreen')

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

    .avatar-gradient {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .social-icon {
        width: 32px;
        height: 32px;
        background-color: #f3f4f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .social-icon:hover {
        background-color: rgba(16, 185, 129, 0.2);
        transform: scale(1.1);
    }

    .skill-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        background-color: #e5e7eb;
        color: #374151;
        border-radius: 0.375rem;
    }

    .mission-emoji {
        font-size: 3rem;
        line-height: 1;
    }
</style>

<div class="container-fluid px-4 py-4">

    <!-- Header -->
    <section class="text-center py-5 mb-5 rounded-4"
             style="background: linear-gradient(135deg, rgba(5, 150, 105, 0.1) 0%, rgba(255, 255, 255, 1) 50%, rgba(52, 211, 153, 0.05) 100%);">
        <div class="d-flex justify-content-center mb-4">
            <div class="d-flex align-items-center justify-content-center rounded-4 gradient-primary shadow-glow"
                 style="height: 64px; width: 64px;">
                <i class="fas fa-users text-white" style="font-size: 2rem;"></i>
            </div>
        </div>
        <h1 class="display-4 fw-bold mb-4 gradient-hero">
            Notre Équipe
        </h1>
        <p class="fs-5 text-muted mx-auto lh-lg" style="max-width: 800px;">
            Une équipe passionnée et multidisciplinaire dédiée à la transformation de nos espaces urbains.
            Ensemble, nous créons des solutions innovantes pour un avenir plus vert.
        </p>
    </section>
    <!-- Team Members -->
    <section class="mb-5">
        <h2 class="display-5 fw-bold text-center mb-5 gradient-hero">
            Rencontrez l'Équipe
        </h2>

        <div class="row g-4">
            <!-- Khalil Ayari -->
            <div class="col-md-6 col-lg-4">
                <div class="card hover-scale border-primary border-opacity-25 animate-fade-in h-100"
                     style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                    <div class="card-body p-4">
                        <!-- Avatar -->
                        <div class="avatar-gradient rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width: 96px; height: 96px; font-size: 1.5rem;">
                            KA
                        </div>

                        <!-- Member Info -->
                        <div class="text-center mb-3">
                            <h3 class="h5 fw-bold text-dark mb-1">Khalil Ayari</h3>
                            <p class="text-primary fw-semibold mb-2">Chef de Projet</p>
                            <span class="badge bg-light text-dark border mb-3">Architecture & Coordination</span>
                        </div>

                        <!-- Description -->
                        <p class="small text-muted text-center mb-3 lh-base">
                            Passionné par l'innovation urbaine et la gestion de projets complexes. Expert en coordination d'équipes multidisciplinaires.
                        </p>

                        <!-- Skills -->
                        <div class="d-flex flex-wrap gap-1 justify-content-center mb-3">
                            <span class="skill-badge">Management</span>
                            <span class="skill-badge">Architecture</span>
                            <span class="skill-badge">Innovation</span>
                        </div>

                        <!-- Social Links -->
                        <div class="d-flex justify-content-center gap-2">
                            <div class="social-icon">
                                <i class="fas fa-envelope text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                            <div class="social-icon">
                                <i class="fab fa-linkedin text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                            <div class="social-icon">
                                <i class="fab fa-github text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dhia Ghouma -->
            <div class="col-md-6 col-lg-4">
                <div class="card hover-scale border-primary border-opacity-25 animate-fade-in h-100"
                     style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                    <div class="card-body p-4">
                        <div class="avatar-gradient rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width: 96px; height: 96px; font-size: 1.5rem;">
                            DG
                        </div>

                        <div class="text-center mb-3">
                            <h3 class="h5 fw-bold text-dark mb-1">Dhia Ghouma</h3>
                            <p class="text-primary fw-semibold mb-2">Développeur Full-Stack</p>
                            <span class="badge bg-light text-dark border mb-3">Solutions Techniques</span>
                        </div>

                        <p class="small text-muted text-center mb-3 lh-base">
                            Spécialiste en développement web moderne et en architecture logicielle. Créateur de solutions durables et performantes.
                        </p>

                        <div class="d-flex flex-wrap gap-1 justify-content-center mb-3">
                            <span class="skill-badge">React</span>
                            <span class="skill-badge">Node.js</span>
                            <span class="skill-badge">DevOps</span>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <div class="social-icon">
                                <i class="fas fa-envelope text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                            <div class="social-icon">
                                <i class="fab fa-linkedin text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                            <div class="social-icon">
                                <i class="fab fa-github text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Moetaz Kheder -->
            <div class="col-md-6 col-lg-4">
                <div class="card hover-scale border-primary border-opacity-25 animate-fade-in h-100"
                     style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                    <div class="card-body p-4">
                        <div class="avatar-gradient rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width: 96px; height: 96px; font-size: 1.5rem;">
                            MK
                        </div>

                        <div class="text-center mb-3">
                            <h3 class="h5 fw-bold text-dark mb-1">Moetaz Kheder</h3>
                            <p class="text-primary fw-semibold mb-2">Designer UX/UI</p>
                            <span class="badge bg-light text-dark border mb-3">Expérience Utilisateur</span>
                        </div>

                        <p class="small text-muted text-center mb-3 lh-base">
                            Expert en conception d'interfaces intuitives et accessibles. Focalisé sur l'amélioration de l'expérience utilisateur.
                        </p>

                        <div class="d-flex flex-wrap gap-1 justify-content-center mb-3">
                            <span class="skill-badge">UI/UX</span>
                            <span class="skill-badge">Design System</span>
                            <span class="skill-badge">Figma</span>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <div class="social-icon">
                                <i class="fas fa-envelope text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                            <div class="social-icon">
                                <i class="fab fa-linkedin text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                            <div class="social-icon">
                                <i class="fab fa-github text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yosr Mekki -->
            <div class="col-md-6 col-lg-4">
                <div class="card hover-scale border-primary border-opacity-25 animate-fade-in h-100"
                     style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                    <div class="card-body p-4">
                        <div class="avatar-gradient rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width: 96px; height: 96px; font-size: 1.5rem;">
                            YM
                        </div>

                        <div class="text-center mb-3">
                            <h3 class="h5 fw-bold text-dark mb-1">Yosr Mekki</h3>
                            <p class="text-primary fw-semibold mb-2">Analyste Environnemental</p>
                            <span class="badge bg-light text-dark border mb-3">Durabilité & Impact</span>
                        </div>

                        <p class="small text-muted text-center mb-3 lh-base">
                            Spécialiste en analyse environnementale et en développement durable. Évalue l'impact écologique des projets urbains.
                        </p>

                        <div class="d-flex flex-wrap gap-1 justify-content-center mb-3">
                            <span class="skill-badge">Analyse</span>
                            <span class="skill-badge">Durabilité</span>
                            <span class="skill-badge">Impact</span>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <div class="social-icon">
                                <i class="fas fa-envelope text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                            <div class="social-icon">
                                <i class="fab fa-linkedin text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                            <div class="social-icon">
                                <i class="fab fa-github text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Khalil Mtallah -->
            <div class="col-md-6 col-lg-4">
                <div class="card hover-scale border-primary border-opacity-25 animate-fade-in h-100"
                     style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                    <div class="card-body p-4">
                        <div class="avatar-gradient rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width: 96px; height: 96px; font-size: 1.5rem;">
                            KM
                        </div>

                        <div class="text-center mb-3">
                            <h3 class="h5 fw-bold text-dark mb-1">Khalil Mtallah</h3>
                            <p class="text-primary fw-semibold mb-2">Développeur Frontend</p>
                            <span class="badge bg-light text-dark border mb-3">Interfaces Modernes</span>
                        </div>

                        <p class="small text-muted text-center mb-3 lh-base">
                            Créateur d'interfaces web modernes et responsives. Expert en technologies frontend et optimisation des performances.
                        </p>

                        <div class="d-flex flex-wrap gap-1 justify-content-center mb-3">
                            <span class="skill-badge">React</span>
                            <span class="skill-badge">TypeScript</span>
                            <span class="skill-badge">Performance</span>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <div class="social-icon">
                                <i class="fas fa-envelope text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                            <div class="social-icon">
                                <i class="fab fa-linkedin text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                            <div class="social-icon">
                                <i class="fab fa-github text-muted" style="font-size: 0.875rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Mission -->
    <section class="text-center mb-5">
        <h2 class="h2 fw-bold mb-4 text-dark">Notre Mission</h2>
        <div class="row g-4 mx-auto" style="max-width: 800px;">
            <div class="col-md-4">
                <div class="card border-primary border-opacity-25 h-100"
                     style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                    <div class="card-body pt-4 text-center">
                        <div class="mission-emoji mb-3">🌱</div>
                        <h3 class="fw-semibold mb-2 text-dark">Innovation</h3>
                        <p class="small text-muted">
                            Développer des solutions technologiques innovantes pour la végétalisation urbaine
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-primary border-opacity-25 h-100"
                     style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                    <div class="card-body pt-4 text-center">
                        <div class="mission-emoji mb-3">🤝</div>
                        <h3 class="fw-semibold mb-2 text-dark">Collaboration</h3>
                        <p class="small text-muted">
                            Faciliter la collaboration entre citoyens, associations et institutions
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-primary border-opacity-25 h-100"
                     style="background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(52, 211, 153, 0.05) 100%);">
                    <div class="card-body pt-4 text-center">
                        <div class="mission-emoji mb-3">🌍</div>
                        <h3 class="fw-semibold mb-2 text-dark">Durabilité</h3>
                        <p class="small text-muted">
                            Créer un impact positif durable sur l'environnement urbain
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Contact Section -->
    <section class="text-center py-5 rounded-4"
             style="background: linear-gradient(135deg, rgba(5, 150, 105, 0.1) 0%, rgba(255, 255, 255, 1) 50%, rgba(52, 211, 153, 0.05) 100%);">
        <h2 class="h2 fw-bold mb-3 text-dark">Travaillons Ensemble</h2>
        <p class="text-muted mb-4 mx-auto lh-base" style="max-width: 600px;">
            Vous souhaitez collaborer avec nous ou en savoir plus sur nos projets ?
            N'hésitez pas à nous contacter !
        </p>
        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <a href="mailto:contact@urbangreen.com"
               class="btn gradient-primary text-white hover-scale shadow-elegant">
                <i class="fas fa-envelope me-2"></i>
                Nous Contacter
            </a>
        </div>
    </section>

</div>

<script>
    // Add interactive animations
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

        // Observe all cards with stagger effect
        document.querySelectorAll('.animate-fade-in').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });

        // Add hover effects to social icons
        document.querySelectorAll('.social-icon').forEach(icon => {
            icon.addEventListener('mouseenter', function() {
                this.style.backgroundColor = 'rgba(16, 185, 129, 0.2)';
            });

            icon.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '#f3f4f6';
            });
        });
    });
</script>
@endsection
