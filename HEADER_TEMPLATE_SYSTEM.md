# Header Template System - Complete Documentation

## 🎯 Overview

The Ross Theme Header Template System provides 5 professionally designed, fully customizable header layouts. Each template is mobile-responsive, features smooth animations, and can be completely customized after application.

## ✨ Features

### Core Capabilities
- ✅ **5 Ready-Made Templates** - Professional designs for different use cases
- ✅ **Full Customization** - Every template can be edited after applying
- ✅ **Automatic Backups** - Settings backed up before template changes
- ✅ **Live Preview** - Preview templates before applying
- ✅ **Responsive Design** - Mobile-first, works on all devices
- ✅ **Sticky Headers** - Configurable sticky behavior
- ✅ **Dark/Light Mode** - Templates support different color schemes
- ✅ **Animation Support** - Smooth transitions and hover effects

## 📐 Available Templates

### 1. Business Classic 💼
**Best For:** Corporate websites, professional services, B2B companies

**Features:**
- Logo positioned left
- Navigation centered
- CTA button right
- Clean, professional appearance
- Sticky header with shadow effect

**Default Colors:**
- Background: `#ffffff` (White)
- Text: `#0b2140` (Dark Blue)
- Accent: `#0b66a6` (Blue)
- Hover: `#084578` (Dark Blue)

---

### 2. Creative Agency 🎨
**Best For:** Design studios, creative agencies, portfolio sites

**Features:**
- Centered logo design
- Stacked layout (logo above navigation)
- Bold typography with uppercase menu
- Dark background with bright accents
- Scroll-up sticky behavior

**Default Colors:**
- Background: `#0c0c0d` (Almost Black)
- Text: `#f3f4f6` (Light Gray)
- Accent: `#E5C902` (Gold)
- Hover: `#ffd633` (Bright Gold)

---

### 3. E-commerce Shop 🛒
**Best For:** Online stores, marketplaces, retail websites

**Features:**
- Compact header design
- Inline search bar
- Cart, wishlist, and account icons
- Sale badge support
- Optional topbar for announcements

**Default Colors:**
- Background: `#ffffff` (White)
- Text: `#1f2937` (Dark Gray)
- Accent: `#dc2626` (Red)
- Hover: `#b91c1c` (Dark Red)

---

### 4. Minimal Modern ✨
**Best For:** Blogs, portfolios, content-focused sites

**Features:**
- Ultra-clean design
- Transparent background with glass effect
- Menu positioned right
- No CTA button by default
- Fade-out on scroll down

**Default Colors:**
- Background: `transparent`
- Text: `#111827` (Charcoal)
- Accent: `#111827`
- Hover: `#6b7280` (Gray)

---

### 5. Transparent Hero 🌅
**Best For:** Landing pages, marketing sites, hero sections

**Features:**
- Absolutely positioned over content
- Transparent initially, solid on scroll
- Text shadow for readability
- Transforms background color when sticky
- Homepage-only option

**Default Colors:**
- Background: `transparent` → `#001946` (on scroll)
- Text: `#ffffff` (White)
- Accent: `#E5C902` (Gold)
- Hover: `#ffd633` (Bright Gold)

## 🛠 How to Use

### Accessing Header Templates

1. Navigate to **WordPress Admin → Ross Theme → Header Options**
2. Click on the **📐 Templates** tab
3. Browse available templates with live previews
4. Click **Preview** to see full template details
5. Click **Apply Template** to activate

### Customizing After Application

After applying a template, you can customize:

**In the Layout & Structure Tab:**
- Container width (Full / Contained)
- Padding and margins
- Sticky behavior
- Header height

**In the Logo & Branding Tab:**
- Upload custom logo
- Set logo width
- Show/hide site title

**In the Navigation Tab:**
- Menu alignment
- Font size and weight
- Active item color
- Hover effects

**In the CTA & Search Tab:**
- Enable/disable search
- CTA button text and URL
- Button colors and styles

**In the Appearance Tab:**
- Background color
- Text color
- Accent colors
- Border styles

### Backup & Restore

**Automatic Backups:**
- Created automatically when applying a new template
- Stores previous template and all settings
- Keeps last 10 backups

**Restore a Backup:**
1. Scroll to the "Header Backups" section
2. Find the backup you want to restore
3. Click **Restore**
4. Confirm the action

**Delete a Backup:**
- Click **Delete** next to any backup
- Confirm deletion

## 💻 Technical Details

### File Structure

```
inc/features/header/
├── header-options.php           # Main options class with AJAX handlers
├── header-functions.php         # Helper functions and display logic
├── header-template-manager.php  # Template loading and management
└── templates/                   # Template configuration files
    ├── business-classic.php
    ├── creative-agency.php
    ├── ecommerce-shop.php
    ├── minimal-modern.php
    └── transparent-hero.php

template-parts/header/
├── header-business-classic.php  # Frontend template rendering
├── header-creative-agency.php
├── header-default.php           # Legacy templates
├── header-centered.php
└── header-transparent.php

inc/admin/
└── header-templates-admin.php   # Admin UI interface
```

### Template Configuration Format

Each template is defined as a PHP array with the following structure:

```php
return array(
    'id' => 'template-id',
    'title' => 'Template Name',
    'description' => 'Template description',
    'icon' => '🎨',
    
    // Layout
    'layout' => 'horizontal', // horizontal, stacked, centered
    'logo_position' => 'left',
    'menu_position' => 'center',
    'cta_position' => 'right',
    
    // Colors
    'bg' => '#ffffff',
    'text' => '#333333',
    'accent' => '#0b66a6',
    'hover' => '#084578',
    
    // Features
    'sticky_enabled' => true,
    'search_enabled' => true,
    
    // CTA
    'cta' => array(
        'enabled' => true,
        'text' => 'Get Started',
        'url' => '#contact',
        // ... more cta settings
    ),
    
    // Mobile
    'mobile' => array(
        'toggle_style' => 'hamburger',
        'animation' => 'slide',
        // ... more mobile settings
    )
);
```

### AJAX Endpoints

The system uses the following AJAX actions:

- `ross_apply_header_template` - Apply a template
- `ross_preview_header_template` - Get template preview HTML
- `ross_restore_header_backup` - Restore from backup
- `ross_delete_header_backup` - Delete a backup

### Hooks & Filters

**Actions:**
```php
do_action('ross_before_header_template_apply', $template_id);
do_action('ross_after_header_template_apply', $template_id);
```

**Filters:**
```php
apply_filters('ross_header_template_defaults', $defaults, $template_id);
apply_filters('ross_header_templates_list', $templates);
```

## 🎨 Creating Custom Templates

### Step 1: Create Template Configuration

Create a new file in `inc/features/header/templates/my-template.php`:

```php
<?php
return array(
    'id' => 'my-custom-template',
    'title' => 'My Template',
    'description' => 'Custom header design',
    'icon' => '⭐',
    
    'bg' => '#ffffff',
    'text' => '#000000',
    'accent' => '#ff0000',
    // ... add all required settings
);
```

### Step 2: Create Frontend Template

Create `template-parts/header/header-my-custom-template.php`:

```php
<?php
// Load template configuration
$template = ross_theme_get_header_template('my-custom-template');
$options = ross_theme_get_header_options();

// Merge defaults with user customizations
// ... your header HTML and CSS
```

### Step 3: Register Template

The template will be automatically discovered and loaded from the templates directory.

## 📱 Responsive Behavior

All templates include:
- Mobile breakpoint at 768px (configurable)
- Hamburger/text menu toggles
- Touch-friendly navigation
- Optimized spacing for mobile
- Collapsible menus with animations

### Mobile Menu Toggle

Templates automatically add mobile menu functionality via JavaScript (loaded from `assets/js/frontend/navigation.js`):

```javascript
// Automatically handles menu toggle
$('.menu-toggle').on('click', function() {
    $('#primary-menu').toggleClass('open');
});
```

## 🔧 Troubleshooting

### Template Not Showing

1. Check if template file exists in both:
   - `inc/features/header/templates/[template-id].php`
   - `template-parts/header/header-[template-id].php`

2. Clear WordPress cache
3. Check for PHP errors in `wp-content/debug.log`

### Customizations Not Saving

1. Verify you clicked **Save Header Settings**
2. Check file permissions on `wp-content` directory
3. Disable caching plugins temporarily

### Sticky Header Not Working

1. Ensure sticky is enabled in template settings
2. Check for CSS conflicts in browser inspector
3. Verify z-index is sufficient (default: 9999)

## 🚀 Performance Optimization

### Best Practices

1. **Use Contained Width** - Better performance than full-width
2. **Optimize Logo Image** - Use WebP format, max 200px width
3. **Minimize Menu Items** - 5-7 items for best UX
4. **Enable Sticky Wisely** - Consider scroll-up behavior instead of always-sticky

### Caching Compatibility

Templates work with:
- ✅ WP Super Cache
- ✅ W3 Total Cache
- ✅ WP Rocket
- ✅ LiteSpeed Cache

## 📊 Browser Support

- ✅ Chrome 60+
- ✅ Firefox 55+
- ✅ Safari 12+
- ✅ Edge 79+
- ✅ iOS Safari 12+
- ✅ Android Chrome 60+

## 🆘 Support

For issues or questions:
1. Check this documentation
2. Review browser console for JavaScript errors
3. Enable WordPress debug mode
4. Check theme version compatibility

---

**Version:** 1.0.0  
**Last Updated:** December 2025  
**Compatibility:** WordPress 5.0+, PHP 7.4+
