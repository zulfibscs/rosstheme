# Header Templates - Quick Reference Guide

## 🚀 Quick Start (5 Minutes)

### Apply a Template

1. Go to **WordPress Admin → Ross Theme → Header Options**
2. Click **📐 Templates** tab
3. Choose a template and click **Apply Template**
4. Done! Your new header is live

### Customize Your Header

1. Stay in **Header Options**
2. Switch to other tabs to customize:
   - **🧱 Layout** - Container width, padding, sticky
   - **🧭 Logo** - Upload logo, set width
   - **🔗 Navigation** - Menu style, colors
   - **🔍 CTA & Search** - Button text, colors
   - **🌗 Appearance** - Background, text colors
3. Click **Save Header Settings**

## 📋 Template Comparison

| Template | Use Case | Layout | Sticky | Best For |
|----------|----------|--------|--------|----------|
| **Business Classic** 💼 | Corporate | Horizontal | Always | Professional services, B2B |
| **Creative Agency** 🎨 | Creative | Stacked | Scroll-up | Design studios, portfolios |
| **E-commerce Shop** 🛒 | Retail | Horizontal | Always | Online stores |
| **Minimal Modern** ✨ | Content | Horizontal | Scroll-down | Blogs, minimalist sites |
| **Transparent Hero** 🌅 | Marketing | Overlay | Always | Landing pages, hero sections |

## 🎨 Color Schemes at a Glance

### Business Classic
```
Background: #ffffff (White)
Text: #0b2140 (Navy)
Accent: #0b66a6 (Blue)
```

### Creative Agency
```
Background: #0c0c0d (Black)
Text: #f3f4f6 (Light)
Accent: #E5C902 (Gold)
```

### E-commerce Shop
```
Background: #ffffff (White)
Text: #1f2937 (Dark)
Accent: #dc2626 (Red)
```

### Minimal Modern
```
Background: transparent
Text: #111827 (Charcoal)
Accent: #111827
```

### Transparent Hero
```
Background: transparent → #001946
Text: #ffffff (White)
Accent: #E5C902 (Gold)
```

## ⚙️ Common Customizations

### Change Logo
```
Header Options → Logo & Branding
→ Click "Upload Logo"
→ Set Logo Width (recommended: 150-250px)
```

### Change Colors
```
Header Options → Appearance
→ Header Background Color
→ Header Text Color
→ Link Hover Color
```

### Enable/Disable Sticky
```
Header Options → Layout & Structure
→ Enable Sticky Header (checkbox)
```

### Customize CTA Button
```
Header Options → CTA & Search
→ Enable CTA Button (checkbox)
→ Button Text
→ Button URL
→ Button Color
```

### Mobile Menu Style
Each template has built-in mobile responsiveness:
- **Business Classic**: Stacks vertically
- **Creative Agency**: Full-screen overlay
- **E-commerce**: Sidebar menu
- **Minimal Modern**: Fade-in full menu
- **Transparent Hero**: Slide-in overlay

## 💾 Backup & Restore

### Automatic Backups
Every time you apply a new template, your current settings are automatically backed up.

### Restore a Backup
```
Header Options → Templates Tab
→ Scroll to "Header Backups"
→ Click "Restore" on desired backup
```

### Delete Old Backups
```
Header Backups → Click "Delete"
```
*Note: System keeps last 10 backups automatically*

## 🐛 Quick Troubleshooting

### Menu Items Not Showing
**Solution:** Go to **Appearance → Menus** → Assign menu to "Primary Menu" location

### Logo Too Large/Small
**Solution:** **Header Options → Logo & Branding** → Adjust "Logo Width"

### Header Not Sticky
**Solution:** **Header Options → Layout** → Check "Enable Sticky Header"

### Colors Not Changing
**Solution:** Clear browser cache + WordPress cache

### Mobile Menu Not Working
**Solution:** Check JavaScript console for errors, ensure navigation.js is loaded

## 📱 Responsive Breakpoints

All templates switch to mobile layout at **768px** by default.

To customize:
```php
// In your template file
'mobile_breakpoint' => '992', // Switch at 992px instead
```

## 🎯 Best Practices

### Logo Size
- **Recommended Width:** 150-250px
- **File Format:** PNG or WebP with transparency
- **Max File Size:** 100KB

### Menu Items
- **Ideal Count:** 5-7 items
- **Max Recommended:** 9 items
- **Mobile:** Auto-collapses at breakpoint

### CTA Button
- **Text Length:** 2-4 words (e.g., "Get Started", "Contact Us")
- **Color:** Should contrast with header background
- **URL:** Use relative URLs when possible

### Performance
- Use "Contained" width for better performance
- Optimize logo image (compress)
- Limit to 1 CTA button
- Enable sticky only if needed

## 🔗 Related Documentation

- **Full Documentation:** [HEADER_TEMPLATE_SYSTEM.md](./HEADER_TEMPLATE_SYSTEM.md)
- **Footer Templates:** Similar system for footers
- **Top Bar Guide:** [ADVANCED_TOPBAR_GUIDE.md](./ADVANCED_TOPBAR_GUIDE.md)

## 💡 Pro Tips

1. **Test Mobile First** - Always check mobile view after changes
2. **Use Template Preview** - Preview before applying to avoid unnecessary backups
3. **Keep Backups Organized** - Delete old backups you don't need
4. **Match Footer Style** - Use coordinating footer template
5. **Consider Users** - Sticky headers improve navigation but use screen space

## 🆘 Need Help?

1. Check the full documentation: `HEADER_TEMPLATE_SYSTEM.md`
2. Review browser console for errors (F12)
3. Enable WordPress debug mode
4. Check theme version compatibility

---

**Quick Access:**
- Templates: **Ross Theme → Header Options → Templates Tab**
- Backups: Same page, scroll to "Header Backups"
- General Header Settings: Other tabs in Header Options

**Remember:** Always save your changes before leaving the page!
