<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>sQRypt</title>
        <link href="{{ secure_asset('assets/css/welcome.css') }}" rel="stylesheet">
        <link href="{{ secure_asset('assets/bootstrap-5.3.3-dist/css/bootstrap.css') }}" rel="stylesheet">
        <link href="{{ secure_asset('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css') }}" rel="stylesheet">
    </head>
    <body>
        <header class="sticky-top">
            @if (Route::has('login'))
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid">
                    <a class="navbar-brand" href="/">
                        <img src="{{ asset('assets/logo.png') }}" alt="Logo" style="height: 40px;">
                        <p>sQRypt</p>
                      </a>
                  <button class="navbar-toggler" type="button" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-list toggle-icon"></i>
                  </button>
                  <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mx-auto text-center">
                        <li class="nav-item">
                            <a class="nav-link" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Upcoming Events</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact Us</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto auth-buttons">
                        @if (Route::has('login'))
                            @auth
                                <li class="nav-item">
                                    <a href="{{ url('/dashboard') }}" class="nav-btn btn-dashboard">
                                        <i class="bi bi-speedometer2"></i>
                                        Dashboard
                                    </a>
                                </li>
                            @else
                                <li class="nav-item me-2">
                                    <a href="{{ route('login') }}" class="nav-btn btn-login">
                                        Login
                                    </a>
                                </li>
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a href="{{ route('register') }}" class="nav-btn btn-register">
                                            Register
                                        </a>
                                    </li>
                                @endif
                            @endauth
                        @endif
                    </ul>
                  </div>
                </div>
              </nav>
            @endif
        </header>

        <!-- Hero Section -->
        <section class="hero-section py-5">
            <div class="floating-elements"></div>
            <div class="vector-effects">
                <div class="wave"></div>
                <div class="wave"></div>
                <div class="wave"></div>
            </div>
            <div class="particles">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
            </div>
            <div class="vector-decoration"></div>
            <div class="container">
                <div class="row align-items-center hero-content">
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <h1 class="hero-title mb-4">
                            Streamline Event Attendance with QR Technology
                        </h1>
                        <p class="hero-subtitle">
                            Transform your event management with our innovative QR-based attendance system. 
                            Track attendance effortlessly, generate insightful reports, and focus on what matters most.
                        </p>
                        <div class="hero-btns">
                            <a href="{{ route('register') }}"><button class="hero-btn btn-get-started">
                                Get Started
                                <i class="bi bi-arrow-right"></i>
                            </button></a>
                            <button class="hero-btn btn-learn-more">
                                Learn More
                                <i class="bi bi-info-circle"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="feature-card">
                                    <div class="feature-icon-container">
                                        <div class="feature-icon qr-scan">
                                            <i class="bi bi-qr-code"></i>
                                        </div>
                                    </div>
                                    <h3 class="h5 text-white">Quick Scanning</h3>
                                    <p class="text-light-blue mb-0">Instant attendance tracking with QR code technology</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-card">
                                    <div class="feature-icon-container">
                                        <div class="feature-icon analytics">
                                            <i class="bi bi-graph-up"></i>
                                        </div>
                                    </div>
                                    <h3 class="h5 text-white">Real-time Analytics</h3>
                                    <p class="text-light-blue mb-0">Monitor attendance patterns and generate reports</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-card">
                                    <div class="feature-icon-container">
                                        <div class="feature-icon event">
                                            <i class="bi bi-calendar-check"></i>
                                        </div>
                                    </div>
                                    <h3 class="h5 text-white">Event Management</h3>
                                    <p class="text-light-blue mb-0">Organize and track multiple events effortlessly</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-card">
                                    <div class="feature-icon-container">
                                        <div class="feature-icon mobile">
                                            <i class="bi bi-phone"></i>
                                        </div>
                                    </div>
                                    <h3 class="h5 text-white">Mobile Friendly</h3>
                                    <p class="text-light-blue mb-0">Access and manage events from any device</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

                <!-- About Section with How it Works -->
                <section class="about-section py-5" id="about">
                    <div class="container">
                        <div class="row align-items-center mb-5">
                            <div class="col-lg-6 mb-5 mb-lg-0">
                                <img src="{{ asset('assets/welcombg.jpg') }}" alt="QR Code Scanning" class="img-fluid rounded-4 about-image" style="max-width: 80%; margin: 0 auto; display: block;">
                            </div>
                            <div class="col-lg-6">
                                <div class="about-content">
                                    <h2 class="section-title mb-4">What is sQRypt?</h2>
                                    <p class="section-description mb-4">
                                        sQRypt is more than just an attendance system - it's a comprehensive solution designed to transform how you manage events and track attendance. Our platform combines cutting-edge QR technology with intuitive features to create a seamless experience for event organizers and attendees alike.
                                    </p>
                                    <div class="about-features">
                                        <div class="about-feature mb-3">
                                            <i class="bi bi-check-circle-fill text-primary"></i>
                                            <span>Instant QR Code Generation</span>
                                        </div>
                                        <div class="about-feature mb-3">
                                            <i class="bi bi-check-circle-fill text-primary"></i>
                                            <span>Real-time Attendance Tracking</span>
                                        </div>
                                        <div class="about-feature mb-3">
                                            <i class="bi bi-check-circle-fill text-primary"></i>
                                            <span>Comprehensive Analytics Dashboard</span>
                                        </div>
                                        <div class="about-feature">
                                            <i class="bi bi-check-circle-fill text-primary"></i>
                                            <span>Multi-event Management</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- How it Works Section -->
                        <div class="how-it-works mt-5">
                            <h3 class="text-center mb-5">How it Works</h3>
                            <div class="row">
                                <!-- For Event Planners -->
                                <div class="col-lg-6 mb-4">
                                    <div class="user-guide p-4 h-100 rounded-4 bg-light">
                                        <h4 class="mb-4"><i class="bi bi-person-gear me-2"></i>For Event Planners</h4>
                                        <div class="step-list">
                                            <div class="step mb-3">
                                                <span class="step-number">1</span>
                                                <div class="step-content">
                                                    <h5>Create Event</h5>
                                                    <p>Set up your event details and customize attendance settings</p>
                                                </div>
                                            </div>
                                            <div class="step mb-3">
                                                <span class="step-number">2</span>
                                                <div class="step-content">
                                                    <h5>Generate QR Codes</h5>
                                                    <p>Create unique QR codes for your attendees</p>
                                                </div>
                                            </div>
                                            <div class="step mb-3">
                                                <span class="step-number">3</span>
                                                <div class="step-content">
                                                    <h5>Monitor Attendance</h5>
                                                    <p>Track real-time attendance and generate reports</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- For Attendees -->
                                <div class="col-lg-6 mb-4">
                                    <div class="user-guide p-4 h-100 rounded-4 bg-light">
                                        <h4 class="mb-4"><i class="bi bi-person me-2"></i>For Attendees</h4>
                                        <div class="step-list">
                                            <div class="step mb-3">
                                                <span class="step-number">1</span>
                                                <div class="step-content">
                                                    <h5>Receive QR Code</h5>
                                                    <p>Get your unique QR code from the event organizer</p>
                                                </div>
                                            </div>
                                            <div class="step mb-3">
                                                <span class="step-number">2</span>
                                                <div class="step-content">
                                                    <h5>Show at Event</h5>
                                                    <p>Present your QR code at the event entrance</p>
                                                </div>
                                            </div>
                                            <div class="step mb-3">
                                                <span class="step-number">3</span>
                                                <div class="step-content">
                                                    <h5>Quick Check-in</h5>
                                                    <p>Get instant verification and entry to the event</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Events Calendar Section -->
                <section class="calendar-section py-5 min-vh-100 d-flex align-items-center" id="features">
                    <div class="container flex-grow-1">
                        <h2 class="section-title text-center mb-4">Upcoming Events</h2>
                        <div class="calendar-container">
                            <div class="calendar-header d-flex justify-content-between align-items-center mb-4">
                                <button class="btn btn-outline-primary" id="prevMonth"><i class="bi bi-chevron-left"></i></button>
                                <h3 class="calendar-title mb-0" id="currentMonth">December 2024</h3>
                                <button class="btn btn-outline-primary" id="nextMonth"><i class="bi bi-chevron-right"></i></button>
                            </div>
                            <div class="calendar-grid">
                                <div class="calendar-weekdays">
                                    <div class="calendar-cell"><span>Sun</span></div>
                                    <div class="calendar-cell"><span>Mon</span></div>
                                    <div class="calendar-cell"><span>Tue</span></div>
                                    <div class="calendar-cell"><span>Wed</span></div>
                                    <div class="calendar-cell"><span>Thu</span></div>
                                    <div class="calendar-cell"><span>Fri</span></div>
                                    <div class="calendar-cell"><span>Sat</span></div>
                                </div>
                                <div class="calendar-days" id="calendarDays">
                                    <!-- Calendar days will be populated by JavaScript -->
                                </div>
                            </div>
                            <div class="event-list mt-4">
                                <h4 class="mb-3">Today's Events</h4>
                                <div class="event-items" id="todayEvents">
                                    <!-- Event items will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Contact Section -->
                <section class="contact-section py-5" id="contact">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-8 text-center mb-5">
                                <h2 class="section-title">Get in Touch</h2>
                                <p class="section-subtitle">Have questions? We'd love to hear from you.</p>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="contact-box">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="contact-info">
                                                <i class="bi bi-envelope"></i>
                                                <h4>Email</h4>
                                                <p>finfawnfoe@gmail.com</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="contact-info">
                                                <i class="bi bi-telephone"></i>
                                                <h4>Phone</h4>
                                                <p>09073448862</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
        
                <!-- Footer -->
                <footer class="footer">
                    <div class="footer-wave">
                        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
                        </svg>
                    </div>
                    <div class="container">
                        <div class="row">
                            <!-- About Column -->
                            <div class="col-md-4 footer-col">
                                <h4>About sQRypt</h4>
                                <p class="mb-4">Revolutionizing event attendance management with QR code technology. Making check-ins seamless and efficient for both organizers and attendees.</p>
                                <div class="footer-social">
                                    <a href="#"><i class="bi bi-facebook"></i></a>
                                    <a href="#"><i class="bi bi-twitter"></i></a>
                                    <a href="#"><i class="bi bi-linkedin"></i></a>
                                    <a href="#"><i class="bi bi-instagram"></i></a>
                                </div>
                            </div>
                            
                            <!-- Quick Links Column -->
                            <div class="col-md-4 footer-col">
                                <h4>Quick Links</h4>
                                <ul class="footer-links">
                                    <li><a href="#home"><i class="bi bi-chevron-right"></i>Home</a></li>
                                    <li><a href="#about"><i class="bi bi-chevron-right"></i>About Us</a></li>
                                    <li><a href="#events"><i class="bi bi-chevron-right"></i>Events</a></li>
                                    <li><a href="#contact"><i class="bi bi-chevron-right"></i>Contact</a></li>
                                    <li><a href="{{ route('login') }}"><i class="bi bi-chevron-right"></i>Login</a></li>
                                    <li><a href="{{ route('register') }}"><i class="bi bi-chevron-right"></i>Register</a></li>
                                </ul>
                            </div>
                            
                            <!-- Contact Column -->
                            <div class="col-md-4 footer-col">
                                <h4>Contact Info</h4>
                                <div class="footer-contact">
                                    <p><i class="bi bi-envelope"></i>finfawnfoe@gmail.com</p>
                                    <p><i class="bi bi-telephone"></i>09073448862</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Footer Bottom -->
                        <div class="footer-bottom">
                            <p>&copy; {{ date('Y') }} sQRypt. All rights reserved. | Designed with <i class="bi bi-heart-fill text-danger"></i> by <a href="#">Me</a></p>
                        </div>
                    </div>
                </footer>
        
                <script src="{{ secure_asset('assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ secure_asset('assets/bootstrap-5.3.3-dist/js/bootstrap.js') }}"></script>
        <script src="{{ secure_asset('assets/js/welcome.js') }}"></script>
        <script src="{{ secure_asset('assets/js/calendar.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Navbar toggler functionality
                const navbarToggler = document.querySelector('.navbar-toggler');
                const navbarCollapse = document.getElementById('navbarNav');
                const toggleIcon = document.querySelector('.toggle-icon');

                // Function to expand navbar
                function expandNavbar() {
                    toggleIcon.classList.remove('bi-list');
                    toggleIcon.classList.add('bi-x-lg');
                    navbarCollapse.classList.add('show');
                    navbarToggler.classList.remove('collapsed');
                    navbarToggler.setAttribute('aria-expanded', 'true');
                }

                // Function to collapse navbar
                function collapseNavbar() {
                    toggleIcon.classList.remove('bi-x-lg');
                    toggleIcon.classList.add('bi-list');
                    navbarCollapse.classList.remove('show');
                    navbarToggler.classList.add('collapsed');
                    navbarToggler.setAttribute('aria-expanded', 'false');
                }

                // Toggle navbar and icon
                navbarToggler.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isExpanded = navbarToggler.getAttribute('aria-expanded') === 'true';
                    if (isExpanded) {
                        collapseNavbar();
                    } else {
                        expandNavbar();
                    }
                });

                // Close navbar when clicking outside
                document.addEventListener('click', function(event) {
                    const isClickInside = navbarCollapse.contains(event.target) || navbarToggler.contains(event.target);
                    if (!isClickInside && navbarCollapse.classList.contains('show')) {
                        collapseNavbar();
                    }
                });

                // Close navbar when clicking on a nav link (for mobile)
                document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 992) { // Bootstrap's lg breakpoint
                            collapseNavbar();
                        }
                    });
                });

                // Smooth scroll with offset for navigation links
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function (e) {
                        e.preventDefault();
                        const targetId = this.getAttribute('href');
                        
                        // Special handling for home link
                        if (targetId === '#home' || targetId === '#') {
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                            return;
                        }
                        
                        const targetElement = document.querySelector(targetId);
                        if (targetElement) {
                            const offset = 80;
                            const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - offset;
                            
                            window.scrollTo({
                                top: targetPosition,
                                behavior: 'smooth'
                            });
                        }
                    });
                });

                // Update active navigation link based on scroll position
                window.addEventListener('scroll', function() {
                    const sections = document.querySelectorAll('section[id]');
                    const navLinks = document.querySelectorAll('.navbar-nav a[href^="#"]');
                    const scrollPosition = window.pageYOffset;
                    
                    // Special handling for home/top of page
                    if (scrollPosition < 100) {
                        navLinks.forEach(link => link.classList.remove('active'));
                        const homeLink = document.querySelector('a[href="#home"]');
                        if (homeLink) homeLink.classList.add('active');
                        return;
                    }
                    
                    let currentSection = '';
                    const offset = 100;

                    sections.forEach(section => {
                        const sectionTop = section.offsetTop - offset;
                        const sectionHeight = section.offsetHeight;
                        if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                            currentSection = '#' + section.getAttribute('id');
                        }
                    });

                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === currentSection) {
                            link.classList.add('active');
                        }
                    });
                });
            });
        </script>
        <script>
            document.querySelectorAll('.nav-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    let x = e.clientX - e.target.offsetLeft;
                    let y = e.clientY - e.target.offsetTop;
                    
                    let ripple = document.createElement('span');
                    ripple.className = 'ripple';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        </script>
        <script>
            // Intersection Observer for fade-in animations
            document.addEventListener('DOMContentLoaded', function() {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                        }
                    });
                }, {
                    threshold: 0.1
                });

                document.querySelectorAll('.fade-in').forEach((element) => {
                    observer.observe(element);
                });
            });
        </script>
    </body>
</html>
