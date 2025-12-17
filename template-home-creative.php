<?php
/**
 * Template Name: Homepage - Creative Agency
 * Description: Modern creative design with portfolio showcase and team section
 * 
 * Fully integrates with Ross Theme settings for dynamic control
 * 
 * @package RossTheme
 */

// Get theme options
$header_options = function_exists('ross_theme_get_header_options') ? ross_theme_get_header_options() : array();
$general_options = ross_get_general_options();
$footer_options = ross_get_footer_options();

// Dynamic colors
$primary_color = $general_options['primary_color'] ?? '#6C63FF';
$secondary_color = $general_options['secondary_color'] ?? '#FF6584';
$text_color = $general_options['text_color'] ?? '#333333';
$heading_color = $general_options['heading_color'] ?? '#1a1a1a';

get_header();
?>

<main id="primary" class="site-main ross-homepage-template ross-template-creative">
    
    <!-- Full-Screen Hero with Parallax -->
    <section class="ross-hero-fullscreen" style="background: linear-gradient(135deg, <?php echo esc_attr($primary_color); ?> 0%, <?php echo esc_attr($secondary_color); ?> 100%);">
        <div class="ross-hero-overlay"></div>
        <div class="ross-container">
            <div class="ross-hero-content-center">
                <span class="ross-hero-badge">Creative Studio</span>
                <h1 class="ross-hero-title-large">
                    <?php echo get_post_meta(get_the_ID(), '_ross_hero_title', true) ?: 'We Create Digital Experiences'; ?>
                </h1>
                <p class="ross-hero-subtitle-large">
                    <?php echo get_post_meta(get_the_ID(), '_ross_hero_subtitle', true) ?: 'Award-winning design agency crafting beautiful websites and brands that inspire.'; ?>
                </p>
                <div class="ross-hero-buttons-center">
                    <a href="#portfolio" class="ross-btn ross-btn-white-outline">
                        View Our Work
                    </a>
                    <a href="#contact" class="ross-btn ross-btn-white">
                        Start a Project
                    </a>
                </div>
                <div class="ross-scroll-indicator">
                    <span>Scroll Down</span>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 12L6 8h8l-4 4z"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Overview -->
    <section class="ross-services-creative">
        <div class="ross-container">
            <div class="ross-section-header-center">
                <span class="ross-section-badge">What We Do</span>
                <h2>Our Creative Services</h2>
                <p>Comprehensive digital solutions to elevate your brand</p>
            </div>
            
            <div class="ross-services-grid-3">
                <div class="ross-service-card-creative">
                    <div class="ross-service-icon" style="background: <?php echo esc_attr($primary_color); ?>;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <h3>Brand Identity</h3>
                    <p>Complete brand design including logo, colors, typography, and guidelines.</p>
                    <a href="#" class="ross-service-link">Learn More →</a>
                </div>
                
                <div class="ross-service-card-creative">
                    <div class="ross-service-icon" style="background: <?php echo esc_attr($secondary_color); ?>;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="3" width="20" height="14" rx="2"/>
                            <path d="M8 21h8M12 17v4"/>
                        </svg>
                    </div>
                    <h3>Web Design</h3>
                    <p>Modern, responsive websites that convert visitors into customers.</p>
                    <a href="#" class="ross-service-link">Learn More →</a>
                </div>
                
                <div class="ross-service-card-creative">
                    <div class="ross-service-icon" style="background: <?php echo esc_attr($primary_color); ?>;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"/>
                            <rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/>
                        </svg>
                    </div>
                    <h3>UI/UX Design</h3>
                    <p>User-centered design that creates delightful experiences.</p>
                    <a href="#" class="ross-service-link">Learn More →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Grid with Filters -->
    <section class="ross-portfolio-section" id="portfolio">
        <div class="ross-container">
            <div class="ross-section-header-center">
                <span class="ross-section-badge">Our Work</span>
                <h2>Featured Projects</h2>
                <p>A selection of our recent creative projects</p>
            </div>
            
            <!-- Portfolio Filters -->
            <div class="ross-portfolio-filters">
                <button class="ross-filter-btn active" data-filter="*">All Projects</button>
                <button class="ross-filter-btn" data-filter=".branding">Branding</button>
                <button class="ross-filter-btn" data-filter=".web">Web Design</button>
                <button class="ross-filter-btn" data-filter=".ui-ux">UI/UX</button>
            </div>
            
            <!-- Portfolio Grid -->
            <div class="ross-portfolio-grid">
                <?php
                // Portfolio items
                $portfolio_items = array(
                    array('title' => 'Modern Brand Identity', 'category' => 'branding', 'image' => 'portfolio-1.jpg'),
                    array('title' => 'E-Commerce Platform', 'category' => 'web', 'image' => 'portfolio-2.jpg'),
                    array('title' => 'Mobile App Design', 'category' => 'ui-ux', 'image' => 'portfolio-3.jpg'),
                    array('title' => 'Corporate Website', 'category' => 'web', 'image' => 'portfolio-4.jpg'),
                    array('title' => 'Startup Branding', 'category' => 'branding', 'image' => 'portfolio-5.jpg'),
                    array('title' => 'Dashboard UI', 'category' => 'ui-ux', 'image' => 'portfolio-6.jpg'),
                );
                
                foreach ($portfolio_items as $item):
                ?>
                <div class="ross-portfolio-item <?php echo esc_attr($item['category']); ?>">
                    <div class="ross-portfolio-image">
                        <div class="ross-portfolio-placeholder" style="background: linear-gradient(135deg, <?php echo esc_attr($primary_color); ?> 0%, <?php echo esc_attr($secondary_color); ?> 100%);"></div>
                        <div class="ross-portfolio-overlay">
                            <h3><?php echo esc_html($item['title']); ?></h3>
                            <span class="ross-portfolio-category"><?php echo esc_html(ucfirst(str_replace('-', ' ', $item['category']))); ?></span>
                            <a href="#" class="ross-portfolio-link">View Project</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Team Members -->
    <section class="ross-team-section">
        <div class="ross-container">
            <div class="ross-section-header-center">
                <span class="ross-section-badge">Meet The Team</span>
                <h2>Creative Minds</h2>
                <p>Talented professionals dedicated to your success</p>
            </div>
            
            <div class="ross-team-grid-4">
                <?php
                $team_members = array(
                    array('name' => 'Sarah Johnson', 'role' => 'Creative Director', 'social' => array('linkedin' => '#', 'twitter' => '#')),
                    array('name' => 'Michael Chen', 'role' => 'Lead Designer', 'social' => array('linkedin' => '#', 'dribbble' => '#')),
                    array('name' => 'Emma Davis', 'role' => 'UX Designer', 'social' => array('linkedin' => '#', 'behance' => '#')),
                    array('name' => 'David Miller', 'role' => 'Art Director', 'social' => array('linkedin' => '#', 'instagram' => '#')),
                );
                
                foreach ($team_members as $member):
                ?>
                <div class="ross-team-card">
                    <div class="ross-team-image">
                        <div class="ross-team-placeholder" style="background: <?php echo esc_attr($primary_color); ?>;"></div>
                    </div>
                    <h3><?php echo esc_html($member['name']); ?></h3>
                    <p class="ross-team-role"><?php echo esc_html($member['role']); ?></p>
                    <div class="ross-team-social">
                        <?php foreach ($member['social'] as $platform => $url): ?>
                        <a href="<?php echo esc_url($url); ?>" class="ross-social-icon">
                            <i class="fab fa-<?php echo esc_attr($platform); ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Client Logos -->
    <section class="ross-clients-section">
        <div class="ross-container">
            <div class="ross-section-header-center">
                <h2>Trusted by Leading Brands</h2>
            </div>
            
            <div class="ross-clients-grid">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="ross-client-logo">
                    <div class="ross-client-placeholder">Client <?php echo $i; ?></div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- Process Timeline -->
    <section class="ross-process-section" style="background: #f8f9fa;">
        <div class="ross-container">
            <div class="ross-section-header-center">
                <span class="ross-section-badge">How We Work</span>
                <h2>Our Creative Process</h2>
                <p>From concept to launch, we've got you covered</p>
            </div>
            
            <div class="ross-process-timeline">
                <?php
                $process_steps = array(
                    array('number' => '01', 'title' => 'Discovery', 'description' => 'Understanding your goals, audience, and requirements'),
                    array('number' => '02', 'title' => 'Strategy', 'description' => 'Developing a comprehensive plan and creative direction'),
                    array('number' => '03', 'title' => 'Design', 'description' => 'Creating stunning visuals and user experiences'),
                    array('number' => '04', 'title' => 'Development', 'description' => 'Building with clean code and best practices'),
                    array('number' => '05', 'title' => 'Launch', 'description' => 'Deploying and monitoring for optimal performance'),
                );
                
                foreach ($process_steps as $step):
                ?>
                <div class="ross-process-step">
                    <div class="ross-process-number" style="background: <?php echo esc_attr($primary_color); ?>;">
                        <?php echo esc_html($step['number']); ?>
                    </div>
                    <h3><?php echo esc_html($step['title']); ?></h3>
                    <p><?php echo esc_html($step['description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="ross-cta-creative" style="background: linear-gradient(135deg, <?php echo esc_attr($primary_color); ?> 0%, <?php echo esc_attr($secondary_color); ?> 100%);">
        <div class="ross-container">
            <div class="ross-cta-content-center">
                <h2>Ready to Start Your Project?</h2>
                <p>Let's create something amazing together</p>
                <div class="ross-cta-buttons">
                    <a href="#contact" class="ross-btn ross-btn-white">Get Started</a>
                    <a href="#portfolio" class="ross-btn ross-btn-white-outline">View Portfolio</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="ross-contact-section" id="contact">
        <div class="ross-container">
            <div class="ross-contact-grid">
                <div class="ross-contact-info">
                    <h2>Let's Talk</h2>
                    <p>Have a project in mind? We'd love to hear from you.</p>
                    
                    <div class="ross-contact-details">
                        <div class="ross-contact-item">
                            <div class="ross-contact-icon" style="background: <?php echo esc_attr($primary_color); ?>;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <strong>Email</strong>
                                <p>hello@creative.agency</p>
                            </div>
                        </div>
                        
                        <div class="ross-contact-item">
                            <div class="ross-contact-icon" style="background: <?php echo esc_attr($secondary_color); ?>;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
                                </svg>
                            </div>
                            <div>
                                <strong>Phone</strong>
                                <p>+1 (555) 123-4567</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="ross-contact-form">
                    <form class="ross-form">
                        <div class="ross-form-group">
                            <input type="text" placeholder="Your Name" required>
                        </div>
                        <div class="ross-form-group">
                            <input type="email" placeholder="Your Email" required>
                        </div>
                        <div class="ross-form-group">
                            <textarea rows="5" placeholder="Tell us about your project" required></textarea>
                        </div>
                        <button type="submit" class="ross-btn ross-btn-primary" style="background: <?php echo esc_attr($primary_color); ?>;">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
