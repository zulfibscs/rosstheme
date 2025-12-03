# Footer Template System - Complete Implementation Guide

## 📋 Overview

This document explains the fully functional footer template system with modern UI/UX, complete with preview, apply, and sync capabilities.

## 🗂️ File Structure

```
inc/features/footer/
├── footer-options.php          # Main footer options class (UPDATED)
├── footer-functions.php        # Footer rendering functions
└── templates/                  # Template definition files
    ├── business-professional.php  # 💼 Business Professional template
    ├── ecommerce.php             # 🛒 E-commerce template
    ├── creative-agency.php       # 🎨 Creative Agency template
    └── minimal-modern.php        # ✨ Minimal Modern template

assets/css/admin/
└── footer-template-ui.css      # Modern template selector UI (NEW)

assets/js/admin/
└── footer-template-selector.js # Template interactions JS (NEW)
```

## 🎨 Template Files

Each template file follows this structure:

```php
<?php
return array(
    'id' => 'template-slug',                    // Unique identifier
    'title' => 'Template Name',                 // Display name
    'description' => 'Brief description',       // Shown in admin UI
    'icon' => '💼',                             // Emoji icon for card
    'columns' => 4,                             // Number of columns
    'bg' => '#f8f9fb',                         // Background color
    'text' => '#0b2140',                       // Text color
    'accent' => '#0b66a6',                     // Accent/link color
    'social' => '#0b66a6',                     // Social icons color
    'cols' => array(                           // Column content
        array(
            'title' => 'Column Title',
            'items' => array('Item 1', 'Item 2')
        ),
        // ... more columns
    ),
    'cta' => array(                            // Optional CTA section
        'title' => 'CTA Title',
        'subtitle' => 'CTA Description',
        'button_text' => 'Button Text',
        'button_url' => '#'
    )
);
```

### Available Templates

1. **Business Professional** (`business-professional.php`)
   - Icon: 💼
   - Layout: 4 columns
   - Best for: Professional services, consulting firms, B2B companies
   - Colors: Light gray background (#f8f9fb), dark blue text

2. **E-commerce** (`ecommerce.php`)
   - Icon: 🛒
   - Layout: 4 columns
   - Best for: Online stores, retail businesses
   - Colors: White background, red accent (#b02a2a)

3. **Creative Agency** (`creative-agency.php`)
   - Icon: 🎨
   - Layout: 4 columns
   - Best for: Design studios, agencies, portfolios
   - Colors: Dark background (#0c0c0d), yellow accent (#E5C902)

4. **Minimal Modern** (`minimal-modern.php`)
   - Icon: ✨
   - Layout: 1 column (centered)
   - Best for: SaaS products, tech startups
   - Colors: Off-white background (#fafafa)

## 🎯 Key Features

### 1. Modern Card-Based UI

Templates are displayed as interactive cards with:
- Large emoji icon
- Template name and description
- Column count indicator
- Hover effects with smooth transitions
- Visual selection indicator (checkmark)

### 2. Preview Functionality

**Button:** "👁️ Preview Selected Template"

**How it works:**
1. User selects a template card
2. Clicks preview button
3. Preview loads instantly from hidden HTML (no AJAX needed)
4. Shows full template layout with sample content
5. Includes CTA, columns, and footer elements

**Implementation:**
```javascript
// Client-side preview from hidden HTML
const $hiddenPreview = $('#ross-preview-' + templateId);
$('#ross-template-preview .ross-template-preview-body').html($hiddenPreview.clone());
```

### 3. Apply Template

**Button:** "✅ Apply Template"

**How it works:**
1. Confirmation modal appears
2. On confirm, sends AJAX request to `ross_apply_footer_template`
3. Creates backup of current settings
4. Applies selected template's colors and structure
5. Updates `ross_theme_footer_options` in database
6. Shows success message
7. Page reloads to reflect changes

**Backend Flow:**
```php
public function ajax_apply_footer_template() {
    // 1. Verify nonce and permissions
    check_ajax_referer('ross_apply_footer_template', 'nonce');
    
    // 2. Get selected template
    $template_id = sanitize_text_field($_POST['template']);
    
    // 3. Create backup
    $this->create_backup($current_options, $template_id);
    
    // 4. Apply template settings
    $new_options = array_merge($current_options, $template_data);
    
    // 5. Save to database
    update_option('ross_theme_footer_options', $new_options);
    
    // 6. Return success
    wp_send_json_success();
}
```

### 4. Sync Templates

**Button:** "🔄 Sync Templates"

**Purpose:** Compare file-based templates with stored settings and update if needed

**How it works:**
1. Opens modal showing comparison table
2. Lists all templates from `/templates/` folder
3. Shows differences between file version and stored version
4. User selects which templates to sync
5. Applies sync button updates selected templates
6. Preserves user customizations

**Use case:** When you update template files and want to refresh stored settings

### 5. Backup & Restore

**Automatic backups created when:**
- Applying a new template
- User can restore from backups table
- Each backup stores: timestamp, user, template name, full settings

**Actions available:**
- **Restore:** Revert to previous footer settings
- **Delete:** Remove backup entry

## 🔧 Technical Implementation

### PHP Class Methods

**In `RossFooterOptions` class:**

```php
// Core methods
public function __construct()                    // Initialize hooks
public function register_footer_settings()       // Register WP settings
public function enqueue_footer_scripts($hook)    // Load CSS/JS assets
public function footer_template_callback()       // Render template selector UI

// Template management
private function load_templates_from_dir()       // Load PHP template files
private function get_templates()                 // Get all available templates
private function get_template_preview_html($id)  // Generate preview HTML

// AJAX handlers
public function ajax_get_footer_template_preview()  // Preview AJAX
public function ajax_apply_footer_template()        // Apply AJAX
public function ajax_sync_footer_templates()        // Sync dialog AJAX
public function ajax_apply_template_sync()          // Apply sync AJAX
public function ajax_restore_footer_backup()        // Restore backup AJAX
public function ajax_delete_footer_backup()         // Delete backup AJAX
public function ajax_list_footer_backups()          // List backups AJAX

// Backup management
private function get_backups()                   // Retrieve backups
private function save_backups($backups)          // Save backups
private function render_backups_list_html()      // Render backups table

// Migration
public function migrate_legacy_template_keys()   // Migrate old IDs
public function ensure_default_template_options() // Ensure templates exist
```

### JavaScript Functions

**In `footer-template-selector.js`:**

```javascript
// Initialization
initTemplateSelector()     // Setup card clicks and selection
initTemplateActions()      // Setup button handlers
initBackupActions()        // Setup backup restore/delete
initConfirmModal()         // Setup confirmation dialog

// Core actions
previewTemplate(id)        // Show template preview
applyTemplate(id)          // Apply template via AJAX
syncTemplates()            // Open sync modal
restoreBackup(id)          // Restore from backup
deleteBackup(id)           // Delete backup entry

// UI helpers
showConfirm(msg, callback) // Show confirmation modal
hideConfirm()              // Hide confirmation modal
showNotice(msg, type)      // Show admin notice
scrollToElement($el)       // Smooth scroll to element
```

### CSS Classes

**In `footer-template-ui.css`:**

```css
/* Template Cards */
.ross-footer-templates         /* Grid container */
.ross-template-card            /* Individual card */
.ross-template-card.selected   /* Selected state */
.ross-template-icon            /* Emoji icon */
.ross-template-title           /* Template name */
.ross-template-description     /* Description text */
.ross-template-meta            /* Meta info (columns) */

/* Actions */
.ross-template-actions         /* Button container */

/* Preview */
#ross-template-preview         /* Preview container */
.ross-template-preview-header  /* Preview header */
.ross-template-preview-body    /* Preview content */

/* Modals */
#ross-confirm-modal            /* Confirmation dialog */
#ross-sync-modal               /* Sync dialog */

/* States */
.ross-loading                  /* Loading state */
```

## 🔐 Security & Validation

### Nonce Verification
```php
check_ajax_referer('ross_apply_footer_template', 'nonce');
```

### Capability Checks
```php
if (!current_user_can('manage_options')) {
    wp_send_json_error('Unauthorized', 403);
}
```

### Data Sanitization
```php
$template_id = sanitize_text_field($_POST['template']);
$backup_id = sanitize_text_field($_POST['backup_id']);
```

### XSS Protection
```php
echo esc_html($title);
echo esc_attr($id);
echo esc_url($url);
```

## 🚀 Usage Guide

### For End Users

1. **Go to:** WordPress Admin → Ross Theme Settings → Footer
2. **Find:** 🧱 Footer Layout section
3. **See:** 4 template cards with icons and descriptions
4. **Click:** Any card to select it
5. **Preview:** Click "Preview Selected Template" to see it
6. **Apply:** Click "Apply Template" to use it
7. **Confirm:** Confirm in modal dialog
8. **Done:** Footer is updated and backup is created

### For Developers

#### Adding a New Template

1. **Create file:** `inc/features/footer/templates/my-template.php`
2. **Return array:**
```php
<?php
return array(
    'id' => 'my-template',
    'title' => 'My Custom Template',
    'description' => 'Description of my template',
    'icon' => '🚀',
    'columns' => 3,
    'bg' => '#ffffff',
    'text' => '#333333',
    'accent' => '#007cba',
    'social' => '#007cba',
    'cols' => array(
        array('title' => 'Column 1', 'items' => array('Item A', 'Item B')),
        array('title' => 'Column 2', 'items' => array('Item C', 'Item D')),
        array('title' => 'Column 3', 'items' => array('Item E', 'Item F'))
    )
);
```
3. **Save file**
4. **Refresh admin** - Template appears automatically!

#### Customizing Template Colors

Templates support these color properties:
- `bg` - Background color
- `text` - Main text color
- `accent` - Links and accents
- `social` - Social icon color

#### Extending Column Types

Columns support three formats:

**1. Items Array:**
```php
array('title' => 'Services', 'items' => array('Web Design', 'SEO', 'Marketing'))
```

**2. Custom HTML:**
```php
array('title' => 'Newsletter', 'html' => '<p>Subscribe now!</p><form>...</form>')
```

**3. Social Links:**
```php
array('title' => 'Follow Us', 'social' => array('Facebook', 'Twitter', 'Instagram'))
```

## 🐛 Troubleshooting

### Template not showing
- Check file exists in `inc/features/footer/templates/`
- Verify file returns valid PHP array
- Check for PHP syntax errors in template file
- Clear WordPress cache

### Preview not loading
- Check browser console for JavaScript errors
- Verify AJAX URL is correct: `rossFooterAdmin.ajax_url`
- Ensure nonce is being passed correctly
- Check if jQuery is loaded

### Apply not working
- Verify user has `manage_options` capability
- Check nonce verification passes
- Look for PHP errors in debug log
- Ensure `ross_theme_footer_options` is writable

### Styles not applying
- Clear browser cache (Ctrl+Shift+Delete)
- Check CSS file loaded: Inspect → Sources → footer-template-ui.css
- Verify file path is correct in enqueue function
- Check for CSS conflicts with other plugins

## 📊 Database Structure

### Option: `ross_theme_footer_options`
```php
array(
    'footer_template' => 'business-professional',  // Selected template ID
    'footer_columns' => '4',
    'enable_widgets' => 1,
    // ... other footer settings
)
```

### Option: `ross_theme_footer_templates`
```php
array(
    'business-professional' => array( /* template data */ ),
    'ecommerce' => array( /* template data */ ),
    'creative-agency' => array( /* template data */ ),
    'minimal-modern' => array( /* template data */ )
)
```

### Option: `ross_footer_template_backups`
```php
array(
    array(
        'id' => 'backup_1234567890_userid',
        'timestamp' => '2025-12-02 10:30:00',
        'user_id' => 1,
        'user_display' => 'admin',
        'template' => 'business-professional',
        'options' => array( /* full footer options snapshot */ )
    ),
    // ... more backups
)
```

## ✅ Testing Checklist

- [ ] All 4 templates display correctly
- [ ] Card selection works (visual feedback)
- [ ] Preview button shows correct template
- [ ] Apply button creates backup
- [ ] Apply button updates footer settings
- [ ] Page reloads after apply
- [ ] Backups list shows new entries
- [ ] Restore backup works
- [ ] Delete backup works
- [ ] Sync templates dialog opens
- [ ] Sync applies selected templates
- [ ] Confirmation modals work
- [ ] Success/error notices display
- [ ] Responsive design works on mobile
- [ ] Browser console has no errors

## 🎓 Best Practices

1. **Always preview before applying** - See changes before committing
2. **Keep backups** - Don't delete all backups; keep at least one
3. **Test on staging first** - Try new templates on non-production sites
4. **Customize after applying** - Templates are starting points, customize as needed
5. **Document changes** - Note what you've customized for future reference

## 🔄 Migration from Old System

If upgrading from template1-4 naming:

1. Old IDs automatically migrate to new names
2. No data loss occurs
3. Backups preserved with old template names
4. Frontend continues working during migration

**Migration map:**
- `template1` → `business-professional`
- `template2` → `ecommerce`
- `template3` → `creative-agency`
- `template4` → `minimal-modern`

## 📞 Support

For issues or questions:
1. Check debug log: `wp-content/debug.log`
2. Enable `WP_DEBUG` in `wp-config.php`
3. Review browser console for JS errors
4. Check this documentation for solutions

---

**Created:** December 2, 2025  
**Version:** 1.0.0  
**Status:** Production Ready ✅
