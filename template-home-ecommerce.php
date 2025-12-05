<?php
/**
 * Template Name: Homepage - E-Commerce Store
 * Description: Product-focused layout with featured items and promotional banners
 * 
 * Fully integrates with Ross Theme settings
 * 
 * @package RossTheme
 */

$general_options = get_option('ross_theme_general_options', array());
$primary_color = $general_options['primary_color'] ?? '#FF5722';
$secondary_color = $general_options['secondary_color'] ?? '#FFC107';

get_header();
?>

<main id="primary" class="site-main ross-homepage-template ross-template-ecommerce">
    
    <!-- Hero Banner with Promo -->
    <section class="ross-hero-ecommerce" style="background: linear-gradient(to right, <?php echo esc_attr($primary_color); ?>, <?php echo esc_attr($secondary_color); ?>);">
        <div class="ross-container">
            <div class="ross-hero-split">
                <div class="ross-hero-content">
                    <span class="ross-badge-sale">Summer Sale</span>
                    <h1>Up to 50% Off</h1>
                    <p>Get the best deals on our entire collection. Limited time offer!</p>
                    <a href="#products" class="ross-btn ross-btn-white">Shop Now</a>
                </div>
                <div class="ross-hero-image">
                    <div class="ross-product-showcase"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Grid -->
    <section class="ross-categories">
        <div class="ross-container">
            <h2>Shop by Category</h2>
            <div class="ross-category-grid-4">
                <?php
                $categories = array('Electronics', 'Fashion', 'Home & Living', 'Sports');
                foreach ($categories as $cat):
                ?>
                <div class="ross-category-card">
                    <div class="ross-category-image" style="background: <?php echo esc_attr($primary_color); ?>15;"></div>
                    <h3><?php echo esc_html($cat); ?></h3>
                    <a href="#">Browse →</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="ross-products-featured" id="products">
        <div class="ross-container">
            <div class="ross-section-header">
                <h2>Trending Products</h2>
                <a href="#" class="ross-view-all">View All →</a>
            </div>
            <div class="ross-products-grid-4">
                <?php for ($i = 1; $i <= 8; $i++): ?>
                <div class="ross-product-card">
                    <div class="ross-product-image">
                        <span class="ross-product-badge">-30%</span>
                        <div class="ross-product-placeholder"></div>
                    </div>
                    <h3>Product Name <?php echo $i; ?></h3>
                    <div class="ross-product-price">
                        <span class="ross-price-sale" style="color: <?php echo esc_attr($primary_color); ?>;">$49.99</span>
                        <span class="ross-price-original">$69.99</span>
                    </div>
                    <button class="ross-btn ross-btn-small" style="background: <?php echo esc_attr($primary_color); ?>;">Add to Cart</button>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- Promotional Banner -->
    <section class="ross-promo-banner" style="background: <?php echo esc_attr($secondary_color); ?>;">
        <div class="ross-container">
            <div class="ross-promo-content">
                <h2>Free Shipping on Orders Over $50</h2>
                <p>Shop now and enjoy free delivery to your doorstep</p>
                <a href="#" class="ross-btn ross-btn-dark">Shop Now</a>
            </div>
        </div>
    </section>

    <!-- Newsletter Signup -->
    <section class="ross-newsletter">
        <div class="ross-container">
            <div class="ross-newsletter-content">
                <h2>Get Exclusive Deals</h2>
                <p>Subscribe to our newsletter and get 10% off your first order</p>
                <form class="ross-newsletter-form">
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit" style="background: <?php echo esc_attr($primary_color); ?>;">Subscribe</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Trust Badges -->
    <section class="ross-trust-badges">
        <div class="ross-container">
            <div class="ross-badges-grid-4">
                <div class="ross-badge-item">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/>
                    </svg>
                    <h3>Secure Payment</h3>
                </div>
                <div class="ross-badge-item">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                    <h3>Fast Shipping</h3>
                </div>
                <div class="ross-badge-item">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                    </svg>
                    <h3>24/7 Support</h3>
                </div>
                <div class="ross-badge-item">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <h3>Money Back</h3>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
