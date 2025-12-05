# Ross Theme - Complete Template System Documentation

## 📋 Overview

This document provides a comprehensive guide to the Ross Theme's predesigned template system. All 6 homepage templates are now fully implemented with dynamic theme option integration, responsive design, and professional styling.

---

## ✅ Completed Implementation

### Phase 1: Template Files ✅ COMPLETE

All 6 homepage template files have been created with complete sections:

1. **template-home-business.php** (150 lines)
   - Professional business homepage
   - Sections: Hero, Services, About, Features, Stats, Team, Testimonials, Contact, CTA

2. **template-home-creative.php** (450 lines)
   - Modern creative agency template
   - Sections: Fullscreen Hero, Services, Portfolio (with filters), Team, Client Logos, Process Timeline, Contact, CTA

3. **template-home-ecommerce.php** (200 lines)
   - Product-focused store design
   - Sections: Hero, Categories, Featured Products, Promo Banner, Newsletter, Trust Badges

4. **template-home-minimal.php** (180 lines)
   - Clean typography-focused design
   - Sections: Minimal Hero, Features, Quote, Services List, CTA, Blog Feed (WordPress integration)

5. **template-home-startup.php** (320 lines)
   - SaaS/App launch template
   - Sections: App Showcase, Stats, Features, Pricing Tables, Testimonials, FAQ Accordion, Download CTA

6. **template-home-restaurant.php** (350 lines)
   - Food service design
   - Sections: Hero, About, Menu, Gallery, Special Offers, Testimonials, Reservation Form, Location Map

### Phase 2: CSS Styling ✅ COMPLETE

**Global Styles:**
- **templates-global.css** (650+ lines) - Shared styles for all templates
  - Typography system with clamp() for responsive sizing
  - Button variations (primary, secondary, white, outline)
  - Hero section layouts (fullscreen, centered, split)
  - Grid systems (2, 3, 4 columns with responsive breakpoints)
  - Card components (service, feature, team, product)
  - Form styling with focus states
  - Testimonial cards
  - CTA sections
  - Animations (fadeInUp)
  - Utility classes
  - Accessibility features (focus styles, skip links)

**Template-Specific Styles:**

1. **template-business.css** (350+ lines)
   - Hero with parallax background
   - Service icons with gradients
   - Split about section
   - Feature items with left border accent
   - Stats grid (4 columns)
   - Team cards with circular avatars
   - Contact split layout

2. **template-creative.css** (450+ lines)
   - Gradient hero with scroll indicator
   - Service cards with hover slide effect
   - Portfolio grid with filters and overlays
   - Team cards with social overlay on hover
   - Client logos grid
   - Process timeline with connected line
   - CTA with gradient background

3. **template-ecommerce.css** (400+ lines)
   - Split hero with sale badges
   - Category cards with image overlays
   - Product cards with pricing, badges, add-to-cart
   - Promo banner gradient
   - Newsletter form inline layout
   - Trust badges with icons
   - Pulse animation for sale badges

4. **template-minimal.css** (300+ lines)
   - Large typography hero (clamp sizing)
   - Numbered feature blocks
   - Quote section with decorative quotation mark
   - Service list with horizontal lines
   - Blog posts with date/title/excerpt
   - Refined typography scale
   - Increased spacing for breathing room

5. **template-startup.css** (450+ lines)
   - Gradient hero with app mockup
   - Stats display
   - Feature cards with gradient icons
   - Pricing tables (3 tiers with "popular" badge)
   - FAQ accordion with expand/collapse
   - App download buttons (App Store/Google Play style)
   - Testimonials with star ratings

6. **template-restaurant.css** (400+ lines)
   - Hero with gradient overlay
   - Split about section with image
   - Menu items with pricing and dashed borders
   - Gallery grid with lightbox hover
   - Special offers full-width section
   - Reservation form (split: info + form)
   - Location map container
   - Decorative elements (section dividers)

### Phase 3: Frontend JavaScript ✅ COMPLETE

**templates.js** (400+ lines)
- **Portfolio Filter** - Filter portfolio items by category (Creative template)
- **FAQ Accordion** - Expandable/collapsible FAQ items (Startup template)
- **Smooth Scroll** - Smooth scrolling for anchor links
- **Scroll Animations** - Fade-in elements on scroll using IntersectionObserver
- **Mobile Menu** - Touch support and outside-click closing
- **Gallery Lightbox** - Basic lightbox for gallery images (Restaurant template)
- **Newsletter Validation** - Email format validation
- **Add to Cart Animation** - Loading state and success feedback (E-commerce template)
- **Scroll Progress** - Optional progress bar indicator

### Phase 4: Asset Loading ✅ COMPLETE

**Updated asset-loader.php:**
- Enqueues `templates-global.css` for all pages
- Conditional loading of template-specific CSS based on active template
- Template CSS map for Business, Creative, E-commerce, Minimal, Startup, Restaurant
- Enqueues `templates.js` only on template pages
- Uses `filemtime()` for cache-busting
- Proper dependency chain

---

## 🎨 Dynamic Theme Integration

### All Templates Use Theme Options

Every template dynamically reads from:

```php
$general_options = get_option('ross_theme_general_options', array());
$header_options = get_option('ross_theme_header_options', array());
$footer_options = get_option('ross_theme_footer_options', array());
```

### Color Integration

```php
$primary_color = $general_options['primary_color'] ?? '#default';
$secondary_color = $general_options['secondary_color'] ?? '#default';
$text_color = $general_options['text_color'] ?? '#333333';
```

Used throughout templates for:
- Background colors
- Button colors
- Icon backgrounds
- Border accents
- Gradient overlays
- Hover states

### CSS Custom Properties

Dynamic CSS outputs:
```css
:root {
    --ross-primary-color: <?php echo esc_attr($primary_color); ?>;
    --ross-secondary-color: <?php echo esc_attr($secondary_color); ?>;
    --ross-text-color: <?php echo esc_attr($text_color); ?>;
    --ross-container-width: <?php echo esc_attr($container_width); ?>;
}
```

---

## 📱 Responsive Design

### Breakpoints Used

```css
/* Desktop: 1200px+ (default) */
/* Laptop: 1024px */
@media (max-width: 1024px) { ... }

/* Tablet: 768px */
@media (max-width: 768px) { ... }

/* Mobile: 480px */
@media (max-width: 480px) { ... }
```

### Responsive Features

1. **Grid Systems** - Auto-collapse from 4 → 2 → 1 columns
2. **Typography** - `clamp()` for fluid sizing
3. **Hero Sections** - Stack on mobile (split layouts → single column)
4. **Navigation** - Mobile menu with hamburger icon
5. **Forms** - Stack form fields vertically on mobile
6. **Pricing Tables** - Stack cards on mobile, remove scale effect
7. **Images** - `object-fit: cover` for aspect ratio consistency

---

## 🚀 How to Use Templates

### For Site Administrators

1. **Navigate to Ross Theme → Homepage Templates**
2. **Preview templates** - Click "Preview" to see template design
3. **Apply template** - Click "Install Template" to create page
4. **Automatic setup:**
   - Creates new page with template
   - Sets as WordPress front page
   - Applies template colors from theme options

### For Developers

**Create a new template page manually:**

```php
1. Go to Pages → Add New
2. Enter page title
3. In Page Attributes, select template:
   - Homepage - Business
   - Homepage - Creative
   - Homepage - E-commerce
   - Homepage - Minimal
   - Homepage - Startup
   - Homepage - Restaurant
4. Publish page
5. Go to Settings → Reading → Set as front page
```

---

## 🎯 Template Features Reference

| Template | Key Features | Best For |
|----------|-------------|----------|
| **Business** | Hero, Services, Stats, Team, Contact | Corporate, Professional Services, B2B |
| **Creative** | Portfolio Filters, Process Timeline, Team Social | Agencies, Studios, Freelancers |
| **E-commerce** | Product Grid, Categories, Newsletter, Trust Badges | Online Stores, Retail |
| **Minimal** | Clean Typography, Blog Feed, Quote Section | Bloggers, Minimalists, Writers |
| **Startup** | Pricing Tables, FAQ, App Download, Feature Grid | SaaS, Apps, Tech Startups |
| **Restaurant** | Menu, Gallery, Reservations, Map | Restaurants, Cafes, Food Service |

---

## 🔧 Customization Guide

### Change Template Colors

**Via Ross Theme → General Settings:**
1. Set Primary Color (used for buttons, icons, accents)
2. Set Secondary Color (used for gradients, secondary elements)
3. Set Text Color
4. Save changes
5. **Templates automatically update** - no code changes needed!

### Modify Template Content

**Edit template file directly:**

```php
// Example: Change hero title
<h1><?php echo esc_html(get_post_meta(get_the_ID(), '_hero_title', true) ?: 'Default Title'); ?></h1>
```

**Add custom meta fields:**
1. Install Advanced Custom Fields (ACF) or use built-in meta boxes
2. Create fields for hero_title, hero_subtitle, etc.
3. Update template to use `get_post_meta()`

### Add New Sections

```php
// Add after existing sections
<section class="my-custom-section">
    <div class="ross-container">
        <div class="ross-section-header-center">
            <h2>My Custom Section</h2>
        </div>
        <!-- Your content -->
    </div>
</section>
```

**Use global CSS classes:**
- `.ross-container` - Max-width container
- `.ross-grid-3` - 3-column grid
- `.ross-btn-primary` - Primary button
- `.ross-section-header-center` - Centered section header

---

## 📂 File Structure

```
rosstheme/
├── template-home-business.php          # Business template
├── template-home-creative.php          # Creative template
├── template-home-ecommerce.php         # E-commerce template
├── template-home-minimal.php           # Minimal template
├── template-home-startup.php           # Startup template
├── template-home-restaurant.php        # Restaurant template
├── assets/
│   ├── css/
│   │   └── frontend/
│   │       ├── templates-global.css    # Shared styles
│   │       ├── template-business.css   # Business styles
│   │       ├── template-creative.css   # Creative styles
│   │       ├── template-ecommerce.css  # E-commerce styles
│   │       ├── template-minimal.css    # Minimal styles
│   │       ├── template-startup.css    # Startup styles
│   │       └── template-restaurant.css # Restaurant styles
│   └── js/
│       └── frontend/
│           └── templates.js            # Template interactions
├── inc/
│   ├── core/
│   │   └── asset-loader.php           # Updated with template CSS/JS
│   └── features/
│       └── homepage-templates/
│           └── homepage-manager.php    # Template manager (existing)
└── TEMPLATE_SYSTEM_DOCUMENTATION.md   # This file
```

---

## 🧪 Testing Checklist

### Functionality Tests

- [ ] All 6 templates can be selected in page editor
- [ ] Template CSS loads correctly (check browser DevTools)
- [ ] Template JS loads on template pages only
- [ ] Portfolio filter works (Creative template)
- [ ] FAQ accordion expands/collapses (Startup template)
- [ ] Newsletter form validates email (all templates)
- [ ] Add to cart animation works (E-commerce template)
- [ ] Smooth scroll works for anchor links

### Dynamic Integration Tests

- [ ] Change primary color → templates update
- [ ] Change secondary color → gradients update
- [ ] Change text color → content updates
- [ ] Change container width → layouts adjust
- [ ] Logo upload appears in header (all templates)
- [ ] Footer content appears (all templates)

### Responsive Tests

- [ ] Desktop (1200px+) - All grids display correctly
- [ ] Laptop (1024px) - 4-column grids → 2 columns
- [ ] Tablet (768px) - Most grids → 1 column
- [ ] Mobile (480px) - All content stacks vertically
- [ ] Hero sections responsive (split → stacked)
- [ ] Navigation → mobile menu
- [ ] Forms → stacked fields

### Browser Tests

- [ ] Chrome/Edge (Chromium)
- [ ] Firefox
- [ ] Safari
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

---

## 🐛 Troubleshooting

### CSS Not Loading

**Problem:** Template looks unstyled

**Solution:**
1. Check file exists: `assets/css/frontend/templates-global.css`
2. Clear WordPress cache (if using caching plugin)
3. Hard refresh browser (Ctrl+F5 or Cmd+Shift+R)
4. Check `asset-loader.php` has template CSS enqueue code

### Colors Not Changing

**Problem:** Theme option colors don't update template

**Solution:**
1. Verify `get_option('ross_theme_general_options')` returns array
2. Check dynamic CSS is output in `<head>` (View Source)
3. Ensure CSS uses `var(--ross-primary-color)` custom properties
4. Clear browser cache

### JavaScript Not Working

**Problem:** Portfolio filters or FAQ don't work

**Solution:**
1. Check `templates.js` is enqueued (View Source or DevTools Network tab)
2. Check browser console for errors (F12 → Console)
3. Verify HTML classes match JS selectors (`.filter-btn`, `.faq-question`)
4. Ensure DOM is loaded before JS runs (uses `DOMContentLoaded`)

### Template Not Available in Page Editor

**Problem:** Can't select template

**Solution:**
1. Verify template file exists in theme root
2. Check template header: `Template Name: Homepage - Business`
3. Ensure no PHP errors in template file
4. Check `functions.php` loads homepage manager

---

## 🔮 Future Enhancements

### Planned Features

1. **Template Meta Boxes**
   - Hero section customization per page
   - Section enable/disable toggles
   - Content overrides without editing PHP

2. **Additional Theme Options**
   - Newsletter section settings (MailChimp integration)
   - App download button URLs
   - Business hours display
   - Map embed settings
   - Trust badges upload

3. **Enhanced Admin UI**
   - Template preview images/screenshots
   - Live preview iframe
   - Category filtering (Business, Creative, E-commerce)
   - Template comparison view

4. **Template Builder**
   - Drag-and-drop section ordering
   - Section library (add pre-made sections)
   - Custom color schemes per template
   - Save custom templates

5. **Performance Optimizations**
   - Critical CSS inline for above-fold content
   - Lazy loading images
   - Minified CSS/JS versions
   - Conditional asset loading (only load what's needed)

---

## 📚 Developer API Reference

### Enqueue Template CSS

```php
// In your custom plugin or child theme
function my_custom_template_css() {
    if (is_page_template('template-home-custom.php')) {
        wp_enqueue_style(
            'my-custom-template',
            get_stylesheet_directory_uri() . '/css/template-custom.css',
            array('ross-theme-templates-global'),
            '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'my_custom_template_css');
```

### Add Custom Template

```php
/**
 * Template Name: Homepage - Custom
 * Description: My custom homepage template
 */

get_header();

$general_options = get_option('ross_theme_general_options', array());
$primary_color = $general_options['primary_color'] ?? '#000000';
?>

<main class="ross-homepage-template ross-template-custom">
    <section style="background: <?php echo esc_attr($primary_color); ?>;">
        <div class="ross-container">
            <h1>My Custom Template</h1>
        </div>
    </section>
</main>

<?php get_footer(); ?>
```

### Register Template with Manager

```php
// In inc/features/homepage-templates/homepage-manager.php
$this->templates['custom'] = array(
    'id' => 'custom',
    'title' => 'Custom Template',
    'description' => 'My custom homepage design',
    'template_file' => 'template-home-custom.php',
    'category' => 'other',
    'preview_image' => get_template_directory_uri() . '/assets/images/homepage-templates/custom-preview.jpg',
    'features' => array('Hero', 'Custom Section')
);
```

---

## 📞 Support & Resources

### Documentation Files

- `TEMPLATE_SYSTEM_ANALYSIS.md` - Complete analysis and implementation plan
- `ARCHITECTURE.md` - Theme architecture and design patterns
- `QUICK_START.md` - Quick integration guide
- `E2E-README.md` - End-to-end testing with Playwright

### Code Examples

All templates include inline comments explaining:
- Dynamic option usage
- Responsive design patterns
- Accessibility features
- Security (escaping, sanitization)

### Getting Help

1. Check browser console for JavaScript errors
2. Enable WordPress debug mode: `define('WP_DEBUG', true);`
3. Review template file for PHP errors
4. Check `wp-content/debug.log` for backend errors

---

## ✨ Credits

**Ross Theme Template System v1.0**
- 6 Complete Homepage Templates
- 7 CSS Files (1 global + 6 template-specific)
- 1 JavaScript File (400+ lines of interactions)
- Fully Responsive (Desktop, Tablet, Mobile)
- Dynamic Theme Option Integration
- Modern CSS (Flexbox, Grid, Custom Properties)
- Accessibility Features (ARIA, Focus States, Skip Links)
- Performance Optimized (Conditional Loading, Cache Busting)

---

## 📝 Changelog

### Version 1.0.0 (Current)

**Added:**
- ✅ 6 complete homepage templates (Business, Creative, E-commerce, Minimal, Startup, Restaurant)
- ✅ Global template CSS (templates-global.css - 650+ lines)
- ✅ 6 template-specific CSS files (1,800+ total lines)
- ✅ Frontend JavaScript (templates.js - 400+ lines)
- ✅ Dynamic theme option integration (all templates)
- ✅ Responsive design (4 breakpoints)
- ✅ Conditional asset loading (performance)
- ✅ Portfolio filter functionality
- ✅ FAQ accordion
- ✅ Smooth scrolling
- ✅ Scroll animations
- ✅ Gallery lightbox
- ✅ Newsletter validation
- ✅ Add to cart animations
- ✅ Accessibility features
- ✅ Complete documentation

**Next Release (v1.1 - Planned):**
- Template meta boxes for customization
- Preview images/screenshots
- Enhanced admin UI
- Additional theme options
- Template builder (drag-and-drop)

---

**End of Documentation**

For questions or feature requests, please review the existing documentation files or check the code comments within template files.
