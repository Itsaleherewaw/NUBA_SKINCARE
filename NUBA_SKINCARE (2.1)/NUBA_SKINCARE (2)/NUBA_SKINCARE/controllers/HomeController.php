<?php
class HomeController {
    public function index() {
        $this->showTempHome();
    }
    
    private function showTempHome() {
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>NUBA Skincare - Cuidado Natural para tu Piel</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                :root {
                    --primary-color: #801458ff;
                    --secondary-color: #db1d82ff;
                    --accent-color: #f8e8f3;
                    --text-dark: #c52286ff;
                    --text-light: #a3aaafff;
                }
                
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #f9f5f7 0%, #f0e6ed 100%);
                    color: var(--text-dark);
                }
                
                .hero-section {
                    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                    color: white;
                    padding: 80px 0;
                    position: relative;
                    overflow: hidden;
                }
                
                .hero-section::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="%23ffffff20"><path d="M500 50L550 0L600 50L650 0L700 50L750 0L800 50L850 0L900 50L950 0L1000 50L1000 100L0 100L0 50Z"/></svg>');
                    background-size: cover;
                    background-position: bottom;
                }
                
                .logo {
                    font-size: 2.5rem;
                    font-weight: bold;
                    background: linear-gradient(135deg, #fff, #f0f0f0);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    text-shadow: 2px 2px 4px rgba(179, 59, 59, 0.1);
                }
                
                .nav-custom {
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(10px);
                    box-shadow: 0 2px 20px rgba(155, 56, 118, 0.1);
                }
                
                .btn-nuba {
                    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                    color: white;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 25px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                    box-shadow: 0 4px 15px rgba(155, 56, 118, 0.3);
                }
                
                .btn-nuba:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(155, 56, 118, 0.4);
                    color: white;
                }
                
                .btn-outline-nuba {
                    border: 2px solid var(--primary-color);
                    color: var(--primary-color);
                    background: transparent;
                    padding: 10px 28px;
                    border-radius: 25px;
                    font-weight: 600;
                    transition: all 0.3s ease;
                }
                
                .btn-outline-nuba:hover {
                    background: var(--primary-color);
                    color: white;
                    transform: translateY(-2px);
                }
                
                .feature-card {
                    background: white;
                    border-radius: 15px;
                    padding: 30px;
                    text-align: center;
                    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
                    transition: all 0.3s ease;
                    border: 1px solid rgba(155, 56, 118, 0.1);
                    height: 100%;
                }
                
                .feature-card:hover {
                    transform: translateY(-10px);
                    box-shadow: 0 15px 35px rgba(155, 56, 118, 0.15);
                }
                
                .feature-icon {
                    width: 80px;
                    height: 80px;
                    background: linear-gradient(135deg, var(--accent-color), #f0d4e4);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 20px;
                    font-size: 2rem;
                    color: var(--primary-color);
                }
                
                .carousel-custom {
                    border-radius: 20px;
                    overflow: hidden;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                }
                
                .carousel-item {
                    height: 500px;
                    position: relative;
                }
                
                .carousel-item img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
                
                .carousel-caption-custom {
                    background: rgba(155, 56, 118, 0.85);
                    backdrop-filter: blur(10px);
                    border-radius: 15px;
                    padding: 30px;
                    bottom: 50px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 80%;
                    max-width: 600px;
                    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
                }
                
                .carousel-indicators button {
                    width: 12px;
                    height: 12px;
                    border-radius: 50%;
                    margin: 0 5px;
                }
                
                .testimonial-card {
                    background: white;
                    border-radius: 15px;
                    padding: 30px;
                    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
                    border-left: 4px solid var(--primary-color);
                }
                
                .user-avatar {
                    width: 60px;
                    height: 60px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-weight: bold;
                    font-size: 1.5rem;
                }
                
                .stats-section {
                    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                    color: white;
                    padding: 60px 0;
                }
                
                .stat-number {
                    font-size: 3rem;
                    font-weight: bold;
                    margin-bottom: 10px;
                }
            </style>
        </head>
        <body>

            <nav class="navbar navbar-expand-lg navbar-light nav-custom fixed-top">
                <div class="container">
                    <a class="navbar-brand logo" href="#">
                        <i class="fas fa-spa me-2"></i>NUBA
                    </a>
                    <div class="navbar-nav ms-auto">
                        <a href="/NUBA_SKINCARE/public/auth/login" class="btn btn-outline-nuba me-2">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </a>
                        <a href="/NUBA_SKINCARE/public/auth/register" class="btn btn-nuba">
                            <i class="fas fa-user-plus me-2"></i>Registrarse
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <section class="hero-section">
                <div class="container position-relative">
                    <div class="row align-items-center min-vh-100">
                        <div class="col-lg-6">
                            <h1 class="display-4 fw-bold mb-4">
                                Descubre tu <span class="text-warning">Belleza Natural</span> con NUBA
                            </h1>
                            <p class="lead mb-4">
                                Productos de skincare 100% naturales diseñados para realzar tu belleza única. 
                                Cuidamos tu piel con ingredientes premium y fórmulas innovadoras.
                            </p>
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="/NUBA_SKINCARE/public/products" class="btn btn-nuba btn-lg">
                                    <i class="fas fa-store me-2"></i>Ver Productos
                                </a>
                                <a href="#features" class="btn btn-outline-light btn-lg">
                                    <i class="fas fa-play-circle me-2"></i>Conocer Más
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center">
                            <div class="hero-image">
                                <i class="fas fa-spa display-1 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Carrusel  -->
            <section class="py-5" id="carousel">
                <div class="container">
                    <div class="row mb-5">
                        <div class="col-12 text-center">
                            <h2 class="fw-bold mb-3">Nuestra Colección Exclusiva</h2>
                            <p class="lead text-muted">Descubre los productos más vendidos y novedades de temporada</p>
                        </div>
                    </div>
                    
                    <div id="productCarousel" class="carousel slide carousel-custom" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="0" class="active"></button>
                            <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="1"></button>
                            <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="2"></button>
                            <button type="button" data-bs-target="#productCarousel" data-bs-slide-to="3"></button>
                        </div>
                        <div class="carousel-inner">
                            <!-- Slide 1 - Serum -->
                            <div class="carousel-item active">
                                <img src="https://farmacityar.vtexassets.com/arquivos/ids/253070/234312_kit-tratamiento-completo-triple-serum-loreal-paris-serum-retinol-serum-acido-hialuronico-y-serum-ojos_imagen--3.jpg?v=638460183286100000" 
                                     class="d-block w-100" 
                                     alt="Serum Revitalizante NUBA">
                                <div class="carousel-caption carousel-caption-custom">
                                    <h3>Serum Revitalizante</h3>
                                    <p>Hidratación profunda con ácido hialurónico y vitaminas naturales</p>
                                    <span class="badge bg-warning text-dark fs-6">Más Vendido</span>
                                </div>
                            </div>
                            
                            <!-- Slide 2 - Crema Nocturna -->
                            <div class="carousel-item">
                                <img src="https://tse3.mm.bing.net/th/id/OIP.BYxj02yS-lO2JYjI_FGsYwHaHa?rs=1&pid=ImgDetMain&o=7&rm=3" 
                                     class="d-block w-100" 
                                     alt="Crema Nocturna Reparadora">
                                <div class="carousel-caption carousel-caption-custom">
                                    <h3>Crema Nocturna Reparadora</h3>
                                    <p>Reparación intensiva mientras descansas con extractos de lavanda</p>
                                    <span class="badge bg-success fs-6">Nueva Línea</span>
                                </div>
                            </div>
                            
                            <!-- Slide 3 - Protector Solar -->
                            <div class="carousel-item">
                                <img src="https://tse3.mm.bing.net/th/id/OIP.cPlSO1aJJNCcR_fowGGmzgHaHa?rs=1&pid=ImgDetMain&o=7&rm=3" 
                                     class="d-block w-100" 
                                     alt="Protector Solar Natural">
                                <div class="carousel-caption carousel-caption-custom">
                                    <h3>Protector Solar Natural</h3>
                                    <p>Protección SPF 50+ con filtros minerales y aloe vera orgánico</p>
                                    <span class="badge bg-info fs-6">Esencial</span>
                                </div>
                            </div>
                            
                            <!-- Slide 4 - Kit Completo -->
                            <div class="carousel-item">
                                <img src="https://tse3.mm.bing.net/th/id/OIP.Q1WQXAz2vdq0Wqb-Huz4IwHaHa?rs=1&pid=ImgDetMain&o=7&rm=3" 
                                     class="d-block w-100" 
                                     alt="Kit Completo de Skincare">
                                <div class="carousel-caption carousel-caption-custom">
                                    <h3>Kit Completo de Skincare</h3>
                                    <p>Todo lo que necesitas para tu rutina diaria de cuidado facial</p>
                                    <span class="badge bg-primary fs-6">Oferta Especial</span>
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="py-5 bg-light" id="features">
                <div class="container">
                    <div class="row mb-5">
                        <div class="col-12 text-center">
                            <h2 class="fw-bold mb-3">¿Por Qué Elegir NUBA?</h2>
                            <p class="lead text-muted">Comprometidos con tu belleza y bienestar</p>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-leaf"></i>
                                </div>
                                <h4>100% Natural</h4>
                                <p class="text-muted">Ingredientes orgánicos y libres de químicos agresivos para el cuidado más puro de tu piel.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-award"></i>
                                </div>
                                <h4>Calidad Premium</h4>
                                <p class="text-muted">Productos testados dermatológicamente con los más altos estándares de calidad.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                                <h4>Envío Rápido</h4>
                                <p class="text-muted">Recibe tus productos en 24-48 horas con nuestro servicio de envío express.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Testimonios -->
            <section class="py-5">
                <div class="container">
                    <div class="row mb-5">
                        <div class="col-12 text-center">
                            <h2 class="fw-bold mb-3">Lo Que Dicen Nuestros Clientes</h2>
                            <p class="lead text-muted">Experiencias reales con nuestros productos</p>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="user-avatar me-3">
                                        M
                                    </div>
                                    <div>
                                        <h6 class="mb-0">María González</h6>
                                        <small class="text-muted">Cliente desde 2024</small>
                                    </div>
                                </div>
                                <p class="mb-0">"El serum revitalizante cambió completamente mi piel. Después de un mes de uso, mi cutis está más luminoso e hidratado."</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="user-avatar me-3">
                                        C
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Carlos Rodríguez</h6>
                                        <small class="text-muted">Cliente frecuente</small>
                                    </div>
                                </div>
                                <p class="mb-0">"La crema nocturna es increíble. Me despierto con la piel suave y renovada. ¡Totalmente recomendada!"</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="testimonial-card">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="user-avatar me-3">
                                        A
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Ana Martínez</h6>
                                        <small class="text-muted">Nueva cliente</small>
                                    </div>
                                </div>
                                <p class="mb-0">"El servicio al cliente es excepcional y los productos llegaron en perfecto estado. Volveré a comprar sin duda."</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Stats Section -->
            <section class="stats-section">
                <div class="container">
                    <div class="row text-center">
                        <div class="col-md-3 mb-4">
                            <div class="stat-number">500+</div>
                            <p class="mb-0">Clientes Satisfechos</p>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="stat-number">50+</div>
                            <p class="mb-0">Productos Naturales</p>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="stat-number">98%</div>
                            <p class="mb-0">Tasa de Satisfacción</p>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="stat-number">24h</div>
                            <p class="mb-0">Envío Express</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Quick Links -->
            <section class="py-5 bg-light">
                <div class="container">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="fw-bold mb-4">Comienza tu Experiencia NUBA</h3>
                            <div class="d-flex justify-content-center gap-3 flex-wrap">
                                <a href="/NUBA_SKINCARE/public/auth/login" class="btn btn-outline-nuba">
                                    <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                </a>
                                <a href="/NUBA_SKINCARE/public/auth/register" class="btn btn-nuba">
                                    <i class="fas fa-user-plus me-2"></i>Registrarse
                                </a>
                                <a href="/NUBA_SKINCARE/public/products" class="btn btn-outline-nuba">
                                    <i class="fas fa-store me-2"></i>Ver Catálogo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="bg-dark text-light py-4">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="logo mb-3">
                                <i class="fas fa-spa me-2"></i>NUBA Skincare
                            </h5>
                            <p class="mb-0">Tu tienda de confianza para el cuidado natural de la piel</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0">&copy; 2025 NUBA Skincare. Todos los derechos reservados. MCA</p>
                            <p class="small text-muted mt-2"></p>
                        </div>
                    </div>
                </div>
            </footer>

            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            
            <!-- Chatbot -->
            <script src="/NUBA_SKINCARE/public/assets/js/chatbot.js"></script>
            <link rel="stylesheet" href="/NUBA_SKINCARE/public/assets/css/chatbot.css">

            <script>
                // Inicializar el carrusel
                document.addEventListener('DOMContentLoaded', function() {
                    var myCarousel = document.querySelector('#productCarousel');
                    var carousel = new bootstrap.Carousel(myCarousel, {
                        interval: 4000,
                        wrap: true
                    });
                });
            </script>
        </body>
        </html>
        <?php
    }
}
?>