<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery – Brar Pet Shop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        /* Gallery item hover overlay */
        .gallery-item img {
            height: 260px;
            object-fit: cover;
            width: 100%;
            display: block;
            transition: transform 0.4s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.50);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }

        /* Lightbox modal image */
        #galleryModalImage {
            max-height: 75vh;
            object-fit: contain;
        }

        /* Caption bar inside modal */
        .modal-caption {
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            color: #555;
            text-align: center;
        }
    </style>
</head>
<body>

      <!-- inherting navbar -->
     <!--  <div id="navbar"></div> -->
    <?php include 'navbar.php'; ?>

    <!-- Gallery Section -->
    <section id="gallery" class="py-5">
        <div class="container">

            <!-- Section Header -->
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-5 fw-bold text-primary mb-2">Gallery</h2>
                <p class="lead text-muted">Moments from Brar Pet Shop</p>
            </div>

            <!-- Gallery Grid -->
            <div class="row g-3">

                <!-- Item 1: Dogs -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="gallery-item position-relative overflow-hidden rounded shadow" role="group" aria-label="Dogs">
                        <img src="IMAGES/AMERICAN_BULLY.jpeg" alt="A dog at Brar Pet Shop">
                        <div class="gallery-overlay">
                            <h6 class="text-white mb-2">Dogs</h6>
                            <button type="button"
                                class="btn btn-light btn-sm view-btn"
                                data-image="IMAGES/AMERICAN_BULLY.jpeg"
                                data-caption="Dogs"
                                data-bs-toggle="modal"
                                data-bs-target="#galleryModal">
                                <i class="bi bi-zoom-in"></i> View
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Item 2: Cats -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="150">
                    <div class="gallery-item position-relative overflow-hidden rounded shadow" role="group" aria-label="Cats">
                        <img src="IMAGES/cat.jpeg" alt="A cat at Brar Pet Shop">
                        <div class="gallery-overlay">
                            <h6 class="text-white mb-2">Cats</h6>
                            <button type="button"
                                class="btn btn-light btn-sm view-btn"
                                data-image="IMAGES/cat.jpeg"
                                data-caption="Cats"
                                data-bs-toggle="modal"
                                data-bs-target="#galleryModal">
                                <i class="bi bi-zoom-in"></i> View
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Item 3: Birds -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="gallery-item position-relative overflow-hidden rounded shadow" role="group" aria-label="Birds">
                        <img src="IMAGES/grey-parrot-on-branch-free-photo.jpg" alt="A bird at Brar Pet Shop">
                        <div class="gallery-overlay">
                            <h6 class="text-white mb-2">Birds</h6>
                            <button type="button"
                                class="btn btn-light btn-sm view-btn"
                                data-image="IMAGES/grey-parrot-on-branch-free-photo.jpg"
                                data-caption="Birds"
                                data-bs-toggle="modal"
                                data-bs-target="#galleryModal">
                                <i class="bi bi-zoom-in"></i> View
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Item 4: Grooming -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="250">
                    <div class="gallery-item position-relative overflow-hidden rounded shadow" role="group" aria-label="Grooming">
                        <img src="IMAGES/groom.jpg" alt="Grooming service at Brar Pet Shop">
                        <div class="gallery-overlay">
                            <h6 class="text-white mb-2">Grooming</h6>
                            <button type="button"
                                class="btn btn-light btn-sm view-btn"
                                data-image="IMAGES/groom.jpg"
                                data-caption="Grooming"
                                data-bs-toggle="modal"
                                data-bs-target="#galleryModal">
                                <i class="bi bi-zoom-in"></i> View
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Item 5: Store Interior -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="gallery-item position-relative overflow-hidden rounded shadow" role="group" aria-label="Store Interior">
                        <img src="IMAGES/inerior.jpg" alt="Store interior of Brar Pet Shop">
                        <div class="gallery-overlay">
                            <h6 class="text-white mb-2">Store Interior</h6>
                            <button type="button"
                                class="btn btn-light btn-sm view-btn"
                                data-image="IMAGES/inerior.jpg"
                                data-caption="Store Interior"
                                data-bs-toggle="modal"
                                data-bs-target="#galleryModal">
                                <i class="bi bi-zoom-in"></i> View
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Item 6: Accessories -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="350">
                    <div class="gallery-item position-relative overflow-hidden rounded shadow" role="group" aria-label="Accessories">
                        <img src="IMAGES/ACESSORIES.png" alt="Pet accessories at Brar Pet Shop">
                        <div class="gallery-overlay">
                            <h6 class="text-white mb-2">Accessories</h6>
                            <button type="button"
                                class="btn btn-light btn-sm view-btn"
                                data-image="IMAGES/ACESSORIES.png"
                                data-caption="Accessories"
                                data-bs-toggle="modal"
                                data-bs-target="#galleryModal">
                                <i class="bi bi-zoom-in"></i> View
                            </button>
                        </div>
                    </div>
                </div>

            </div><!-- /.row -->
        </div><!-- /.container -->
    </section>

    <!-- ===== Gallery Lightbox Modal ===== -->
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">

                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-semibold" id="galleryModalLabel"></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-2 text-center">
                    <img id="galleryModalImage" src="" alt="" class="img-fluid rounded">
                </div>

                <div class="modal-caption" id="galleryModalCaption"></div>

            </div>
        </div>
    </div>

     <!-- inherting footer -->
     <!--  <div id="footer"></div> -->
     <?php include 'footer.php'; ?>

    <!-- Back to Top Button -->
    <button id="backToTopBtn" class="btn btn-primary rounded-circle position-fixed" title="Back to top" style="bottom: 2rem; right: 2rem; display: none; z-index: 999;">
        <i class="bi bi-arrow-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // ── AOS init (must come AFTER the AOS script tag)
        AOS.init({ duration: 1000, once: true });

        // ── Gallery modal: populate image + caption when a View button is clicked
        const galleryModal = document.getElementById('galleryModal');
        galleryModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;                       // the button that triggered the modal
            const imgSrc = btn.getAttribute('data-image');
            const caption = btn.getAttribute('data-caption') || '';

            document.getElementById('galleryModalImage').src = imgSrc;
            document.getElementById('galleryModalImage').alt = caption;
            document.getElementById('galleryModalLabel').textContent = caption;
            document.getElementById('galleryModalCaption').textContent = caption;
        });

        // ── Clear image src when modal closes (prevents flash of old image on next open)
        galleryModal.addEventListener('hidden.bs.modal', function () {
            document.getElementById('galleryModalImage').src = '';
        });

        // ── Back-to-top button
        const backToTopBtn = document.getElementById('backToTopBtn');
        window.addEventListener('scroll', () => {
            backToTopBtn.style.display = window.scrollY > 300 ? 'block' : 'none';
        });
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>

    <!-- Inherited navbar + footer loader (keep your existing script.js) -->
    <script src="script.js"></script>
</body>
</html>
