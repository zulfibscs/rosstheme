<?php
/**
 * Template Name: Homepage - Startup Launch
 * Description: Dynamic startup page with app features and pricing tables
 * 
 * @package RossTheme
 */

$general_options = get_option('ross_theme_general_options', array());
$primary_color = $general_options['primary_color'] ?? '#5E35B1';
$secondary_color = $general_options['secondary_color'] ?? '#00BCD4';

get_header();
?>

<main id="primary" class="site-main ross-homepage-template ross-template-startup">
    
    <!-- App Showcase Hero -->
    <section class="ross-hero-startup">
        <div class="ross-container">
            <div class="ross-hero-app-split">
                <div class="ross-hero-app-content">
                    <span class="ross-badge-new" style="background: <?php echo esc_attr($secondary_color); ?>;">New Launch</span>
                    <h1>The App That Changes Everything</h1>
                    <p>Revolutionize the way you work with our innovative platform. Join thousands of happy users worldwide.</p>
                    <div class="ross-app-buttons">
                        <a href="#" class="ross-btn-app-store">
                            <svg width="24" height="24"><use href="#icon-apple"/></svg>
                            App Store
                        </a>
                        <a href="#" class="ross-btn-play-store">
                            <svg width="24" height="24"><use href="#icon-google"/></svg>
                            Google Play
                        </a>
                    </div>
                    <div class="ross-app-stats">
                        <div class="ross-stat">
                            <strong>10K+</strong>
                            <span>Downloads</span>
                        </div>
                        <div class="ross-stat">
                            <strong>4.9</strong>
                            <span>Rating</span>
                        </div>
                        <div class="ross-stat">
                            <strong>50+</strong>
                            <span>Countries</span>
                        </div>
                    </div>
                </div>
                <div class="ross-hero-app-mockup">
                    <div class="ross-mockup-placeholder" style="background: linear-gradient(135deg, <?php echo esc_attr($primary_color); ?> 0%, <?php echo esc_attr($secondary_color); ?> 100%);"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Comparison -->
    <section class="ross-features-startup">
        <div class="ross-container">
            <div class="ross-section-header-center">
                <h2>Powerful Features</h2>
                <p>Everything you need to succeed, all in one place</p>
            </div>
            <div class="ross-features-grid-3">
                <?php
                $features = array(
                    array('icon' => 'zap', 'title' => 'Lightning Fast', 'desc' => 'Blazing fast performance optimized for speed'),
                    array('icon' => 'shield', 'title' => 'Secure & Private', 'desc' => 'Bank-level security to protect your data'),
                    array('icon' => 'users', 'title' => 'Team Collaboration', 'desc' => 'Work together seamlessly in real-time'),
                    array('icon' => 'bar-chart', 'title' => 'Analytics', 'desc' => 'Detailed insights and reporting'),
                    array('icon' => 'smartphone', 'title' => 'Mobile First', 'desc' => 'Perfect experience on any device'),
                    array('icon' => 'headphones', 'title' => '24/7 Support', 'desc' => 'Always here when you need us'),
                );
                
                foreach ($features as $feature):
                ?>
                <div class="ross-feature-startup">
                    <div class="ross-feature-icon-startup" style="background: <?php echo esc_attr($primary_color); ?>15; color: <?php echo esc_attr($primary_color); ?>;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <use href="#icon-<?php echo esc_attr($feature['icon']); ?>"/>
                        </svg>
                    </div>
                    <h3><?php echo esc_html($feature['title']); ?></h3>
                    <p><?php echo esc_html($feature['desc']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Pricing Tables -->
    <section class="ross-pricing-section" id="pricing">
        <div class="ross-container">
            <div class="ross-section-header-center">
                <h2>Simple, Transparent Pricing</h2>
                <p>Choose the plan that's right for you</p>
            </div>
            <div class="ross-pricing-grid-3">
                <?php
                $plans = array(
                    array('name' => 'Starter', 'price' => '9', 'features' => array('Up to 5 users', 'Basic features', 'Email support', '1GB storage')),
                    array('name' => 'Professional', 'price' => '29', 'popular' => true, 'features' => array('Up to 25 users', 'All features', 'Priority support', '50GB storage')),
                    array('name' => 'Enterprise', 'price' => '99', 'features' => array('Unlimited users', 'Premium features', '24/7 support', 'Unlimited storage')),
                );
                
                foreach ($plans as $plan):
                ?>
                <div class="ross-pricing-card <?php echo isset($plan['popular']) ? 'popular' : ''; ?>">
                    <?php if (isset($plan['popular'])): ?>
                    <span class="ross-popular-badge" style="background: <?php echo esc_attr($primary_color); ?>;">Most Popular</span>
                    <?php endif; ?>
                    <h3><?php echo esc_html($plan['name']); ?></h3>
                    <div class="ross-price">
                        <span class="ross-currency">$</span>
                        <span class="ross-amount"><?php echo esc_html($plan['price']); ?></span>
                        <span class="ross-period">/month</span>
                    </div>
                    <ul class="ross-features-list">
                        <?php foreach ($plan['features'] as $feature): ?>
                        <li>✓ <?php echo esc_html($feature); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="#" class="ross-btn-pricing" style="<?php echo isset($plan['popular']) ? 'background: ' . esc_attr($primary_color) : ''; ?>">
                        Get Started
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="ross-testimonials-startup" style="background: #f8f9fa;">
        <div class="ross-container">
            <div class="ross-section-header-center">
                <h2>Loved by Users Worldwide</h2>
            </div>
            <div class="ross-testimonials-grid-3">
                <?php
                for ($i = 1; $i <= 3; $i++):
                ?>
                <div class="ross-testimonial-card-startup">
                    <div class="ross-stars">★★★★★</div>
                    <p>"This app has completely transformed how we work. Can't imagine going back to the old way."</p>
                    <div class="ross-author">
                        <div class="ross-author-avatar" style="background: <?php echo esc_attr($primary_color); ?>;"></div>
                        <div>
                            <strong>User Name</strong>
                            <span>CEO, Company</span>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="ross-faq-section">
        <div class="ross-container-narrow">
            <div class="ross-section-header-center">
                <h2>Frequently Asked Questions</h2>
            </div>
            <div class="ross-faq-accordion">
                <?php
                $faqs = array(
                    array('q' => 'How do I get started?', 'a' => 'Simply download the app, create an account, and you\'re ready to go in minutes.'),
                    array('q' => 'Can I change plans later?', 'a' => 'Yes, you can upgrade or downgrade your plan at any time.'),
                    array('q' => 'Is my data secure?', 'a' => 'Absolutely. We use bank-level encryption to keep your data safe.'),
                    array('q' => 'What payment methods do you accept?', 'a' => 'We accept all major credit cards and PayPal.'),
                );
                
                foreach ($faqs as $index => $faq):
                ?>
                <div class="ross-faq-item">
                    <button class="ross-faq-question" data-index="<?php echo $index; ?>">
                        <?php echo esc_html($faq['q']); ?>
                        <span class="ross-faq-icon">+</span>
                    </button>
                    <div class="ross-faq-answer">
                        <p><?php echo esc_html($faq['a']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Download CTA -->
    <section class="ross-download-cta" style="background: linear-gradient(135deg, <?php echo esc_attr($primary_color); ?> 0%, <?php echo esc_attr($secondary_color); ?> 100%);">
        <div class="ross-container">
            <div class="ross-cta-content-center">
                <h2>Ready to Get Started?</h2>
                <p>Download our app today and join thousands of satisfied users</p>
                <div class="ross-download-buttons">
                    <a href="#" class="ross-btn-download">
                        <svg width="24" height="24"><use href="#icon-apple"/></svg>
                        Download for iOS
                    </a>
                    <a href="#" class="ross-btn-download">
                        <svg width="24" height="24"><use href="#icon-google"/></svg>
                        Download for Android
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
