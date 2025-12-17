<?php
/**
 * Template Name: Homepage - Restaurant & Cafe
 * Description: Food-focused design with menu highlights and reservation form
 * 
 * @package RossTheme
 */

$general_options = ross_get_general_options();
$primary_color = $general_options['primary_color'] ?? '#D35400';
$secondary_color = $general_options['secondary_color'] ?? '#E74C3C';

get_header();
?>

<main id="primary" class="site-main ross-homepage-template ross-template-restaurant">
    
    <!-- Full-Width Hero Banner -->
    <section class="ross-hero-restaurant" style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url() center/cover;">
        <div class="ross-container">
            <div class="ross-hero-content-restaurant">
                <span class="ross-badge-restaurant" style="background: <?php echo esc_attr($primary_color); ?>;">Since 1995</span>
                <h1>Experience Fine Dining</h1>
                <p>Authentic flavors crafted with passion and served with love</p>
                <div class="ross-hero-buttons">
                    <a href="#menu" class="ross-btn-restaurant" style="background: <?php echo esc_attr($primary_color); ?>;">
                        View Menu
                    </a>
                    <a href="#reservation" class="ross-btn-restaurant-outline">
                        Make Reservation
                    </a>
                </div>
                <div class="ross-opening-hours">
                    <span>Open Daily: 11:00 AM - 11:00 PM</span>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="ross-about-restaurant">
        <div class="ross-container">
            <div class="ross-about-split">
                <div class="ross-about-image">
                    <div class="ross-image-placeholder" style="background: <?php echo esc_attr($primary_color); ?>15;"></div>
                </div>
                <div class="ross-about-content">
                    <span class="ross-section-label" style="color: <?php echo esc_attr($primary_color); ?>;">Our Story</span>
                    <h2>Tradition Meets Innovation</h2>
                    <p>For over 25 years, we've been serving our community with delicious, authentic cuisine made from the finest ingredients. Our chefs blend traditional recipes with modern techniques to create unforgettable dining experiences.</p>
                    <p>Every dish tells a story, and we invite you to be part of ours.</p>
                    <a href="#" class="ross-link-restaurant" style="color: <?php echo esc_attr($primary_color); ?>;">
                        Learn More About Us →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Highlights -->
    <section class="ross-menu-section" id="menu" style="background: #f8f9fa;">
        <div class="ross-container">
            <div class="ross-section-header-center">
                <span class="ross-section-label" style="color: <?php echo esc_attr($primary_color); ?>;">Our Menu</span>
                <h2>Signature Dishes</h2>
                <p>Handpicked favorites from our kitchen</p>
            </div>
            
            <div class="ross-menu-grid-2">
                <?php
                $dishes = array(
                    array('name' => 'Grilled Salmon', 'desc' => 'Fresh Atlantic salmon with seasonal vegetables', 'price' => '28'),
                    array('name' => 'Beef Tenderloin', 'desc' => 'Premium cuts with truffle mashed potatoes', 'price' => '35'),
                    array('name' => 'Lobster Risotto', 'desc' => 'Creamy arborio rice with fresh lobster', 'price' => '32'),
                    array('name' => 'Vegetarian Delight', 'desc' => 'Seasonal vegetables with herb sauce', 'price' => '22'),
                );
                
                foreach ($dishes as $dish):
                ?>
                <div class="ross-menu-item">
                    <div class="ross-menu-header">
                        <h3><?php echo esc_html($dish['name']); ?></h3>
                        <span class="ross-menu-price" style="color: <?php echo esc_attr($primary_color); ?>;">
                            $<?php echo esc_html($dish['price']); ?>
                        </span>
                    </div>
                    <p class="ross-menu-desc"><?php echo esc_html($dish['desc']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="ross-menu-cta">
                <a href="#" class="ross-btn-restaurant" style="background: <?php echo esc_attr($primary_color); ?>;">
                    View Full Menu
                </a>
            </div>
        </div>
    </section>

    <!-- Image Gallery -->
    <section class="ross-gallery-section">
        <div class="ross-container">
            <div class="ross-section-header-center">
                <h2>Gallery</h2>
                <p>A visual taste of our cuisine</p>
            </div>
            <div class="ross-gallery-grid-3">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="ross-gallery-item">
                    <div class="ross-gallery-image" style="background: <?php echo esc_attr($primary_color); ?>15;"></div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- Special Offers -->
    <section class="ross-specials-section" style="background: <?php echo esc_attr($primary_color); ?>; color: white;">
        <div class="ross-container">
            <div class="ross-specials-content">
                <h2>Chef's Special This Week</h2>
                <p class="ross-special-dish">Pan-Seared Sea Bass with Lemon Butter Sauce</p>
                <p>Available Tuesday - Saturday | Limited Seating</p>
                <a href="#reservation" class="ross-btn-white">Reserve Your Table</a>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="ross-testimonials-restaurant">
        <div class="ross-container">
            <div class="ross-section-header-center">
                <h2>What Our Guests Say</h2>
            </div>
            <div class="ross-testimonials-grid-3">
                <?php
                $reviews = array(
                    array('name' => 'Sarah M.', 'review' => 'Best dining experience in town! The food is exceptional and the service is impeccable.', 'rating' => 5),
                    array('name' => 'John D.', 'review' => 'Absolutely loved the atmosphere and the menu variety. Will definitely come back!', 'rating' => 5),
                    array('name' => 'Emma L.', 'review' => 'The chef\'s special was amazing. Every dish was a masterpiece. Highly recommend!', 'rating' => 5),
                );
                
                foreach ($reviews as $review):
                ?>
                <div class="ross-review-card">
                    <div class="ross-review-stars" style="color: <?php echo esc_attr($secondary_color); ?>;">
                        <?php echo str_repeat('★', $review['rating']); ?>
                    </div>
                    <p class="ross-review-text">"<?php echo esc_html($review['review']); ?>"</p>
                    <strong class="ross-review-author"><?php echo esc_html($review['name']); ?></strong>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Reservation Form -->
    <section class="ross-reservation-section" id="reservation" style="background: #f8f9fa;">
        <div class="ross-container">
            <div class="ross-reservation-split">
                <div class="ross-reservation-info">
                    <h2>Make a Reservation</h2>
                    <p>Book your table for an unforgettable dining experience</p>
                    
                    <div class="ross-contact-details">
                        <div class="ross-contact-item">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
                            </svg>
                            <div>
                                <strong>Phone</strong>
                                <p>+1 (555) 123-4567</p>
                            </div>
                        </div>
                        
                        <div class="ross-contact-item">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <div>
                                <strong>Location</strong>
                                <p>123 Main Street, City</p>
                            </div>
                        </div>
                        
                        <div class="ross-contact-item">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <div>
                                <strong>Hours</strong>
                                <p>Mon-Sun: 11AM - 11PM</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="ross-reservation-form">
                    <form class="ross-form-restaurant">
                        <div class="ross-form-row">
                            <div class="ross-form-group">
                                <input type="text" placeholder="Your Name" required>
                            </div>
                            <div class="ross-form-group">
                                <input type="email" placeholder="Email" required>
                            </div>
                        </div>
                        <div class="ross-form-row">
                            <div class="ross-form-group">
                                <input type="tel" placeholder="Phone" required>
                            </div>
                            <div class="ross-form-group">
                                <input type="number" placeholder="Guests" min="1" max="20" required>
                            </div>
                        </div>
                        <div class="ross-form-row">
                            <div class="ross-form-group">
                                <input type="date" required>
                            </div>
                            <div class="ross-form-group">
                                <input type="time" required>
                            </div>
                        </div>
                        <div class="ross-form-group">
                            <textarea rows="3" placeholder="Special requests"></textarea>
                        </div>
                        <button type="submit" class="ross-btn-restaurant" style="background: <?php echo esc_attr($primary_color); ?>;">
                            Reserve Table
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Location Map -->
    <section class="ross-map-section">
        <div class="ross-map-placeholder" style="background: #e0e0e0; height: 400px; display: flex; align-items: center; justify-content: center;">
            <p>Map Integration Here (Google Maps Embed)</p>
        </div>
    </section>

</main>

<?php get_footer(); ?>
