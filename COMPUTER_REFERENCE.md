# Ross Theme - Complete Computer Reference & Development Manual

**Version:** 5.0  
**Last Updated:** December 7, 2025  
**Environment:** Windows 10/11 + XAMPP + WordPress  

---

## 🖥️ System Requirements & Environment

### Required Software

| Software | Version | Purpose | Download |
|----------|---------|---------|----------|
| **XAMPP** | 8.0+ | Local server (Apache + MySQL + PHP) | https://www.apachefriends.org |
| **PHP** | 7.4+ | Server-side scripting | Included in XAMPP |
| **MySQL** | 5.7+ | Database | Included in XAMPP |
| **WordPress** | 5.0+ | CMS Platform | https://wordpress.org |
| **Node.js** | 16+ | NPM packages & testing | https://nodejs.org |
| **Git** | Latest | Version control | https://git-scm.com |
| **VS Code** | Latest | Code editor (recommended) | https://code.visualstudio.com |

### Your Current Setup

```
Local URL: http://theme.dev
Installation Path: c:\xampp\htdocs\theme.dev\
Theme Path: c:\xampp\htdocs\theme.dev\wp-content\themes\rosstheme5\rosstheme\
Database: MySQL via phpMyAdmin
Admin URL: http://theme.dev/wp-admin
Admin User: admin
Admin Pass: password
```

---

## 📁 Complete Directory Reference

### Root Directory Structure

```
c:\xampp\htdocs\theme.dev\wp-content\themes\rosstheme5\rosstheme\
│
├── 📄 functions.php                      # Theme loader (loads all modules)
├── 📄 style.css                          # Theme metadata + base styles
├── 📄 header.php                         # Header template (calls header templates)
├── 📄 footer.php                         # Footer template (calls footer templates)
├── 📄 front-page.php                     # Homepage template
├── 📄 index.php                          # Fallback template
├── 📄 screenshot.png                     # Theme preview image
│
├── 📦 package.json                       # NPM dependencies
├── 📦 playwright.config.ts               # E2E test configuration
│
├── 📂 inc/                               # PHP includes (all backend logic)
│   ├── 📂 core/                          # Core theme functionality
│   │   ├── theme-setup.php               # WordPress theme support
│   │   └── asset-loader.php              # CSS/JS enqueue system
│   │
│   ├── 📂 admin/                         # Admin panel pages
│   │   ├── admin-pages.php               # Main admin UI (Header/Footer/General pages)
│   │   ├── settings-api.php              # Settings API helpers
│   │   ├── customizer-topbar.php         # Topbar customizer panel
│   │   ├── customizer-enqueuer.php       # Customizer asset loader
│   │   └── announcement-admin.php        # Announcement bar admin
│   │
│   ├── 📂 features/                      # Feature modules (main functionality)
│   │   ├── 📂 header/
│   │   │   ├── header-options.php        # Header settings (2,132 lines, 55+ controls)
│   │   │   ├── header-functions.php      # Header rendering helpers
│   │   │   └── header-template-manager.php # Header template system
│   │   │
│   │   ├── 📂 footer/
│   │   │   ├── footer-options.php        # Footer settings (3,536 lines, 100+ controls)
│   │   │   ├── footer-functions.php      # Footer rendering helpers
│   │   │   └── 📂 templates/             # Footer template definitions
│   │   │       ├── business-professional.php
│   │   │       ├── ecommerce.php
│   │   │       ├── creative-agency.php
│   │   │       └── minimal-modern.php
│   │   │
│   │   ├── 📂 general/
│   │   │   └── general-options.php       # Site-wide settings (logo, favicon)
│   │   │
│   │   └── 📂 homepage-templates/
│   │       └── homepage-manager.php      # Homepage template system
│   │
│   ├── 📂 frontend/                      # Frontend rendering
│   │   └── dynamic-css.php               # Generates inline CSS (760+ lines)
│   │
│   ├── 📂 utilities/                     # Helper functions
│   │   ├── helper-functions.php          # Utility functions
│   │   └── theme-reset-utility.php       # Reset settings tool
│   │
│   ├── 📂 integrations/                  # Third-party integrations
│   │
│   ├── customizer-footer-social.php      # Footer social icons customizer
│   └── template-tags-footer-social.php   # Footer social rendering
│
├── 📂 template-parts/                    # Reusable template components
│   ├── 📂 header/                        # Header template variants
│   │   ├── header-business-classic.php   # Traditional header
│   │   ├── header-creative-agency.php    # Bold creative header
│   │   ├── header-minimal-modern.php     # Clean minimal header ⭐ NEW
│   │   ├── header-ecommerce-shop.php     # E-commerce 3-tier header ⭐ NEW
│   │   ├── header-transparent-hero.php   # Transparent overlay header ⭐ NEW
│   │   ├── header-default.php            # Fallback header
│   │   ├── header-centered.php           # Centered navigation
│   │   ├── header-minimal.php            # Old minimal variant
│   │   ├── header-modern.php             # Modern variant
│   │   ├── header-transparent.php        # Old transparent
│   │   └── header-search.php             # Search form component
│   │
│   ├── 📂 footer/                        # Footer template variants
│   │   ├── footer-business-professional.php # 4-column professional footer
│   │   ├── footer-ecommerce.php          # Newsletter + links footer
│   │   ├── footer-creative-agency.php    # Bold dark footer
│   │   └── footer-minimal-modern.php     # Clean minimal footer
│   │
│   ├── 📂 components/                    # Reusable UI components
│   │   ├── topbar.php                    # Top bar component
│   │   └── announcement.php              # Announcement bar
│   │
│   └── 📂 blog/                          # Blog components
│
├── 📂 templates/                         # Custom page templates
│   ├── page-fullwidth.php                # Full-width page
│   ├── page-sidebar-left.php             # Left sidebar page
│   └── page-sidebar-right.php            # Right sidebar page
│
├── 📂 assets/                            # Static assets (CSS, JS, images)
│   ├── 📂 css/
│   │   ├── 📂 admin/                     # Admin panel styles
│   │   │   ├── admin-main.css            # Main admin styling
│   │   │   ├── footer-styling-admin.css  # Footer admin UI
│   │   │   ├── footer-template-ui.css    # Footer template selector
│   │   │   ├── social-icons-ui.css       # Social icons manager UI
│   │   │   └── [more admin CSS files]
│   │   │
│   │   └── 📂 frontend/                  # Frontend styles
│   │       ├── base.css                  # Base theme styles
│   │       ├── header.css                # Header-specific styles
│   │       ├── footer.css                # Footer-specific styles
│   │       ├── front-page.css            # Homepage styles
│   │       ├── templates-global.css      # Homepage template base
│   │       ├── template-business.css     # Business template styles
│   │       ├── template-creative.css     # Creative template styles
│   │       ├── template-ecommerce.css    # E-commerce template styles
│   │       ├── template-minimal.css      # Minimal template styles
│   │       ├── template-restaurant.css   # Restaurant template styles
│   │       └── template-startup.css      # Startup template styles
│   │
│   ├── 📂 js/
│   │   ├── 📂 admin/                     # Admin scripts
│   │   │   ├── footer-options.js         # Footer admin UI (957 lines)
│   │   │   ├── header-options.js         # Header admin UI
│   │   │   ├── general-options.js        # General admin UI
│   │   │   ├── social-icons-manager.js   # Social icons interface
│   │   │   ├── footer-template-selector.js # Template picker
│   │   │   ├── media-uploader.js         # Media library integration
│   │   │   ├── reset-settings.js         # Settings reset handler
│   │   │   └── [more admin JS files]
│   │   │
│   │   └── 📂 frontend/                  # Frontend scripts
│   │       ├── navigation.js             # Header navigation (430 lines)
│   │       ├── search.js                 # Search overlay
│   │       └── templates.js              # Homepage template interactions
│   │
│   └── 📂 images/                        # Theme images
│       ├── 📂 backgrounds/               # Background images
│       └── 📂 sprites/                   # Icon sprites
│
├── 📂 tests/                             # E2E automated tests
│   ├── header-admin.spec.ts              # Header tests (23 tests) ⭐ NEW
│   ├── footer-admin.spec.ts              # Footer tests
│   └── cta-admin.spec.ts                 # CTA tests
│
├── 📂 languages/                         # Translation files
│
├── 📂 .github/                           # GitHub configuration
│   └── copilot-instructions.md           # AI coding assistant instructions
│
├── 📂 .vscode/                           # VS Code workspace settings
│
└── 📂 [DOCUMENTATION]/                   # 60+ markdown files
    ├── THEME_DEBUG_GUIDE.md              # Complete debug reference ⭐ NEW
    ├── ARCHITECTURE.md                   # System architecture
    ├── QUICK_START.md                    # 5-minute setup
    ├── HEADER_PHASE5_COMPLETE.md         # Header system complete docs
    ├── FOOTER_IMPLEMENTATION_COMPLETE.md # Footer system complete docs
    ├── TOPBAR_SETTINGS_GUIDE.md          # Topbar customization
    ├── E2E-README.md                     # Testing guide
    └── [50+ more documentation files]
```

---

## 🔧 Development Tools & Commands

### PowerShell Commands (Windows)

#### Navigate to Theme Directory
```powershell
cd c:\xampp\htdocs\theme.dev\wp-content\themes\rosstheme5\rosstheme
```

#### Start XAMPP Services
```powershell
# Start Apache
c:\xampp\apache_start.bat

# Start MySQL
c:\xampp\mysql_start.bat

# Or use XAMPP Control Panel GUI
c:\xampp\xampp-control.exe
```

#### NPM Commands
```powershell
# Install dependencies (first time only)
npm install

# Install Playwright browsers
npm run playwright:install

# Run all tests
npm run test:e2e

# Run tests in headed mode (see browser)
npm run test:e2e:headed

# Run specific test file
npx playwright test tests/header-admin.spec.ts

# Debug tests
npm run test:e2e:debug

# Run tests for specific browser
npx playwright test --project=chromium
npx playwright test --project=firefox
```

#### Git Commands
```powershell
# Check status
git status

# View changes
git diff

# Stage all changes
git add .

# Commit changes
git commit -m "Your commit message"

# Push to GitHub
git push origin main

# View commit history
git log --oneline

# Create new branch
git checkout -b feature-name

# Switch branches
git checkout main
```

#### File Operations
```powershell
# List files
dir

# Create directory
mkdir new-folder

# Copy file
copy source.php destination.php

# Move/rename file
move old-name.php new-name.php

# Delete file
del filename.php

# View file contents
type filename.php

# Search in files
Select-String -Path "*.php" -Pattern "search-term"
```

---

## 🗄️ Database Reference

### Database Tables Used

```
wp_options                      # Settings storage
├── ross_theme_header_options   # Header settings (serialized array)
├── ross_theme_footer_options   # Footer settings (serialized array)
├── ross_theme_general_options  # General settings (serialized array)
├── ross_theme_topbar_options   # Topbar settings (serialized array)
└── ross_theme_footer_backups   # Footer template backups

wp_posts                        # Pages, posts, custom post types
wp_postmeta                     # Post metadata
wp_users                        # User accounts
wp_usermeta                     # User metadata
```

### Access Database via phpMyAdmin

**URL:** http://localhost/phpmyadmin  
**Username:** root  
**Password:** (blank)

### SQL Queries for Debugging

```sql
-- View all Ross theme options
SELECT option_name, LENGTH(option_value) as size 
FROM wp_options 
WHERE option_name LIKE 'ross_theme_%' 
ORDER BY option_name;

-- View specific option (pretty print)
SELECT option_name, option_value 
FROM wp_options 
WHERE option_name = 'ross_theme_header_options';

-- Check if option exists
SELECT COUNT(*) as exists 
FROM wp_options 
WHERE option_name = 'ross_theme_header_options';

-- Delete option (reset to defaults)
DELETE FROM wp_options 
WHERE option_name = 'ross_theme_header_options';

-- View footer template backups
SELECT option_name, option_value 
FROM wp_options 
WHERE option_name LIKE 'ross_footer_backup_%' 
ORDER BY option_name DESC 
LIMIT 10;

-- Check for corrupted options
SELECT option_name, option_value 
FROM wp_options 
WHERE option_name LIKE 'ross_theme_%' 
AND (option_value IS NULL OR option_value = '' OR option_value = 'a:0:{}');

-- View all pages
SELECT ID, post_title, post_name, post_status 
FROM wp_posts 
WHERE post_type = 'page' 
ORDER BY post_title;

-- Find admin user
SELECT ID, user_login, user_email 
FROM wp_users 
WHERE user_login = 'admin';
```

### Backup & Restore

```sql
-- Export specific tables
mysqldump -u root wp_theme_dev wp_options > options_backup.sql
mysqldump -u root wp_theme_dev wp_posts > posts_backup.sql

-- Import backup
mysql -u root wp_theme_dev < full_backup.sql

-- Full database backup
mysqldump -u root wp_theme_dev > full_backup.sql
```

---

## 🎨 WordPress Admin Reference

### Admin URLs

```
Dashboard:         http://theme.dev/wp-admin/
Theme Settings:    http://theme.dev/wp-admin/admin.php?page=ross-theme-settings
Header Settings:   http://theme.dev/wp-admin/admin.php?page=ross-theme-header
Footer Settings:   http://theme.dev/wp-admin/admin.php?page=ross-theme-footer
General Settings:  http://theme.dev/wp-admin/admin.php?page=ross-theme-general
Customizer:        http://theme.dev/wp-admin/customize.php
Widgets:           http://theme.dev/wp-admin/widgets.php
Menus:             http://theme.dev/wp-admin/nav-menus.php
Pages:             http://theme.dev/wp-admin/edit.php?post_type=page
Posts:             http://theme.dev/wp-admin/edit.php
Media Library:     http://theme.dev/wp-admin/upload.php
Plugins:           http://theme.dev/wp-admin/plugins.php
Themes:            http://theme.dev/wp-admin/themes.php
Users:             http://theme.dev/wp-admin/users.php
```

### WordPress Configuration Files

```
wp-config.php           # Main configuration (DB credentials, debug mode)
.htaccess               # Apache rewrite rules (permalinks)
wp-content/debug.log    # Debug log file (if WP_DEBUG_LOG enabled)
```

### Enable Debug Mode

Edit `c:\xampp\htdocs\theme.dev\wp-config.php`:

```php
// Add before "That's all, stop editing!"

// Enable debug mode
define('WP_DEBUG', true);

// Log errors to wp-content/debug.log
define('WP_DEBUG_LOG', true);

// Hide errors from screen (security)
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);

// Use unminified CSS/JS files
define('SCRIPT_DEBUG', true);

// Enable WordPress memory limit
define('WP_MEMORY_LIMIT', '256M');
```

---

## 🧪 Testing Reference

### Test Files Location

```
tests/
├── header-admin.spec.ts        # Header admin & frontend (23 tests)
├── footer-admin.spec.ts        # Footer admin panel
└── cta-admin.spec.ts           # CTA functionality
```

### Test Commands

```powershell
# Run all tests
npm run test:e2e

# Run specific test file
npx playwright test tests/header-admin.spec.ts

# Run specific test by name
npx playwright test -g "should load header settings page"

# Run in headed mode (see browser)
npm run test:e2e:headed

# Debug mode (step through tests)
npm run test:e2e:debug

# Run only failed tests
npx playwright test --last-failed

# Generate HTML report
npx playwright show-report
```

### Test Environment Variables

Create `.env` file in theme root:

```env
ADMIN_URL=http://theme.dev/wp-admin
ADMIN_USER=admin
ADMIN_PASS=password
SITE_URL=http://theme.dev
```

---

## 🔍 Browser Developer Tools Reference

### Chrome DevTools Shortcuts

| Shortcut | Action |
|----------|--------|
| `F12` | Open DevTools |
| `Ctrl + Shift + C` | Inspect element |
| `Ctrl + Shift + J` | Open Console |
| `Ctrl + Shift + I` | Open DevTools |
| `Ctrl + Shift + M` | Toggle device toolbar (mobile view) |
| `Ctrl + Shift + P` | Command palette |
| `Ctrl + F` | Find in file |
| `Ctrl + Shift + F` | Search all files |

### DevTools Panels

**Elements Tab:**
- View HTML structure
- Inspect CSS (computed styles)
- Edit HTML/CSS live
- See box model

**Console Tab:**
- View JavaScript errors
- Run JavaScript code
- Log debugging info
- Test functions

**Network Tab:**
- Monitor requests (CSS, JS, AJAX)
- Check file sizes
- View response times
- Debug 404 errors

**Sources Tab:**
- Set JavaScript breakpoints
- Step through code
- View variables
- Debug minified code

**Performance Tab:**
- Measure page load time
- Identify slow operations
- Profile JavaScript execution

**Application Tab:**
- View cookies
- Check localStorage
- Inspect service workers

### Console Commands for Debugging

```javascript
// Check if jQuery loaded
console.log(typeof jQuery);  // Should be "function"

// Check WordPress localized data
console.log(window.rossHeaderOptions);

// Check if function exists
console.log(typeof RossHeaderNavigation);

// Test color picker
console.log(typeof $.fn.wpColorPicker);

// View all window properties
console.dir(window);

// Check form data
$('form.ross-settings-form').serialize();

// Count form fields
$('form.ross-settings-form input').length;

// Check for errors
console.error('Test error');
console.warn('Test warning');

// Clear console
console.clear();
```

---

## 📝 Code Editor (VS Code) Reference

### Recommended Extensions

```
PHP:
- PHP Intelephense (bmewburn.vscode-intelephense-client)
- PHP Debug (xdebug.php-debug)
- WordPress Snippets (wordpresstoolbox.wordpress-toolbox)

JavaScript:
- ESLint (dbaeumer.vscode-eslint)
- JavaScript (ES6) code snippets (xabikos.JavaScriptSnippets)

CSS:
- CSS Peek (pranaygp.vscode-css-peek)
- Autoprefixer (mrmlnc.vscode-autoprefixer)

General:
- Prettier - Code formatter (esbenp.prettier-vscode)
- GitLens (eamonn.gitlens)
- Path Intellisense (christian-kohler.path-intellisense)
- Auto Rename Tag (formulahendry.auto-rename-tag)
- Bracket Pair Colorizer (CoenraadS.bracket-pair-colorizer-2)
```

### VS Code Shortcuts

| Shortcut | Action |
|----------|--------|
| `Ctrl + P` | Quick file open |
| `Ctrl + Shift + P` | Command palette |
| `Ctrl + F` | Find in file |
| `Ctrl + H` | Find and replace |
| `Ctrl + Shift + F` | Find in all files |
| `Ctrl + G` | Go to line number |
| `Ctrl + /` | Toggle comment |
| `Ctrl + D` | Select next occurrence |
| `Alt + Up/Down` | Move line up/down |
| `Ctrl + Shift + K` | Delete line |
| `Ctrl + Shift + L` | Select all occurrences |
| `Ctrl + B` | Toggle sidebar |
| `Ctrl + J` | Toggle terminal |
| `F2` | Rename symbol |

### VS Code Settings for Theme Development

Create `.vscode/settings.json`:

```json
{
  "files.associations": {
    "*.php": "php"
  },
  "php.validate.enable": true,
  "php.validate.executablePath": "c:\\xampp\\php\\php.exe",
  "emmet.includeLanguages": {
    "php": "html"
  },
  "editor.formatOnSave": true,
  "editor.defaultFormatter": "esbenp.prettier-vscode",
  "editor.tabSize": 4,
  "files.exclude": {
    "**/node_modules": true,
    "**/.git": true
  }
}
```

---

## 🛠️ Debugging Workflow Quick Reference

### 1. Check if Feature is Loading

```php
// Add to functions.php temporarily
add_action('wp_head', function() {
    echo '<!-- Theme loaded -->';
    error_log('Theme loaded successfully');
});
```

### 2. Check if Settings Exist

```php
// Add to footer.php temporarily
<?php
$options = get_option('ross_theme_footer_options', array());
echo '<!-- Footer options count: ' . count($options) . ' -->';
?>
```

### 3. Check if CSS is Generated

View page source, search for:
```html
<style id="ross-theme-dynamic-css">
```

### 4. Check if JavaScript is Loaded

Browser console:
```javascript
console.log(typeof RossHeaderNavigation);
// Should be "object" if loaded
```

### 5. Check for PHP Errors

View: `c:\xampp\htdocs\theme.dev\wp-content\debug.log`

### 6. Check for JavaScript Errors

Browser console (F12) - look for red errors

### 7. Clear All Caches

```powershell
# Browser cache: Ctrl+Shift+Delete
# WordPress cache:
wp cache flush

# Or via PHP (add to functions.php temporarily):
wp_cache_flush();
```

---

## 🎓 Common Tasks Reference

### Add New Header Control

**File:** `inc/features/header/header-options.php`

```php
// 1. Add settings field
add_settings_field(
    'new_control',
    'Control Label',
    array($this, 'new_control_callback'),
    'ross-theme-header-appearance',
    'ross_header_appearance_section'
);

// 2. Add callback function
public function new_control_callback() {
    $value = isset($this->options['new_control']) ? $this->options['new_control'] : '';
    echo '<input type="text" name="ross_theme_header_options[new_control]" value="' . esc_attr($value) . '" />';
}

// 3. Add sanitization
public function sanitize_header_options($input) {
    // ... existing code ...
    $sanitized['new_control'] = isset($input['new_control']) ? sanitize_text_field($input['new_control']) : '';
    // ... existing code ...
}
```

**File:** `inc/frontend/dynamic-css.php`

```php
// 4. Add CSS output
if (isset($header_options['new_control']) && !empty($header_options['new_control'])) {
    echo '.site-header { property: ' . esc_attr($header_options['new_control']) . '; }';
}
```

### Add New Footer Template

**File:** `inc/features/footer/templates/my-template.php`

```php
<?php
return array(
    'title' => 'My Template',
    'description' => 'Description here',
    'icon' => '🎨',
    'cols' => array('Column 1', 'Column 2', 'Column 3'),
    'bg' => '#ffffff',
    'text' => '#333333',
    'accent' => '#0066cc',
    'columns' => 3
);
```

**File:** `template-parts/footer/footer-my-template.php`

```php
<?php
$footer_options = get_option('ross_theme_footer_options', array());
?>
<footer class="site-footer footer-my-template">
    <!-- Your template HTML -->
</footer>
```

### Create Custom Page Template

**File:** `templates/template-custom.php`

```php
<?php
/**
 * Template Name: My Custom Template
 */
get_header();
?>

<main id="main" class="site-main">
    <?php
    while (have_posts()) :
        the_post();
        the_content();
    endwhile;
    ?>
</main>

<?php
get_footer();
```

---

## 🔐 Security Reference

### Sanitization Functions

```php
// Text input
sanitize_text_field($input);

// Email
sanitize_email($input);

// URL
esc_url_raw($input);          // For storage
esc_url($input);              // For output

// HTML
wp_kses_post($input);         // Allow safe HTML
wp_strip_all_tags($input);    // Strip all HTML

// Hex color
sanitize_hex_color($input);

// Integer
absint($input);

// Checkbox (0 or 1)
isset($input) ? 1 : 0;

// Array of text
array_map('sanitize_text_field', $input);
```

### Escaping Functions (Output)

```php
// HTML
esc_html($text);

// Attributes
esc_attr($text);

// URL
esc_url($url);

// JavaScript
esc_js($text);

// Textarea
esc_textarea($text);
```

### Nonce Verification

```php
// Generate nonce
wp_nonce_field('action_name', 'nonce_field_name');

// Verify nonce
if (!wp_verify_nonce($_POST['nonce_field_name'], 'action_name')) {
    wp_die('Security check failed');
}

// AJAX nonce
wp_create_nonce('ajax_action');
check_ajax_referer('ajax_action');
```

---

## 📊 Performance Optimization Reference

### Check Page Load Time

```powershell
# Using curl (if installed)
curl -o /dev/null -s -w "Total time: %{time_total}s\n" http://theme.dev
```

### Optimize Images

**Tools:**
- TinyPNG (https://tinypng.com)
- ImageOptim (https://imageoptim.com)
- Squoosh (https://squoosh.app)

**Recommended Sizes:**
- Logo: 200-300px wide, < 50KB
- Header images: 1920x600px, < 200KB
- Footer images: 600x400px, < 100KB

### Minify CSS/JS

**For Production:**
```php
// In functions.php
if (!defined('SCRIPT_DEBUG') || !SCRIPT_DEBUG) {
    // Load minified versions
    wp_enqueue_style('theme-style', get_stylesheet_directory_uri() . '/style.min.css');
}
```

---

## 🚨 Emergency Fixes

### Site is Blank (White Screen)

1. Check `wp-content/debug.log` for PHP fatal errors
2. Disable theme: Rename theme folder temporarily
3. Switch to default theme via database:
```sql
UPDATE wp_options 
SET option_value = 'twentytwentyfour' 
WHERE option_name = 'template' OR option_name = 'stylesheet';
```

### Cannot Access Admin

1. Add admin user via database:
```sql
INSERT INTO wp_users (user_login, user_pass, user_email, user_status)
VALUES ('newadmin', MD5('password'), 'admin@example.com', 0);

INSERT INTO wp_usermeta (user_id, meta_key, meta_value)
VALUES (LAST_INSERT_ID(), 'wp_capabilities', 'a:1:{s:13:"administrator";b:1;}');
```

2. Reset admin password:
```sql
UPDATE wp_users 
SET user_pass = MD5('newpassword') 
WHERE user_login = 'admin';
```

### Settings Not Saving

1. Check file permissions (should be writable)
2. Check `php.ini` - `max_input_vars` should be 3000+
3. Increase PHP limits in `wp-config.php`:
```php
@ini_set('max_input_vars', 3000);
@ini_set('post_max_size', '20M');
@ini_set('upload_max_filesize', '20M');
```

### Database Connection Error

1. Check XAMPP MySQL is running
2. Verify `wp-config.php` credentials:
```php
define('DB_NAME', 'wp_theme_dev');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
```

---

## 📚 Additional Resources

### WordPress Codex
- https://developer.wordpress.org
- https://codex.wordpress.org

### PHP Documentation
- https://www.php.net/manual/en/

### JavaScript MDN
- https://developer.mozilla.org/en-US/docs/Web/JavaScript

### CSS Reference
- https://developer.mozilla.org/en-US/docs/Web/CSS

### Git Documentation
- https://git-scm.com/doc

### Playwright Documentation
- https://playwright.dev

---

## 🎯 Quick Access Checklist

### Daily Development Workflow

- [ ] Start XAMPP (Apache + MySQL)
- [ ] Open VS Code
- [ ] Navigate to theme directory
- [ ] Check `debug.log` for errors
- [ ] Make changes
- [ ] Test in browser
- [ ] Check console for errors
- [ ] Commit changes to Git
- [ ] Push to GitHub

### Before Deploying to Production

- [ ] Run all tests: `npm run test:e2e`
- [ ] Check for console errors
- [ ] Check for PHP errors in debug.log
- [ ] Optimize images
- [ ] Minify CSS/JS
- [ ] Test on multiple browsers
- [ ] Test on mobile devices
- [ ] Backup database
- [ ] Document changes

---

**This reference guide contains everything you need for daily development. Bookmark this page!**
