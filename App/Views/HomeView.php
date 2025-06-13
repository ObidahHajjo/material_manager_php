<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-boxes-stacked"></i>
            Material Manager
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#features">Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">About</a>
                </li>
            </ul>

            <div class="navbar-nav">
                <a href="/login" class="btn btn-login">Login</a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section id="home" class="hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center hero-content">
                <h1>Material Manager</h1>
                <p>Transform your inventory management with our comprehensive material tracking and management system. Streamline operations, reduce costs, and improve efficiency.</p>

                <div class="hero-buttons">
                    <a href="#features" class="btn btn-hero btn-hero-secondary">
                        <i class="fas fa-info-circle me-2"></i>
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="features">
    <div class="container">
        <div class="section-title">
            <h2>Why Choose Material Manager?</h2>
            <p>Our platform offers comprehensive tools to manage your materials efficiently, from inventory tracking to automated reporting.</p>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <h3>Inventory Tracking</h3>
                    <p>Real-time tracking of all materials with detailed analytics, stock levels, and automated alerts for low inventory.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Analytics & Reports</h3>
                    <p>Generate comprehensive reports on material usage, costs, and trends to make informed business decisions.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Team Collaboration</h3>
                    <p>Multiple user roles and permissions system allowing teams to collaborate efficiently on material management.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Smart Notifications</h3>
                    <p>Automated alerts for low stock, expiring materials, and important updates to keep your operations running smoothly.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Friendly</h3>
                    <p>Access your materials database from anywhere with our responsive design that works on all devices.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure & Reliable</h3>
                    <p>Enterprise-grade security with regular backups and data protection to keep your information safe.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about">
    <div class="container">
        <div class="row about-content">
            <div class="col-lg-6 about-text">
                <h2>About Material Manager</h2>
                <p>Material Manager is a comprehensive inventory management system designed to help businesses of all sizes efficiently track, manage, and optimize their material resources.</p>

                <p>Our platform combines powerful functionality with an intuitive interface, making it easy for teams to collaborate on material management tasks while maintaining complete visibility over inventory levels, costs, and usage patterns.</p>

                <p>Whether you're managing construction materials, office supplies, or manufacturing components, Material Manager provides the tools you need to streamline your operations and reduce costs.</p>
            </div>

            <div class="col-lg-6 about-image">
                <div class="about-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <h2>Ready to Get Started?</h2>
        <p>Join thousands of businesses already using Material Manager to optimize their inventory management.</p>

        <div class="hero-buttons">
            <a href="login.html" class="btn btn-hero btn-hero-secondary">
                <i class="fas fa-sign-in-alt me-2"></i>
                Sign In
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<div class="footer">
    <div class="container">
        <p>&copy; 2025 Material Manager. All rights reserved. | Designed for efficient material management.</p>
    </div>
</div>

<script>
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe feature cards
    document.querySelectorAll('.feature-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });

    // Navbar background on scroll
    window.addEventListener('scroll', () => {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            navbar.style.backdropFilter = 'blur(10px)';
        } else {
            navbar.style.background = 'white';
            navbar.style.backdropFilter = 'none';
        }
    });
</script>