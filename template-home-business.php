<?php
/**
 * Template Name: Homepage - Business Professional
 * Description: Professional business homepage with hero, services, and CTA
 * 
 * This template fully integrates with Ross Theme settings:
 * - Header from: Ross Theme → Header options
 * - Footer from: Ross Theme → Footer options
 * - Colors/Styling from: Ross Theme → General options
 * 
 * @package RossTheme
 */

// Get theme options for dynamic content
$header_options = function_exists('ross_theme_get_header_options') ? ross_theme_get_header_options() : array();
$general_options = get_option('ross_theme_general_options', array());

// Get dynamic colors from theme settings
$primary_color = $general_options['primary_color'] ?? '#001946';
$secondary_color = $general_options['secondary_color'] ?? '#E5C902';
$text_color = $general_options['text_color'] ?? '#333333';

get_header(); 
?>

<main id="primary" class="site-main ross-homepage-template ross-template-business">
    
    <!-- Hero Section -->
    <section class="ross-hero-section">
        <div class="ross-container">
            <div class="ross-hero-content">
                <h1 class="ross-hero-title">
                    <?php echo get_post_meta(get_the_ID(), '_ross_hero_title', true) ?: 'Transform Your Business Today'; ?>
                </h1>
                <p class="ross-hero-subtitle">
                    <?php echo get_post_meta(get_the_ID(), '_ross_hero_subtitle', true) ?: 'Professional solutions tailored to your needs. We help businesses grow and succeed in the digital age.'; ?>
                </p>
                <div class="ross-hero-buttons">
                    <a href="<?php echo get_post_meta(get_the_ID(), '_ross_hero_primary_link', true) ?: '#contact'; ?>" class="ross-btn ross-btn-primary">
                        <?php echo get_post_meta(get_the_ID(), '_ross_hero_primary_text', true) ?: 'Get Started'; ?>
                    </a>
                    <a href="<?php echo get_post_meta(get_the_ID(), '_ross_hero_secondary_link', true) ?: '#services'; ?>" class="ross-btn ross-btn-secondary">
                        <?php echo get_post_meta(get_the_ID(), '_ross_hero_secondary_text', true) ?: 'Learn More'; ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="ross-services-section">
        <div class="ross-container">
            <div class="ross-section-header">
                <h2 class="ross-section-title">Our Services</h2>
                <p class="ross-section-subtitle">Comprehensive solutions for your business needs</p>
            </div>
            
            <div class="ross-services-grid">
                <div class="ross-service-card">
                    <div class="ross-service-icon">
                        <span class="dashicons dashicons-chart-line"></span>
                    </div>
                    <h3>Business Strategy</h3>
                    <p>Strategic planning and consulting to drive your business forward</p>
                </div>
                
                <div class="ross-service-card">
                    <div class="ross-service-icon">
                        <span class="dashicons dashicons-laptop"></span>
                    </div>
                    <h3>Digital Solutions</h3>
                    <p>Modern technology solutions for the digital transformation</p>
                </div>
                
                <div class="ross-service-card">
                    <div class="ross-service-icon">
                        <span class="dashicons dashicons-groups"></span>
                    </div>
                    <h3>Team Training</h3>
                    <p>Expert training programs to empower your workforce</p>
                </div>
                
                <div class="ross-service-card">
                    <div class="ross-service-icon">
                        <span class="dashicons dashicons-performance"></span>
                    </div>
                    <h3>Growth Analytics</h3>
                    <p>Data-driven insights to measure and optimize performance</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="ross-testimonials-section">
        <div class="ross-container">
            <div class="ross-section-header">
                <h2 class="ross-section-title">Client Success Stories</h2>
            </div>
            
            <div class="ross-testimonials-grid">
                <div class="ross-testimonial-card">
                    <div class="ross-testimonial-content">
                        <p>"Working with this team has been transformative for our business. Their expertise and dedication are unmatched."</p>
                    </div>
                    <div class="ross-testimonial-author">
                        <strong>John Smith</strong>
                        <span>CEO, Tech Corp</span>
                    </div>
                </div>
                
                <div class="ross-testimonial-card">
                    <div class="ross-testimonial-content">
                        <p>"Exceptional service and results. They delivered exactly what we needed and exceeded our expectations."</p>
                    </div>
                    <div class="ross-testimonial-author">
                        <strong>Sarah Johnson</strong>
                        <span>Director, Marketing Pro</span>
                    </div>
                </div>
                
                <div class="ross-testimonial-card">
                    <div class="ross-testimonial-content">
                        <p>"Professional, reliable, and innovative. Our partnership has driven significant growth for our company."</p>
                    </div>
                    <div class="ross-testimonial-author">
                        <strong>Michael Chen</strong>
                        <span>Founder, StartupXYZ</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Banner Section -->
    <section class="ross-cta-banner-section">
        <div class="ross-container">
            <div class="ross-cta-banner-content">
                <h2>Ready to Transform Your Business?</h2>
                <p>Let's discuss how we can help you achieve your goals</p>
                <a href="#contact" class="ross-btn ross-btn-light">Contact Us Today</a>
            </div>
        </div>
    </section>

</main>

<?php
get_footer();
