# 🏠 Homepage Template System - Commercial Strategy

## 📋 Executive Summary

**Problem Identified:** Two conflicting template systems causing homepage templates not to apply properly.

**Solution:** Unified WordPress-native template system that works like commercial themes (Avada, Astra, OceanWP).

---

## 🔍 Current Problems

### Issue 1: Conflicting Systems
```
homepage-manager.php      → Creates NEW PAGES for each template ❌
template-switcher-ui.php  → Applies template to EXISTING page ✅ (correct approach)
front-page.php           → Hardcoded content, ignores templates ❌
```

### Issue 2: Template Not Applied
When user clicks "Apply Template":
- ✅ AJAX saves `_wp_page_template` meta correctly
- ❌ front-page.php has hardcoded content (doesn't load template)
- ❌ WordPress doesn't automatically use template-home-*.php files

### Issue 3: WordPress Template Hierarchy Confusion
```
WordPress looks for homepage in this order:
1. front-page.php (ALWAYS used first if exists) ← PROBLEM!
2. page template assigned to homepage
3. home.php
4. index.php
```

**Solution:** front-page.php must LOAD the selected template, not replace it.

---

## ✅ Commercial Theme Strategy

### How Professional Themes Handle This

**Avada/Astra/OceanWP Pattern:**
```php
// front-page.php (acts as ROUTER)
<?php
$page_id = get_option('page_on_front');
$template = get_post_meta($page_id, '_wp_page_template', true);

if ($template && file_exists(locate_template($template))) {
    include(locate_template($template));
} else {
    // Default homepage content
    get_header();
    // ... default content ...
    get_footer();
}
```

### Our Implementation Plan

**File Structure:**
```
front-page.php                    ← ROUTER (loads selected template)
template-home-business.php        ← Selectable via admin
template-home-creative.php        ← Selectable via admin
template-home-ecommerce.php       ← Selectable via admin
template-home-minimal.php         ← Selectable via admin
template-home-restaurant.php      ← Selectable via admin
template-home-startup.php         ← Selectable via admin
```

**Admin System:**
```
Ross Theme → Homepage Templates
- Visual card UI
- One-click template selection
- Live preview (opens in new tab)
- Apply button (saves _wp_page_template meta)
- Active indicator (shows current template)
```

---

## 🔧 Technical Implementation

### Step 1: Fix front-page.php (Template Router)

**Current (Hardcoded):**
```php
// ❌ WRONG - Hardcoded accountant content
<section class="front-page-hero">
    <h1>Accounting & Advisory for Growing Businesses</h1>
    ...
</section>
```

**New (Dynamic Router):**
```php
<?php
/**
 * Front Page Template - Ross Theme Homepage Router
 * Loads the selected homepage template or default content
 */

$page_id = get_option('page_on_front');
$selected_template = '';

if ($page_id) {
    $selected_template = get_post_meta($page_id, '_wp_page_template', true);
}

// If homepage template is selected and exists, use it
if ($selected_template && 
    $selected_template !== 'default' && 
    file_exists(locate_template($selected_template))) {
    
    include(locate_template($selected_template));
    
} else {
    // Default homepage (no template selected)
    get_header();
    ?>
    <main id="primary" class="site-main front-page">
        <!-- Default homepage content -->
        <section class="welcome-section container">
            <h1>Welcome to Ross Theme</h1>
            <p>Select a homepage template from: <strong>Ross Theme → Homepage Templates</strong></p>
        </section>
    </main>
    <?php
    get_footer();
}
```

### Step 2: Ensure Homepage Exists

**Auto-Create Homepage Function:**
```php
// inc/features/homepage-templates/homepage-setup.php

function ross_ensure_homepage_exists() {
    $page_on_front = get_option('page_on_front');
    
    if (!$page_on_front) {
        // Create homepage
        $page_id = wp_insert_post(array(
            'post_title' => 'Home',
            'post_name' => 'home',
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_content' => ''
        ));
        
        if (!is_wp_error($page_id)) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $page_id);
            return $page_id;
        }
    }
    
    return $page_on_front;
}
add_action('after_switch_theme', 'ross_ensure_homepage_exists');
```

### Step 3: Fix AJAX Handler

**Current Issue:**
```php
// ✅ Saves template meta correctly
update_post_meta($home_page_id, '_wp_page_template', $template);

// ❌ But front-page.php doesn't load it!
```

**Enhanced AJAX (Already Correct):**
```php
public function ajax_apply_template() {
    check_ajax_referer('ross_template_switcher', 'nonce');
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }
    
    $template = sanitize_text_field($_POST['template']);
    
    // Ensure homepage exists
    $home_page_id = get_option('page_on_front');
    if (!$home_page_id) {
        // Auto-create homepage
        $home_page_id = wp_insert_post(array(
            'post_title' => 'Home',
            'post_name' => 'home',
            'post_type' => 'page',
            'post_status' => 'publish'
        ));
        
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_page_id);
    }
    
    // Validate template exists
    $templates = $this->get_available_templates();
    if (!isset($templates[$template])) {
        wp_send_json_error('Invalid template');
    }
    
    // Apply template to homepage
    update_post_meta($home_page_id, '_wp_page_template', $template);
    
    // Clear cache
    delete_transient('ross_homepage_template_cache');
    
    wp_send_json_success(array(
        'message' => 'Homepage template applied successfully!',
        'template' => $template,
        'name' => $templates[$template]['name'],
        'home_url' => home_url('/')
    ));
}
```

### Step 4: Remove Conflicting Code

**Delete from homepage-manager.php:**
- ✅ Already removed admin UI
- ❌ Still has `create_homepage_from_template()` - REMOVE
- ❌ Still has duplicate AJAX handlers - KEEP for backwards compatibility

**Keep Only:**
- Template definitions array
- AJAX handlers for API compatibility
- Helper functions

---

## 📦 Installation Flow (Commercial Theme)

### First-Time Theme Activation

```
1. User activates Ross Theme
2. Theme auto-creates "Home" page (if none exists)
3. Sets as static front page (Settings → Reading)
4. Shows admin notice: "Visit Ross Theme → Homepage Templates to choose a design"
```

### Changing Homepage Template

```
1. Admin → Ross Theme → Homepage Templates
2. See 6 template cards with previews
3. Click "Preview" - opens in new tab
4. Click "Apply Template" - saves to homepage
5. Visit homepage - see new design
6. Change anytime - no data loss
```

### How It Works (Behind the Scenes)

```
User clicks "Business Professional":
├─ AJAX saves: _wp_page_template = template-home-business.php
├─ User visits homepage
├─ WordPress loads: front-page.php
├─ front-page.php checks: get_post_meta(page_id, '_wp_page_template')
├─ Finds: template-home-business.php
├─ Includes: template-home-business.php
└─ Displays: Business Professional homepage
```

---

## 🎯 Implementation Checklist

### Phase 1: Core Fix (URGENT)
- [ ] Rewrite front-page.php as template router
- [ ] Add homepage auto-creation on theme activation
- [ ] Fix AJAX to create homepage if missing
- [ ] Test template switching works

### Phase 2: Clean Up
- [ ] Remove page creation code from homepage-manager.php
- [ ] Keep only AJAX handlers in homepage-manager.php
- [ ] Verify template-switcher-ui.php is the only UI

### Phase 3: Commercial Features
- [ ] Add theme activation notice
- [ ] Add screenshot previews for each template
- [ ] Add "Visit Homepage" link in admin
- [ ] Add template reset functionality

### Phase 4: Documentation
- [ ] User guide: "How to Change Homepage"
- [ ] Developer guide: "Adding New Templates"
- [ ] Video tutorial (optional)

---

## 🔄 How Other Themes Do It

### Astra Theme
```php
// front-page.php
get_header();
astra_content_layout(); // Loads selected layout
get_footer();
```

### Avada Theme
```php
// front-page.php
get_header();
fusion_get_template_part('homepage'); // Loads template parts
get_footer();
```

### OceanWP Theme
```php
// front-page.php
get_header();
do_action('ocean_before_content_wrap');
oceanwp_display_page_template(); // Router function
do_action('ocean_after_content_wrap');
get_footer();
```

**Our Approach (Simple & Clean):**
```php
// front-page.php
$template = get_post_meta(get_option('page_on_front'), '_wp_page_template', true);
if ($template && file_exists(locate_template($template))) {
    include(locate_template($template));
} else {
    // Default content
}
```

---

## 📊 Benefits of This Approach

### For Users
✅ Simple template switching (no page creation clutter)  
✅ No data loss when switching templates  
✅ Preview before applying  
✅ Works with page builders (Elementor, Gutenberg)  
✅ Professional, commercial-grade experience  

### For Developers
✅ WordPress-native (uses standard template hierarchy)  
✅ Easy to add new templates  
✅ Compatible with child themes  
✅ Clean, maintainable code  
✅ Follows WP Codex standards  

### For Commercial Distribution
✅ GPL-compatible  
✅ Theme Check approved pattern  
✅ Works on any hosting  
✅ No database bloat  
✅ Fast, efficient  

---

## 🚀 Next Steps

1. **Implement front-page.php router** (10 min)
2. **Test template switching** (5 min)
3. **Add activation setup** (10 min)
4. **Clean up duplicate code** (5 min)
5. **Document for users** (10 min)

**Total Time: ~40 minutes to production-ready homepage template system!**

---

## 📝 Template File Standards

Each homepage template MUST have:

```php
<?php
/**
 * Template Name: Homepage - [Template Name]
 * Description: [Brief description]
 * 
 * @package RossTheme
 */

get_header(); // Uses theme header settings
?>

<main id="primary" class="site-main ross-homepage-template">
    <!-- Template content here -->
</main>

<?php
get_footer(); // Uses theme footer settings
```

**Key Points:**
- Always call `get_header()` and `get_footer()`
- Use theme settings for colors (get_option('ross_theme_general_options'))
- Make responsive
- Include demo content with placeholders
- Add classes for styling hooks

---

## ✅ Success Criteria

System is working when:
1. ✅ User can switch templates from admin UI
2. ✅ Template immediately shows on homepage
3. ✅ No page clutter in Pages list
4. ✅ Works on fresh WordPress install
5. ✅ Templates use theme header/footer
6. ✅ Active template shows green badge
7. ✅ Preview opens in new tab

---

**Status:** Ready to implement  
**Priority:** HIGH - Core functionality  
**Estimated Time:** 40 minutes  
**Commercial Readiness:** Will reach 90/100 after implementation  
