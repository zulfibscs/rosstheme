<?php
/**
 * Template Name: Homepage - Minimal Modern
 * Description: Clean, minimal design focused on content and typography
 * 
 * @package RossTheme
 */

$general_options = get_option('ross_theme_general_options', array());
$primary_color = $general_options['primary_color'] ?? '#000000';
$text_color = $general_options['text_color'] ?? '#333333';

get_header();
?>

<main id="primary" class="site-main ross-homepage-template ross-template-minimal">
    
    <!-- Minimal Hero -->
    <section class="ross-hero-minimal">
        <div class="ross-container-narrow">
            <div class="ross-hero-content-minimal">
                <h1 class="ross-hero-title-minimal">Simple. Clean. Effective.</h1>
                <p class="ross-hero-subtitle-minimal">
                    We believe in the power of simplicity. Less is more, and we make it work beautifully.
                </p>
                <a href="#services" class="ross-btn-minimal" style="color: <?php echo esc_attr($primary_color); ?>;">
                    Discover More →
                </a>
            </div>
        </div>
    </section>

    <!-- Feature Blocks -->
    <section class="ross-features-minimal" id="services">
        <div class="ross-container-narrow">
            <div class="ross-features-list">
                <?php
                $features = array(
                    array('number' => '01', 'title' => 'Clean Design', 'desc' => 'Minimalist aesthetics that focus on what matters most'),
                    array('number' => '02', 'title' => 'Fast Performance', 'desc' => 'Optimized for speed without compromising quality'),
                    array('number' => '03', 'title' => 'User Focused', 'desc' => 'Built with your audience in mind, always'),
                );
                
                foreach ($features as $feature):
                ?>
                <div class="ross-feature-minimal">
                    <span class="ross-feature-number" style="color: <?php echo esc_attr($primary_color); ?>;">
                        <?php echo esc_html($feature['number']); ?>
                    </span>
                    <h3><?php echo esc_html($feature['title']); ?></h3>
                    <p><?php echo esc_html($feature['desc']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Quote Section -->
    <section class="ross-quote-section">
        <div class="ross-container-narrow">
            <blockquote class="ross-quote-minimal">
                <p>"Simplicity is the ultimate sophistication."</p>
                <cite>— Leonardo da Vinci</cite>
            </blockquote>
        </div>
    </section>

    <!-- Services List -->
    <section class="ross-services-minimal">
        <div class="ross-container-narrow">
            <h2 class="ross-section-title-minimal">What We Offer</h2>
            <div class="ross-services-list-minimal">
                <?php
                $services = array('Strategy & Planning', 'Brand Identity', 'Web Development', 'Content Creation');
                foreach ($services as $service):
                ?>
                <div class="ross-service-item-minimal">
                    <h3><?php echo esc_html($service); ?></h3>
                    <div class="ross-service-line" style="background: <?php echo esc_attr($primary_color); ?>;"></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA Minimal -->
    <section class="ross-cta-minimal">
        <div class="ross-container-narrow">
            <div class="ross-cta-content-minimal">
                <h2>Let's Work Together</h2>
                <p>Ready to start your project? Get in touch.</p>
                <a href="#contact" class="ross-btn-minimal-large" style="background: <?php echo esc_attr($primary_color); ?>;">
                    Start a Conversation
                </a>
            </div>
        </div>
    </section>

    <!-- Blog Feed -->
    <section class="ross-blog-minimal">
        <div class="ross-container-narrow">
            <h2 class="ross-section-title-minimal">Latest Thoughts</h2>
            <div class="ross-blog-list-minimal">
                <?php
                $recent_posts = wp_get_recent_posts(array('numberposts' => 3));
                foreach ($recent_posts as $post):
                ?>
                <article class="ross-blog-item-minimal">
                    <time datetime="<?php echo get_the_date('c', $post['ID']); ?>">
                        <?php echo get_the_date('', $post['ID']); ?>
                    </time>
                    <h3><a href="<?php echo get_permalink($post['ID']); ?>"><?php echo esc_html($post['post_title']); ?></a></h3>
                    <p><?php echo wp_trim_words($post['post_content'], 20); ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
