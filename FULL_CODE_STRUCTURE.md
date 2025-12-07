# Ross Theme - Complete Code Structure

## 📁 Root Directory Files

### Core WordPress Theme Files
```
style.css                          # Theme stylesheet with metadata (theme name, version, author)
functions.php                      # Main theme loader - requires all modules in specific order
header.php                         # Main header template - orchestrates topbar, announcement, header layouts
footer.php                         # Main footer template - renders selected footer template
front-page.php                     # Homepage template - displays selected homepage template
index.php                          # Default archive template - fallback for all content types
```

### Template Files (Page Templates)
```
template-home-business.php         # Business Professional homepage template
template-home-creative.php         # Creative Agency homepage template  
template-home-ecommerce.php        # E-commerce Shop homepage template
template-home-minimal.php          # Minimal Modern homepage template
template-home-restaurant.php       # Restaurant homepage template
template-home-startup.php          # Startup homepage template
```

### Configuration Files
```
package.json                       # Node.js dependencies (Playwright testing framework)
playwright.config.ts               # Playwright E2E test configuration
```

### Testing & Debug Files
```
debug-social.php                   # Debug script for social icons rendering
test-social-render.php             # Test script for social icons output
test-topbar.php                    # Test script for topbar functionality
```

---

## 📁 inc/ - PHP Backend Logic (Modular Architecture)

### inc/core/ - Theme Foundation
```
inc/core/theme-setup.php           # Theme support registration (menus, post-thumbnails, HTML5)
                                   # Custom image sizes, content width, text domain loading
                                   
inc/core/asset-loader.php          # Enqueues frontend CSS/JS with cache-busting (filemtime)
                                   # Conditional loading of feature-specific assets
```

### inc/admin/ - WordPress Admin Panel
```
inc/admin/admin-pages.php          # Renders admin panel HTML pages for Header/Footer/General
                                   # Contains all HTML form structure, controls, submit buttons
                                   # Displays settings grouped by sections with CSS styling
                                   
inc/admin/settings-api.php         # WordPress Settings API registration (DEPRECATED - moved to feature classes)
                                   
inc/admin/customizer-topbar.php    # WordPress Customizer panel for topbar settings
                                   # 27+ controls (colors, spacing, typography, social icons)
                                   
inc/admin/customizer-enqueuer.php  # Enqueues customizer preview JavaScript
                                   # Loads CSS for customizer UI panels
                                   
inc/admin/ajax-handlers.php        # AJAX endpoint handlers for admin actions (if exists)
```

### inc/features/header/ - Header Feature Module
```
inc/features/header/header-template-manager.php
    # Manages 5 header templates (business-classic, creative-agency, minimal-modern, 
    # ecommerce-shop, transparent-hero)
    # Handles template selection, preview, apply, restore functionality
    # AJAX handlers for template operations

inc/features/header/header-options.php
    # CLASS: RossHeaderOptions (2,132 lines)
    # Purpose: Header settings registration, sanitization, admin UI
    # Settings: 55+ controls (layout, logo upload, navigation, search, sticky header, 
    #           colors, spacing, typography, mobile menu, custom CSS/JS)
    # Methods:
    #   - __construct(): Hooks registration
    #   - register_header_settings(): Settings API registration
    #   - sanitize_header_options(): Input validation and sanitization
    #   - enqueue_header_scripts(): Loads admin JS/CSS for header panel
    #   - ajax_apply_header_template(): Applies selected template
    #   - ajax_restore_header_backup(): Restores previous settings

inc/features/header/header-functions.php
    # Helper functions for header rendering
    # Functions:
    #   - ross_theme_render_topbar(): Outputs topbar HTML
    #   - ross_theme_render_announcement(): Outputs announcement bar
    #   - ross_theme_render_announcement_at($position): Conditional announcement display
    #   - ross_theme_display_header(): Renders selected header template
    #   - ross_theme_render_search_overlay(): Search modal HTML
```

### inc/features/footer/ - Footer Feature Module
```
inc/features/footer/footer-options.php
    # CLASS: RossFooterOptions (3,536 lines - ENHANCED with debugging)
    # Purpose: Footer settings registration, sanitization, template system, AJAX
    # Settings: 100+ controls (4 footer templates, layout, widgets, CTA section,
    #           social icons, copyright, newsletter, colors, spacing, custom CSS/JS)
    # Methods:
    #   - __construct(): Hooks registration + update tracking
    #   - register_footer_settings(): Settings API registration
    #   - sanitize_footer_options(): Input validation with debug logging ✅
    #   - on_footer_options_updated(): Tracks successful saves ✅
    #   - show_settings_saved_notice(): Success notification ✅
    #   - enqueue_footer_scripts(): Loads admin JS/CSS (footer-options.js)
    #   - ajax_get_footer_template_preview(): Returns template preview HTML
    #   - ajax_apply_footer_template(): Applies template and backs up old settings
    #   - ajax_restore_footer_backup(): Restores from backup
    #   - ajax_export_footer_settings(): Exports settings as JSON
    #   - ajax_import_footer_settings(): Imports settings from JSON
    # Database: Stores in wp_options as 'ross_theme_footer_options' (serialized array)

inc/features/footer/footer-functions.php
    # Helper functions for footer rendering
    # Functions:
    #   - ross_theme_display_footer(): Renders selected footer template
    #   - ross_theme_render_footer_cta(): Outputs CTA section
    #   - ross_theme_render_footer_widgets(): Outputs widget areas
    #   - ross_theme_render_footer_social(): Outputs social icons
    #   - ross_theme_render_footer_copyright(): Outputs copyright text
    #   - ross_theme_get_footer_setting($key, $default): Retrieves single footer setting
```

### inc/features/general/ - General Settings Module
```
inc/features/general/general-options.php
    # CLASS: RossGeneralOptions
    # Purpose: Site-wide settings (logo, favicon, colors, typography, layout)
    # Settings: Global color scheme, default fonts, container width, sidebar layout
    # Methods: Similar pattern to header-options.php and footer-options.php
```

### inc/features/homepage-templates/ - Homepage Template System
```
inc/features/homepage-templates/homepage-manager.php
    # CLASS: RossHomepageManager (Singleton pattern)
    # Purpose: Manages 6 homepage templates, handles template selection
    # Methods:
    #   - get_instance(): Singleton accessor
    #   - get_available_templates(): Returns array of template info
    #   - get_active_template(): Returns currently selected template
    #   - set_active_template($template_id): Saves template selection
    #   - render_active_template(): Outputs selected template HTML
    # Templates: business, creative, ecommerce, minimal, restaurant, startup
```

### inc/frontend/ - Dynamic Frontend Generation
```
inc/frontend/dynamic-css.php
    # Function: ross_theme_dynamic_css() - 760+ lines (CRITICAL for customizations)
    # Purpose: Generates inline <style> tag from all customizer/admin settings
    # Output: Injected in <head> via wp_head hook at priority 999
    # Sections:
    #   - Topbar styles (colors, spacing, typography, social icons)
    #   - Header styles (logo size, navigation colors, sticky header)
    #   - Footer styles (background, text colors, widget spacing, CTA, copyright)
    #   - General styles (global colors, fonts, container width)
    #   - Custom CSS from admin panels
    # Uses !important flags to override theme defaults
    # NOTE: If customizations don't appear, check this file first!
```

### inc/utilities/ - Helper Functions
```
inc/utilities/helper-functions.php
    # Shared utility functions across theme
    # Functions:
    #   - ross_theme_get_option($option_name, $key, $default): Safe option retrieval
    #   - ross_sanitize_checkbox($checked): Converts checkbox to 1/0
    #   - ross_theme_get_svg($icon_name): Returns inline SVG icons
    #   - ross_theme_get_reading_time($post_id): Calculates post reading time
    #   - ross_theme_get_primary_color(): Returns theme primary color

inc/utilities/theme-reset-utility.php
    # Admin utility to reset all theme settings to defaults
    # Deletes ross_theme_*_options from database
    # WARNING: Use with caution - cannot be undone!
```

### inc/integrations/ - Third-Party Integrations
```
inc/integrations/woocommerce.php   # WooCommerce support (if exists)
inc/integrations/elementor.php     # Elementor page builder compatibility (if exists)
```

### inc/ Root Level Files (Legacy/Standalone)
```
inc/customizer-footer-social.php   # Legacy customizer social icons (may be redundant)
inc/template-tags-footer-social.php # Social icons template functions
```

---

## 📁 template-parts/ - Reusable Template Components

### template-parts/header/ - Header Template Variants
```
template-parts/header/header-default.php              # Default header layout
template-parts/header/business-classic.php            # Business Professional header
template-parts/header/creative-agency.php             # Creative Agency header (centered logo)
template-parts/header/minimal-modern.php              # Minimal Modern header (simple)
template-parts/header/ecommerce-shop.php              # E-commerce header (cart icon, search)
template-parts/header/transparent-hero.php            # Transparent overlay header (for hero sections)
```

### template-parts/footer/ - Footer Template Variants
```
template-parts/footer/template1.php                   # Footer Template 1 (4-column widget layout)
template-parts/footer/template2.php                   # Footer Template 2 (centered social icons)
template-parts/footer/template3.php                   # Footer Template 3 (newsletter focus)
template-parts/footer/template4.php                   # Footer Template 4 (minimal footer)
```

### template-parts/ Root Level Components
```
template-parts/topbar.php                             # Default topbar component
template-parts/topbar-advanced.php                    # Advanced topbar with more features
template-parts/header-default.php                     # Default header (duplicate?)
```

### template-parts/blog/ - Blog Template Parts
```
template-parts/blog/content.php                       # Standard post content template
template-parts/blog/content-single.php                # Single post full content
template-parts/blog/content-excerpt.php               # Post excerpt for archives
template-parts/blog/content-none.php                  # No posts found message
```

### template-parts/components/ - Reusable UI Components
```
template-parts/components/breadcrumbs.php             # Breadcrumb navigation
template-parts/components/pagination.php              # Numeric pagination
template-parts/components/post-meta.php               # Post date, author, categories
template-parts/components/share-buttons.php           # Social sharing buttons
```

---

## 📁 templates/ - Custom Page Templates

```
templates/page-fullwidth.php                          # Full-width page (no sidebar)
templates/page-sidebar-left.php                       # Page with left sidebar
templates/page-sidebar-right.php                      # Page with right sidebar
templates/template-custom.php                         # Custom page template example
```

---

## 📁 assets/ - Static Assets (CSS, JS, Images)

### assets/css/ - Stylesheets

#### assets/css/admin/ - Admin Panel Styles
```
assets/css/admin/admin-main.css                       # Main admin panel styling (settings pages)
                                                      # Styles for ross-admin-header, ross-admin-content,
                                                      # ross-settings-section, ross-submit-btn
                                                      # Enhanced with z-index fixes for submit button ✅

assets/css/admin/customizer-topbar.css                # Customizer UI panel styles for topbar section
assets/css/admin/customizer-header.css                # Customizer UI panel styles for header section
assets/css/admin/customizer-footer.css                # Customizer UI panel styles for footer section
```

#### assets/css/frontend/ - Frontend Styles
```
assets/css/frontend/base.css                          # Base styles (reset, typography, layout grid)
assets/css/frontend/header.css                        # Header component styles (all templates)
assets/css/frontend/topbar.css                        # Topbar component styles
assets/css/frontend/footer.css                        # Footer component styles (all templates)
assets/css/frontend/navigation.css                    # Main navigation menu styles (desktop + mobile)
assets/css/frontend/blog.css                          # Blog archive and single post styles
assets/css/frontend/widgets.css                       # Sidebar widget styles
assets/css/frontend/responsive.css                    # Media queries for all breakpoints
```

### assets/js/ - JavaScript Files

#### assets/js/admin/ - Admin Panel Scripts
```
assets/js/admin/footer-options.js                     # Footer admin panel interactions (957 lines - ENHANCED ✅)
                                                      # Features:
                                                      #   - Template preview/apply/restore
                                                      #   - Color picker initialization
                                                      #   - Submit button click handler with logging ✅
                                                      #   - Form submission debugging ✅
                                                      #   - AJAX success/error monitoring ✅
                                                      #   - Button state diagnostics ✅
                                                      # Dependencies: jQuery, wp-color-picker

assets/js/admin/header-options.js                     # Header admin panel interactions
                                                      # Similar functionality to footer-options.js

assets/js/admin/general-options.js                    # General settings admin panel interactions

assets/js/admin/customizer-topbar-preview.js          # Topbar live preview in customizer
                                                      # Binds to wp.customize() API for instant updates
                                                      # Updates CSS properties without page reload

assets/js/admin/customizer-header-preview.js          # Header live preview in customizer
assets/js/admin/customizer-footer-preview.js          # Footer live preview in customizer
```

#### assets/js/frontend/ - Frontend Scripts
```
assets/js/frontend/navigation.js                      # Main navigation functionality (430 lines)
                                                      # Features:
                                                      #   - Mobile menu toggle
                                                      #   - Dropdown menu interactions
                                                      #   - Sticky header on scroll
                                                      #   - Search overlay toggle
                                                      #   - Accessibility (keyboard navigation)

assets/js/frontend/search.js                          # Search overlay functionality
assets/js/frontend/smooth-scroll.js                   # Smooth scrolling for anchor links
assets/js/frontend/animations.js                      # Scroll animations (fade-in, slide-up)
```

### assets/images/ - Image Assets
```
assets/images/logo.png                                # Default theme logo
assets/images/favicon.ico                             # Default favicon
assets/images/placeholder.jpg                         # Placeholder image for posts without featured image
```

---

## 📁 tests/ - End-to-End Tests (Playwright)

```
tests/cta-admin.spec.ts                               # Tests for CTA section admin controls
tests/footer-admin.spec.ts                            # Tests for footer settings page
tests/footer-templates.spec.ts                        # Tests for footer template switching
tests/header-admin.spec.ts                            # Tests for header settings (23 tests)
tests/header-templates.spec.ts                        # Tests for header template switching
tests/topbar-admin.spec.ts                            # Tests for topbar customizer controls
tests/homepage-templates.spec.ts                      # Tests for homepage template selection
```

Test Configuration:
- Framework: Playwright (TypeScript)
- Config: `playwright.config.ts`
- Environment: http://theme.dev (local WordPress site)
- Authentication: Uses admin credentials from environment variables

---

## 📁 languages/ - Translation Files

```
languages/rosstheme.pot                               # Portable Object Template (translation template)
languages/en_US.po                                    # English translation file
languages/en_US.mo                                    # Compiled English translation
```

---

## 📁 images/ - Theme Background Images

```
images/backgrounds/hero-1.jpg                         # Default hero background
images/backgrounds/hero-2.jpg                         # Alternative hero background
images/backgrounds/cta-bg.jpg                         # CTA section background
images/sprites/social-icons.png                       # Social icons sprite sheet (if used)
```

---

## 📁 js/ - Additional JavaScript (Legacy?)

```
js/custom.js                                          # Custom JavaScript (may be legacy)
```

Note: This folder may be redundant with `assets/js/` structure.

---

## 📁 .github/ - GitHub Configuration

```
.github/workflows/                                    # GitHub Actions workflows (CI/CD)
.github/copilot-instructions.md                       # GitHub Copilot custom instructions for this project
```

---

## 📁 .vscode/ - VS Code Configuration

```
.vscode/settings.json                                 # Workspace-specific VS Code settings
.vscode/extensions.json                               # Recommended VS Code extensions
.vscode/launch.json                                   # Debug configurations
```

---

## 📄 Documentation Files (60+ Markdown Files)

### Architecture & Guides
```
ARCHITECTURE.md                                       # System architecture diagrams and component flow
QUICK_START.md                                        # 5-minute integration guide
THEME_DEBUG_GUIDE.md                                  # Complete debugging guide (1,000+ lines) ✅
COMPUTER_REFERENCE.md                                 # Complete computer reference (900+ lines) ✅
DOCUMENTATION_INDEX.md                                # Index of all documentation files
FILES_MANIFEST.md                                     # List of all theme files with descriptions
```

### Feature-Specific Documentation
```
TOPBAR_SETTINGS_GUIDE.md                              # All 27+ topbar customizer settings
TOPBAR_EXAMPLES.js                                    # Ready-to-use topbar code patterns
TOPBAR_QUICK_START.md                                 # Topbar setup guide
TOPBAR_ENHANCEMENTS_COMPLETE.md                       # Topbar feature completion report
ADVANCED_TOPBAR_GUIDE.md                              # Advanced topbar customization

HEADER_FOOTER_IMPLEMENTATION.md                       # Header/footer template system
DYNAMIC_HEADER_FOOTER_GUIDE.md                        # Dynamic template switching guide

FOOTER_TEMPLATE_SYSTEM.md                             # Footer template architecture
FOOTER_IMPLEMENTATION_COMPLETE.md                     # Footer feature completion report
FOOTER_TEMPLATES_CHECKLIST.md                         # Footer template verification checklist
FOOTER_TESTING_GUIDE.md                               # Footer testing procedures
FOOTER_ADMIN_UI_ENHANCEMENT.md                        # Footer admin UI improvements

HOMEPAGE_TEMPLATES_GUIDE.md                           # Homepage template system
HOMEPAGE_IMPLEMENTATION_SUMMARY.md                    # Homepage feature summary

CTA_IMPLEMENTATION_SUMMARY.md                         # CTA section implementation
CTA_ADMIN_REFERENCE.md                                # CTA admin controls reference
CTA_ENHANCEMENTS_COMPLETE.md                          # CTA feature completion
CTA_TESTING_CHECKLIST.md                              # CTA testing procedures
CTA_BEFORE_AFTER.md                                   # CTA improvements comparison
```

### Testing & Debugging Documentation
```
E2E-README.md                                         # Playwright E2E testing guide
UPLOAD_TESTING_GUIDE.md                               # Logo/image upload testing
LOGO_UPLOAD_TROUBLESHOOTING.md                        # Logo upload issue fixes

SOCIAL_ICONS_V2_COMPLETE.md                           # Social icons v2 implementation
SOCIAL_ICONS_FRONTEND_FIX.md                          # Social icons frontend fixes
SOCIAL_ICONS_QUICK_FIX.md                             # Social icons quick fixes
SOCIAL_ICONS_QUICK_START.md                           # Social icons setup guide

COPYRIGHT_CONDITIONAL_DISPLAY_COMPLETE.md             # Copyright display logic
```

### Implementation Reports
```
IMPLEMENTATION_SUMMARY.md                             # Overall theme implementation summary
TEMPLATE_IMPLEMENTATION_SUMMARY.md                    # Template system summary
TEMPLATE_SYSTEM_ANALYSIS.md                           # Template system analysis
TEMPLATE_SYSTEM_DOCUMENTATION.md                      # Template system docs
COMPLETION_CHECKLIST.md                               # Feature completion checklist
COMPLETION_REPORT.md                                  # Project completion report
ADMIN_UI_IMPROVEMENTS.md                              # Admin UI enhancement summary
```

### Business Templates Documentation
```
BUSINESS_PROFESSIONAL_TEMPLATE.md                     # Business template guide
BUSINESS_PROFESSIONAL_IMPLEMENTATION.md               # Business template implementation
BUSINESS_PROFESSIONAL_QUICK_START.md                  # Business template quick start
BUSINESS_PROFESSIONAL_VISUAL_MAP.md                   # Business template visual guide
```

---

## 🔄 Data Flow Summary

### Settings Save Flow (Footer Example)
```
1. User changes setting in admin panel (inc/admin/admin-pages.php)
2. User clicks submit button
3. JavaScript logs click event (assets/js/admin/footer-options.js) ✅
4. Form submits to WordPress Settings API
5. WordPress calls sanitize callback (inc/features/footer/footer-options.php::sanitize_footer_options()) ✅
6. Input validation runs with debug logging ✅
7. Sanitized data saved to wp_options table as 'ross_theme_footer_options'
8. Update hook fires (on_footer_options_updated()) ✅
9. Success notice displayed (show_settings_saved_notice()) ✅
10. Dynamic CSS regenerates on next page load (inc/frontend/dynamic-css.php)
```

### Frontend Rendering Flow
```
1. User visits page
2. header.php loads
3. ross_theme_render_announcement_at('above_header') called
4. ross_theme_render_topbar() called
5. ross_theme_display_header() called
6. Selected header template loaded from template-parts/header/
7. Main content renders
8. footer.php loads
9. ross_theme_display_footer() called
10. Selected footer template loaded from template-parts/footer/
11. ross_theme_dynamic_css() outputs <style> tag in <head> at priority 999
12. Frontend CSS/JS loaded (assets/css/frontend/, assets/js/frontend/)
```

---

## 🎯 Key File Priorities for Development

### ⭐⭐⭐⭐⭐ Critical Files (Touch these most often)
1. `inc/features/footer/footer-options.php` - Footer settings logic
2. `inc/features/header/header-options.php` - Header settings logic
3. `inc/frontend/dynamic-css.php` - All customization output
4. `assets/js/admin/footer-options.js` - Footer admin interactions
5. `functions.php` - Module loading order

### ⭐⭐⭐⭐ Important Files (Modify for features)
6. `inc/admin/admin-pages.php` - Admin UI HTML
7. `template-parts/footer/template*.php` - Footer templates
8. `template-parts/header/*.php` - Header templates
9. `assets/css/frontend/*.css` - Frontend styling
10. `inc/features/homepage-templates/homepage-manager.php` - Homepage system

### ⭐⭐⭐ Supporting Files (Modify occasionally)
11. `inc/core/asset-loader.php` - Add new CSS/JS files
12. `inc/utilities/helper-functions.php` - Add utility functions
13. `assets/js/frontend/navigation.js` - Navigation behavior
14. `tests/*.spec.ts` - Add new tests

### ⭐⭐ Rarely Modified Files
15. `inc/core/theme-setup.php` - Only for new theme features
16. `style.css` - Only for version bumps
17. `languages/*.pot` - Only for translations

---

## 🔧 Development Patterns Used

### 1. Modular OOP Classes
Each feature (header, footer, general) uses a class:
```php
class RossFooterOptions {
    public function __construct() {
        add_action('admin_init', array($this, 'register_footer_settings'));
        add_action('wp_ajax_ross_apply_footer_template', array($this, 'ajax_apply_footer_template'));
    }
}
new RossFooterOptions(); // Initialize
```

### 2. Settings Storage Pattern
- Settings stored in `wp_options` table as serialized arrays
- Access via: `get_option('ross_theme_footer_options', array())`
- Always check `is_array()` before accessing keys

### 3. Dynamic CSS Generation
- All settings output as inline CSS in `<head>` via `wp_head` hook
- Uses `!important` to override defaults
- Located in `inc/frontend/dynamic-css.php`

### 4. Template System
- Multiple layouts for header/footer/homepage
- Selection stored in database
- Rendering via helper functions (ross_theme_display_footer())

### 5. AJAX Pattern
- Preview templates before applying
- Backup/restore functionality
- Export/import settings as JSON

---

## 📊 File Count Summary

- **PHP Files**: ~50+ files (core, admin, features, templates)
- **JavaScript Files**: ~15+ files (admin + frontend)
- **CSS Files**: ~15+ files (admin + frontend)
- **Template Files**: ~25+ files (header, footer, homepage variants)
- **Test Files**: ~10+ Playwright test specs
- **Documentation Files**: 60+ Markdown files
- **Total Lines of Code**: ~15,000+ lines (excluding documentation)

---

## 🎨 Naming Conventions

### PHP Functions
```
ross_theme_{action}               # Global functions (snake_case)
ross_{component}_{action}         # Component-specific functions
```

### PHP Classes
```
Ross{Feature}Options              # Feature classes (PascalCase)
RossHomepageManager               # Manager classes
```

### CSS Classes
```
.site-topbar                      # Component root (kebab-case)
.topbar-inner                     # Component child
.header-default                   # Template variant
```

### JavaScript Files
```
{feature}-options.js              # Admin panel scripts
{feature}-preview.js              # Customizer preview scripts
{component}.js                    # Frontend functionality
```

### Database Options
```
ross_theme_{feature}_options      # Settings arrays
ross_theme_{feature}_backup_{timestamp}  # Backup options
```

---

## 🚀 Quick File Lookup Guide

**Need to change footer colors?**
→ `inc/features/footer/footer-options.php` (register setting)
→ `inc/admin/admin-pages.php` (add control HTML)
→ `inc/frontend/dynamic-css.php` (output CSS)

**Need to add header template?**
→ Create `template-parts/header/my-template.php`
→ Register in `inc/features/header/header-template-manager.php`

**Frontend not updating?**
→ Check `inc/frontend/dynamic-css.php` first!
→ Hard refresh browser (Ctrl+F5)
→ Check `wp_options` table in database

**Settings not saving?**
→ Enable WP_DEBUG in wp-config.php
→ Check browser console (F12)
→ Check wp-content/debug.log
→ Verify nonce and sanitization callback

**Need to add homepage template?**
→ Create `template-home-{name}.php` in root
→ Register in `inc/features/homepage-templates/homepage-manager.php`

---

## 📝 Notes

- ✅ = Recently enhanced with debugging/fixes
- All settings use WordPress Settings API (not theme_mods)
- Live preview uses Customizer API with `transport => 'postMessage'`
- Template system allows backup/restore of previous settings
- All user inputs sanitized before saving
- All outputs escaped before rendering
- Follows WordPress Coding Standards

---

*This structure map is current as of December 7, 2025*
*For debugging workflows, see: THEME_DEBUG_GUIDE.md*
*For development commands, see: COMPUTER_REFERENCE.md*
