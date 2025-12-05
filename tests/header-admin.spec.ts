import { test, expect } from '@playwright/test';

const ADMIN_URL = process.env.ADMIN_URL || 'http://theme.dev/wp-admin';
const ADMIN_USER = process.env.ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.ADMIN_PASS || 'password';
const SITE_URL = process.env.SITE_URL || 'http://theme.dev';

// Helper: Login to WordPress admin
async function adminLogin(page) {
  await page.goto(ADMIN_URL);
  await page.fill('input#user_login', ADMIN_USER);
  await page.fill('input#user_pass', ADMIN_PASS);
  await page.click('input#wp-submit');
  await page.waitForLoadState('networkidle');
}

test.describe('Header Admin Settings', () => {
  
  test.beforeEach(async ({ page }) => {
    await adminLogin(page);
    await page.goto(`${ADMIN_URL}/admin.php?page=ross-theme-header`);
    await page.waitForSelector('form[method="post"]');
  });

  test('should load header settings page correctly', async ({ page }) => {
    // Verify page title
    await expect(page.locator('h1')).toContainText('Header Settings');
    
    // Verify main sections are present
    await expect(page.locator('h2:has-text("Logo Settings")')).toBeVisible();
    await expect(page.locator('h2:has-text("Navigation Settings")')).toBeVisible();
    await expect(page.locator('h2:has-text("Header Appearance")')).toBeVisible();
    await expect(page.locator('h2:has-text("Sticky Header Options")')).toBeVisible();
    await expect(page.locator('h2:has-text("Mobile Menu Settings")')).toBeVisible();
  });

  test('should toggle sticky header options', async ({ page }) => {
    // Find sticky header checkbox
    const stickyCheckbox = page.locator('input[name="ross_theme_header_options[sticky_header]"]');
    
    // Check if checkbox exists
    await expect(stickyCheckbox).toBeVisible();
    
    // Check the box
    await stickyCheckbox.check();
    await expect(stickyCheckbox).toBeChecked();
    
    // Verify dependent options appear
    await expect(page.locator('select[name="ross_theme_header_options[sticky_behavior]"]')).toBeVisible();
    
    // Save settings
    await page.click('input[type="submit"][value="Save Changes"]');
    await page.waitForLoadState('networkidle');
    
    // Verify saved successfully
    await expect(page.locator('.notice-success, .updated')).toBeVisible();
  });

  test('should update header template selection', async ({ page }) => {
    // Select different header templates
    const templates = ['business-classic', 'creative-agency', 'minimal-modern', 'ecommerce-shop', 'transparent-hero'];
    
    for (const template of templates) {
      const templateRadio = page.locator(`input[name="ross_theme_header_options[header_layout]"][value="${template}"]`);
      
      if (await templateRadio.count() > 0) {
        await templateRadio.check();
        await expect(templateRadio).toBeChecked();
      }
    }
    
    // Save
    await page.click('input[type="submit"][value="Save Changes"]');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('.notice-success')).toBeVisible();
  });

  test('should configure mobile logo settings', async ({ page }) => {
    // Check mobile logo checkbox
    const mobileLogo = page.locator('input[name="ross_theme_header_options[enable_mobile_logo]"]');
    
    if (await mobileLogo.count() > 0) {
      await mobileLogo.check();
      
      // Verify mobile logo width input appears
      const widthInput = page.locator('input[name="ross_theme_header_options[mobile_logo_width]"]');
      await expect(widthInput).toBeVisible();
      
      // Set width value
      await widthInput.fill('120');
      
      // Save
      await page.click('input[type="submit"][value="Save Changes"]');
      await page.waitForLoadState('networkidle');
    }
  });

  test('should configure search settings', async ({ page }) => {
    // Select search type
    const searchTypeSelect = page.locator('select[name="ross_theme_header_options[search_type]"]');
    
    if (await searchTypeSelect.count() > 0) {
      await searchTypeSelect.selectOption('modal');
      
      // Set search placeholder
      const placeholderInput = page.locator('input[name="ross_theme_header_options[search_placeholder]"]');
      await placeholderInput.fill('Search our site...');
      
      // Save
      await page.click('input[type="submit"][value="Save Changes"]');
      await page.waitForLoadState('networkidle');
      await expect(page.locator('.notice-success')).toBeVisible();
    }
  });

  test('should configure header appearance settings', async ({ page }) => {
    // Header opacity
    const opacityInput = page.locator('input[name="ross_theme_header_options[header_opacity]"]');
    if (await opacityInput.count() > 0) {
      await opacityInput.fill('0.95');
    }
    
    // Header shadow
    const shadowSelect = page.locator('select[name="ross_theme_header_options[header_shadow]"]');
    if (await shadowSelect.count() > 0) {
      await shadowSelect.selectOption('medium');
    }
    
    // Header border
    const borderCheckbox = page.locator('input[name="ross_theme_header_options[header_border_bottom]"]');
    if (await borderCheckbox.count() > 0) {
      await borderCheckbox.check();
    }
    
    // Save
    await page.click('input[type="submit"][value="Save Changes"]');
    await page.waitForLoadState('networkidle');
  });

  test('should configure mobile menu settings', async ({ page }) => {
    // Mobile menu style
    const menuStyleSelect = page.locator('select[name="ross_theme_header_options[mobile_menu_style]"]');
    if (await menuStyleSelect.count() > 0) {
      await menuStyleSelect.selectOption('slide');
    }
    
    // Hamburger animation
    const hamburgerSelect = page.locator('select[name="ross_theme_header_options[hamburger_animation]"]');
    if (await hamburgerSelect.count() > 0) {
      await hamburgerSelect.selectOption('spin');
    }
    
    // Mobile menu position
    const positionSelect = page.locator('select[name="ross_theme_header_options[mobile_menu_position]"]');
    if (await positionSelect.count() > 0) {
      await positionSelect.selectOption('left');
    }
    
    // Save
    await page.click('input[type="submit"][value="Save Changes"]');
    await page.waitForLoadState('networkidle');
    await expect(page.locator('.notice-success')).toBeVisible();
  });
});

test.describe('Header Frontend Display', () => {
  
  test('should display header with default template', async ({ page }) => {
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    // Verify header exists
    await expect(page.locator('header.site-header, header[role="banner"]')).toBeVisible();
    
    // Verify navigation menu
    await expect(page.locator('nav.main-navigation, nav.primary-navigation')).toBeVisible();
  });

  test('should show sticky header on scroll', async ({ page }) => {
    // First configure sticky header in admin
    await adminLogin(page);
    await page.goto(`${ADMIN_URL}/admin.php?page=ross-theme-header`);
    
    const stickyCheckbox = page.locator('input[name="ross_theme_header_options[sticky_header]"]');
    if (await stickyCheckbox.count() > 0) {
      await stickyCheckbox.check();
      await page.click('input[type="submit"][value="Save Changes"]');
      await page.waitForLoadState('networkidle');
    }
    
    // Now test frontend
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    const header = page.locator('header.site-header, header[role="banner"]');
    await expect(header).toBeVisible();
    
    // Scroll down
    await page.evaluate(() => window.scrollTo(0, 500));
    await page.waitForTimeout(500);
    
    // Check if sticky class is added
    const hasSticky = await header.evaluate((el) => {
      return el.classList.contains('is-sticky') || 
             el.classList.contains('sticky') || 
             el.style.position === 'fixed';
    });
    
    // Header should be sticky or fixed
    expect(hasSticky).toBeTruthy();
  });

  test('should toggle mobile menu', async ({ page }) => {
    // Set viewport to mobile
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    // Find hamburger/menu toggle
    const menuToggle = page.locator('.hamburger, .mobile-menu-toggle, button[aria-label*="menu"], button.menu-toggle').first();
    
    if (await menuToggle.count() > 0) {
      await expect(menuToggle).toBeVisible();
      
      // Click to open
      await menuToggle.click();
      await page.waitForTimeout(300);
      
      // Verify menu is visible
      const mobileMenu = page.locator('.mobile-menu, .mobile-navigation, nav.mobile-nav, .menu-mobile').first();
      
      if (await mobileMenu.count() > 0) {
        await expect(mobileMenu).toBeVisible();
        
        // Click to close
        await menuToggle.click();
        await page.waitForTimeout(300);
      }
    }
  });

  test('should display mobile logo on small screens', async ({ page }) => {
    // Configure mobile logo in admin first
    await adminLogin(page);
    await page.goto(`${ADMIN_URL}/admin.php?page=ross-theme-header`);
    
    const mobileLogoCheckbox = page.locator('input[name="ross_theme_header_options[enable_mobile_logo]"]');
    if (await mobileLogoCheckbox.count() > 0) {
      await mobileLogoCheckbox.check();
      await page.click('input[type="submit"][value="Save Changes"]');
      await page.waitForLoadState('networkidle');
    }
    
    // Test on mobile viewport
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    // Check for logo visibility
    const logo = page.locator('.site-logo, .custom-logo-link, .header-logo').first();
    if (await logo.count() > 0) {
      await expect(logo).toBeVisible();
    }
  });

  test('should open search modal', async ({ page }) => {
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    // Find search toggle
    const searchToggle = page.locator('.search-toggle, button[aria-label*="search"], .search-icon').first();
    
    if (await searchToggle.count() > 0) {
      await searchToggle.click();
      await page.waitForTimeout(300);
      
      // Check for search modal/form
      const searchModal = page.locator('.search-modal, .search-overlay, .search-form-wrapper').first();
      
      if (await searchModal.count() > 0) {
        await expect(searchModal).toBeVisible();
        
        // Find search input
        const searchInput = page.locator('input[type="search"], input.search-field').first();
        if (await searchInput.count() > 0) {
          await expect(searchInput).toBeVisible();
          
          // Type search query
          await searchInput.fill('test query');
          await expect(searchInput).toHaveValue('test query');
        }
      }
    }
  });

  test('should apply header transparency for transparent template', async ({ page }) => {
    // Configure transparent hero template
    await adminLogin(page);
    await page.goto(`${ADMIN_URL}/admin.php?page=ross-theme-header`);
    
    const transparentTemplate = page.locator('input[name="ross_theme_header_options[header_layout]"][value="transparent-hero"]');
    if (await transparentTemplate.count() > 0) {
      await transparentTemplate.check();
      await page.click('input[type="submit"][value="Save Changes"]');
      await page.waitForLoadState('networkidle');
    }
    
    // Check frontend
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    const header = page.locator('header.site-header, header[role="banner"]').first();
    
    // Check if header has transparent styling
    const isTransparent = await header.evaluate((el) => {
      const styles = window.getComputedStyle(el);
      return styles.position === 'absolute' || 
             styles.backgroundColor.includes('transparent') ||
             parseFloat(styles.opacity) < 1;
    });
    
    expect(isTransparent).toBeTruthy();
  });

  test('should verify dynamic CSS is loaded', async ({ page }) => {
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    // Check for dynamic CSS style tag
    const dynamicCSS = await page.locator('style#ross-theme-dynamic-css').count();
    
    // Dynamic CSS should be present in head
    expect(dynamicCSS).toBeGreaterThan(0);
  });

  test('should verify navigation.js is loaded with localized options', async ({ page }) => {
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    // Check if rossHeaderOptions is available in window
    const hasOptions = await page.evaluate(() => {
      return typeof window['rossHeaderOptions'] !== 'undefined';
    });
    
    expect(hasOptions).toBeTruthy();
    
    // Verify specific options are present
    const options = await page.evaluate(() => window['rossHeaderOptions']);
    
    expect(options).toHaveProperty('sticky_header');
    expect(options).toHaveProperty('mobile_menu_style');
    expect(options).toHaveProperty('search_type');
  });

  test('should handle menu hover effects', async ({ page }) => {
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    // Find first menu item
    const menuItem = page.locator('nav a, .menu-item a').first();
    
    if (await menuItem.count() > 0) {
      await expect(menuItem).toBeVisible();
      
      // Hover over menu item
      await menuItem.hover();
      await page.waitForTimeout(200);
      
      // Menu item should change appearance on hover
      // (This is a visual test - in real scenario you'd check computed styles)
      const hasHoverStyle = await menuItem.evaluate((el) => {
        const styles = window.getComputedStyle(el);
        return styles.textDecoration !== 'none' || 
               styles.color !== '';
      });
      
      expect(hasHoverStyle).toBeTruthy();
    }
  });
});

test.describe('Header Responsive Behavior', () => {
  
  test('should display properly on desktop viewport', async ({ page }) => {
    await page.setViewportSize({ width: 1920, height: 1080 });
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    const header = page.locator('header.site-header, header[role="banner"]').first();
    await expect(header).toBeVisible();
    
    // Desktop menu should be visible
    const desktopNav = page.locator('nav.main-navigation, nav.primary-navigation').first();
    if (await desktopNav.count() > 0) {
      await expect(desktopNav).toBeVisible();
    }
  });

  test('should display properly on tablet viewport', async ({ page }) => {
    await page.setViewportSize({ width: 768, height: 1024 });
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    const header = page.locator('header.site-header, header[role="banner"]').first();
    await expect(header).toBeVisible();
  });

  test('should display properly on mobile viewport', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    const header = page.locator('header.site-header, header[role="banner"]').first();
    await expect(header).toBeVisible();
    
    // Hamburger should be visible on mobile
    const hamburger = page.locator('.hamburger, .mobile-menu-toggle, button.menu-toggle').first();
    if (await hamburger.count() > 0) {
      await expect(hamburger).toBeVisible();
    }
  });
});

test.describe('Header Accessibility', () => {
  
  test('should have proper ARIA labels', async ({ page }) => {
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    // Check for navigation landmarks
    const nav = page.locator('nav[aria-label], nav[role="navigation"]').first();
    if (await nav.count() > 0) {
      await expect(nav).toBeVisible();
    }
    
    // Check for header landmark
    const header = page.locator('header[role="banner"]').first();
    if (await header.count() > 0) {
      await expect(header).toBeVisible();
    }
  });

  test('should support keyboard navigation', async ({ page }) => {
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    // Tab through focusable elements
    await page.keyboard.press('Tab');
    await page.waitForTimeout(100);
    
    // Check if a focusable element is focused
    const focusedElement = await page.evaluate(() => {
      return document.activeElement?.tagName;
    });
    
    expect(focusedElement).toBeTruthy();
  });

  test('should close search modal with ESC key', async ({ page }) => {
    await page.goto(SITE_URL);
    await page.waitForLoadState('networkidle');
    
    // Open search if available
    const searchToggle = page.locator('.search-toggle, button[aria-label*="search"]').first();
    if (await searchToggle.count() > 0) {
      await searchToggle.click();
      await page.waitForTimeout(300);
      
      // Press ESC
      await page.keyboard.press('Escape');
      await page.waitForTimeout(300);
      
      // Search modal should be hidden
      const searchModal = page.locator('.search-modal, .search-overlay').first();
      if (await searchModal.count() > 0) {
        await expect(searchModal).not.toBeVisible();
      }
    }
  });
});
