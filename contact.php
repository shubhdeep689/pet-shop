<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gallery</title>
     
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- AOS (Animate On Scroll) CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">

    <script>
    AOS.init({
        duration: 1000,
        once: true
    });
</script>
</head>
<body>
       <!-- inherting navbar -->
      <div id="navbar"></div>



    <!-- Contact Section -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <!-- Section Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold text-primary mb-2">Contact Us</h2>
                <p class="lead text-muted">Get in Touch with Us</p>
            </div>

            <!-- Contact Form and Info -->
            <div class="row g-4">
                <!-- Contact Form -->
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Send us a Message</h5>
                            <form id="contactForm" novalidate>
                                <!-- Full Name -->
                                <div class="mb-3">
                                    <label for="fullName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="fullName" placeholder="Your Full Name" required>
                                    <div class="invalid-feedback">Please provide a valid full name.</div>
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" placeholder="your@email.com" required>
                                    <div class="invalid-feedback">Please provide a valid email address.</div>
                                </div>

                                <!-- Phone -->
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" placeholder="+91 XXXXXXXXXX" required>
                                    <div class="invalid-feedback">Please provide a valid phone number.</div>
                                </div>

                                <!-- Pet Type -->
                                <div class="mb-3">
                                    <label for="petType" class="form-label">Pet Type</label>
                                    <select class="form-select" id="petType" required>
                                        <option value="">Select Pet Type</option>
                                        <option value="dog">Dog</option>
                                        <option value="cat">Cat</option>
                                        <option value="bird">Bird</option>
                                        <option value="fish">Fish</option>
                                        <option value="rabbit">Rabbit</option>
                                        <option value="hamster">Hamster</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a pet type.</div>
                                </div>

                                <!-- Subject -->
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" class="form-control" id="subject" placeholder="Inquiry Subject" required>
                                    <div class="invalid-feedback">Please provide a subject.</div>
                                </div>

                                <!-- Message -->
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" rows="4" placeholder="Your message here..." required></textarea>
                                    <div class="invalid-feedback">Please provide a message.</div>
                                </div>

                                <!-- Submit & Reset Buttons -->
                                <div class="gap-2 d-flex">
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        <i class="bi bi-send"></i> Submit
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary flex-grow-1">
                                        <i class="bi bi-arrow-clockwise"></i> Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Contact Information</h5>
                            
                            <!-- Address -->
                            <div class="mb-4">
                                <h6 class="text-primary mb-2"><i class="bi bi-geo-alt"></i> Address</h6>
                                <p class="text-muted">
                                    Brar Pet Shop<br>
                                    Old Fazilka, Abohar<br>
                                    Punjab 152116
                                </p>
                            </div>

                            <!-- Phone -->
                            <div class="mb-4">
                                <h6 class="text-primary mb-2"><i class="bi bi-telephone"></i> Phone</h6>
                                <a href="tel:+919779654617" class="text-decoration-none text-dark">+91 97796 54617</a>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <h6 class="text-primary mb-2"><i class="bi bi-envelope"></i> Email</h6>
                                <a href="mailto:info@brarpetshop.com" class="text-decoration-none text-dark">info@brarpetshop.com</a>
                            </div>

                            <!-- Opening Hours -->
                            <div class="mb-4">
                                <h6 class="text-primary mb-2"><i class="bi bi-clock"></i> Opening Hours</h6>
                                <p class="text-muted mb-1">Monday - Saturday: 9:00 AM - 6:00 PM</p>
                                <p class="text-muted">Sunday: 10:00 AM - 4:00 PM</p>
                            </div>

                            <!-- Quick Links -->
                            <div>
                                <h6 class="text-primary mb-2">Follow Us</h6>
                                <div class="gap-2 d-flex flex-wrap">
                                    <a href="https://facebook.com" class="btn btn-outline-primary btn-sm" title="Facebook" data-bs-toggle="tooltip">
                                        <i class="bi bi-facebook"></i>
                                    </a>
                                    <a href="https://instagram.com" class="btn btn-outline-danger btn-sm" title="Instagram" data-bs-toggle="tooltip">
                                        <i class="bi bi-instagram"></i>
                                    </a>
                                    <a href="https://wa.me/919779654617" class="btn btn-outline-success btn-sm" title="WhatsApp" data-bs-toggle="tooltip">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                    <a href="https://youtube.com" class="btn btn-outline-danger btn-sm" title="YouTube" data-bs-toggle="tooltip">
                                        <i class="bi bi-youtube"></i>
                                    </a>
                                    <a href="https://twitter.com" class="btn btn-outline-info btn-sm" title="X (Twitter)" data-bs-toggle="tooltip">
                                        <i class="bi bi-twitter"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Google Map Section -->
    <section class="py-5">
        <div class="container-fluid">
            <div class="row g-0">
                <div class="col-12">
                    <h3 class="text-center mb-4 text-primary fw-bold">Find Us on Map</h3>
                    <!-- Google Map Embed -->
                    <div class="ratio ratio-16x9">
                       <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3450.132240939842!2d74.18577599999999!3d30.147634999999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMzDCsDA4JzUxLjUiTiA3NMKwMTEnMDguOCJF!5e0!3m2!1sen!2sin!4v1781019105421!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <div class="text-center mt-3">
                        <a href="https://maps.google.com" target="_blank" class="btn btn-primary btn-sm">
                            <i class="bi bi-geo-alt"></i> Get Directions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


          <!-- inherting footer -->
     <!--  <div id="footer"></div> -->
     <?php include 'footer.php'; ?>
 <!-- Back to Top Button -->
    <button id="backToTopBtn" class="btn btn-primary rounded-circle position-fixed" title="Back to top">
        <i class="bi bi-arrow-up"></i>
    </button>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS (Animate On Scroll) JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom Script -->
    <script src="script.js"></script>
</body>
</html>