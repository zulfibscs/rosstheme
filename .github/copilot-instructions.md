# Ross Theme Development Guide

## Architecture Overview

This is a **modular WordPress theme** with a centralized admin panel built using WordPress Customizer API and Settings API. The theme follows a component-based structure where features are isolated into feature modules.

### Core Structure

```
functions.php               # Central loader - requires all modules
inc/
  core/                     # Theme setup, asset loading, security
  admin/                    # Customizer panels, settings registration, AJAX handlers
  features/                 # Feature modules (header, footer, general, blog, etc.)
  frontend/                 # Dynamic CSS generation, template rendering
  utilities/                # Helpers, sanitization, theme reset utility
template-parts/             # Reusable template components (topbar, header variants, footer variants)
assets/
  css/admin/               # Customizer UI styling
  css/frontend/            # Frontend styles loaded conditionally
  js/admin/                # Customizer preview JS (live updates)
  js/frontend/             # Navigation, search overlay, interactions
```

### Key Design Patterns

**1. Settings Storage Pattern**
- Settings stored in `wp_options` table as arrays: `ross_theme_header_options`, `ross_theme_footer_options`, `ross_theme_general_options`
- Access via `get_option('ross_theme_header_options', array())` - always provide array default
- Defensive coding: check `is_array()` before accessing keys to prevent corruption errors

**2. Feature Module Pattern (OOP Classes)**
Each feature (header, footer, general) uses a class-based structure:
```php
class RossFooterOptions {
    public function __construct() {
        add_action('admin_init', array($this, 'register_footer_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_footer_scripts'));
        // AJAX handlers for template preview/apply/restore
        add_action('wp_ajax_ross_apply_footer_template', array($this, 'ajax_apply_footer_template'));
    }
}
new RossFooterOptions();
```
See: `inc/features/footer/footer-options.php`, `inc/features/header/header-options.php`

**3. Dynamic CSS Generation**
- All customizer settings are output as inline CSS in `<head>` via `ross_theme_dynamic_css()` hooked to `wp_head` at priority 999
- Located in: `inc/frontend/dynamic-css.php`
- Uses `!important` flags to override theme defaults
- Critical for live preview updates and maintaining user customizations

**4. Template Rendering Helpers**
- Use centralized rendering functions instead of direct `get_template_part()` calls
- Example: `ross_theme_render_topbar()`, `ross_theme_render_announcement_at('below_header')`
- Located in: `inc/features/header/header-functions.php`, `inc/features/footer/footer-functions.php`

**5. Customizer Live Preview Pattern**
Settings use `transport => 'postMessage'` with matching JS in `assets/js/admin/customizer-topbar-preview.js`:
```javascript
wp.customize('setting_name', function(value) {
    value.bind(function(newval) {
        $('.target-element').css('property', newval);
    });
});
```

## Critical Workflows

### Adding New Customizer Settings

1. **Register setting** in appropriate feature class (e.g., `RossHeaderOptions::register_header_settings()`):
   ```php
   register_setting('ross_theme_header_options_group', 'ross_theme_header_options', array(
       'sanitize_callback' => array($this, 'sanitize_header_options')
   ));
   ```

2. **Add control HTML** in the same class's settings page method
3. **Add sanitization** in the class's sanitization callback
4. **Add dynamic CSS output** in `inc/frontend/dynamic-css.php`
5. **Add live preview JS** (optional) in `assets/js/admin/customizer-*-preview.js`

### Running Tests

End-to-end tests use **Playwright**:
```bash
# Install dependencies (first time only)
npm run playwright:install

# Run tests
npm run test:e2e              # Headless mode
npm run test:e2e:headed       # See browser
npm run test:e2e:debug        # Debug mode
```

Test files: `tests/*.spec.ts`
Config: `playwright.config.ts` (WordPress site at `http://theme.dev`)

**Test Authentication Pattern:**
```typescript
const ADMIN_URL = process.env.ADMIN_URL || 'http://theme.dev/wp-admin';
const ADMIN_USER = process.env.ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.ADMIN_PASS || 'password';
```

### Debugging

- **PHP Errors**: Check `wp-content/debug.log` when `WP_DEBUG` is enabled
- **Dynamic CSS Issues**: Check `ross_theme_dynamic_css()` is firing - look for `#ross-theme-dynamic-css` style tag in `<head>`
- **Customizer Not Showing**: Verify `require_once` statements in `functions.php` and clear WordPress cache
- **Live Preview Not Working**: Check browser console for JS errors; verify `transport => 'postMessage'` is set

### Asset Loading

**Frontend Assets** (`inc/core/asset-loader.php`):
- Uses `filemtime()` for cache-busting: `filemtime(get_template_directory() . '/style.css')`
- Conditional loading: checks `file_exists()` before enqueuing
- Dependency chain: `ross-theme-style` → `ross-theme-frontend-base` → feature-specific CSS

**Admin Assets** (per-feature):
- Each feature class handles its own admin scripts in `enqueue_*_scripts()` methods
- Example: `inc/features/footer/footer-options.php` enqueues color pickers, preview JS, template sync logic

## Project-Specific Conventions

### Naming Patterns
- **Options**: `ross_theme_{feature}_options` (e.g., `ross_theme_header_options`)
- **Functions**: `ross_theme_{action}` or `ross_{component}_{action}` (snake_case)
- **Classes**: `Ross{Feature}Options` (PascalCase)
- **CSS Classes**: `.site-topbar`, `.topbar-inner`, `.header-default` (kebab-case with BEM-like structure)
- **AJAX Actions**: `wp_ajax_ross_{action}` (e.g., `wp_ajax_ross_apply_footer_template`)

### Template System
Theme supports **multiple header/footer layouts** selected via admin:
- Header layouts: `default`, `centered`, `minimal`, `transparent`
- Footer templates: `template1`, `template2`, `template3`, `template4` (with backup/restore system)
- Template parts in: `template-parts/header/`, `template-parts/footer/`

**Template Application Flow**:
1. User previews template → AJAX call to `ross_get_footer_template_preview`
2. User applies template → Backup current settings → Apply new template colors/layout
3. User can restore from backups table

### Security & Sanitization
- **Text inputs**: `sanitize_text_field()`
- **HTML content**: `wp_kses_post()` (allows safe HTML)
- **Colors**: `sanitize_hex_color()`
- **Checkboxes**: Custom `ross_sanitize_checkbox()` (returns 0 or 1)
- **URLs**: `esc_url_raw()` for storage, `esc_url()` for output
- **AJAX**: Always verify `check_ajax_referer()` and `current_user_can('manage_options')`

### Migration & Legacy Handling
The theme includes migration logic for legacy settings (see `RossFooterOptions::migrate_legacy_template_keys()`). When adding new features:
- Check for old option key formats and migrate on `admin_init` hook
- Set migration flag to prevent repeated runs: use transients or dedicated option

## External Dependencies

- **Font Awesome 6.4.0** (CDN): Used for social icons and UI icons
- **WordPress Customizer API**: Core dependency for all admin panels
- **Playwright** (dev): E2E testing framework
- **jQuery**: Used for frontend interactions (navigation, search overlay)

## Integration Points

### WordPress Customizer
- Custom sections: `ross_announcement_section`, per-feature sections for header/footer/topbar
- All settings use `type => 'option'` (not theme_mods) for centralized storage

### Template Hierarchy
- `header.php` orchestrates: announcement → topbar → header → search overlay
- Uses conditional rendering: `ross_theme_render_announcement_at('position')` for flexible placement
- `footer.php` renders selected footer template via `ross_theme_display_footer()`

### Admin Pages
- Custom admin menu: "Ross Theme Settings" with sub-pages for Header, Footer, General
- Located: `inc/admin/admin-pages.php`
- Uses WordPress Settings API with custom styling in `assets/css/admin/admin-main.css`

## Documentation Map

- **Quick Start**: `QUICK_START.md` - 5-min integration guide
- **Architecture**: `ARCHITECTURE.md` - System flow diagrams, component hierarchy
- **Settings Reference**: `TOPBAR_SETTINGS_GUIDE.md` - All 27+ topbar customizer settings
- **Code Examples**: `TOPBAR_EXAMPLES.js` - Ready-to-use code patterns
- **Testing Guide**: `E2E-README.md` - Playwright test setup and patterns
