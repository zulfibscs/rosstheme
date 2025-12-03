# CTA Admin Quick Reference

## 📍 Location
**WordPress Admin → Ross Theme Settings → Footer → Call-to-Action Tab**

---

## 🎨 Section 4: Styling Options (ENHANCED)

### Border Controls
```
┌─────────────────────────────────────────┐
│ Border Width (px)                       │
│ [  2  ] (0-20)                         │
├─────────────────────────────────────────┤
│ Border Style                            │
│ [Solid ▼] none/solid/dashed/dotted     │
├─────────────────────────────────────────┤
│ Border Color                            │
│ [🎨 #e0e0e0]                           │
├─────────────────────────────────────────┤
│ Border Radius (px)                      │
│ [  8  ] (0-100)                        │
└─────────────────────────────────────────┘
```

### Shadow Controls
```
┌─────────────────────────────────────────┐
│ ☐ Enable Box Shadow                    │
├─────────────────────────────────────────┤
│ Shadow Color (rgba supported)           │
│ [ rgba(0,0,0,0.1) ]                    │
├─────────────────────────────────────────┤
│ Shadow Blur (px)                        │
│ [ 15  ] (0-100)                        │
└─────────────────────────────────────────┘
```

### Button Hover Effects
```
┌─────────────────────────────────────────┐
│ Button Hover Background                 │
│ [🎨 #0056b3]                           │
├─────────────────────────────────────────┤
│ Button Hover Text Color                 │
│ [🎨 #ffffff]                           │
├─────────────────────────────────────────┤
│ Button Border Radius (px)               │
│ [  4  ] (0-50)                         │
└─────────────────────────────────────────┘
```

---

## ✍️ Section 5: Typography (NEW SECTION)

```
┌─────────────────────────────────────────┐
│ Title Font Size (px)                    │
│ [ 32  ] (12-72) Default: 28           │
├─────────────────────────────────────────┤
│ Title Font Weight                       │
│ [700 ▼] 300/400/500/600/700/800       │
├─────────────────────────────────────────┤
│ Text Font Size (px)                     │
│ [ 16  ] (10-32) Default: 16           │
├─────────────────────────────────────────┤
│ Button Font Size (px)                   │
│ [ 16  ] (10-24) Default: 16           │
├─────────────────────────────────────────┤
│ Button Font Weight                      │
│ [600 ▼] 400/500/600/700               │
├─────────────────────────────────────────┤
│ Letter Spacing (px)                     │
│ [ 0.5 ] (-2 to 10) Default: 0         │
└─────────────────────────────────────────┘
```

---

## 📏 Section 6: Spacing & Dimensions (ENHANCED)

### Container Width Controls
```
┌─────────────────────────────────────────┐
│ Container Width                         │
│ [Standard ▼]                           │
│   • Standard (theme container)          │
│   • Full (edge-to-edge)                │
│   • Custom (specify max-width)         │
├─────────────────────────────────────────┤
│ Custom Max Width (px)                   │
│ [ 1200 ] (300-2000)                    │
│ ℹ️  Only used when "Custom" selected   │
└─────────────────────────────────────────┘
```

---

## 🎯 Quick Design Recipes

### Recipe 1: Modern Card
```yaml
Border:
  Width: 1px
  Style: solid
  Color: #e0e0e0
  Radius: 12px
Shadow:
  Enabled: ✓
  Color: rgba(0,0,0,0.08)
  Blur: 20px
Typography:
  Title Size: 36px
  Title Weight: 700
```

### Recipe 2: Bold & Vibrant
```yaml
Border:
  Width: 3px
  Style: solid
  Color: #007bff
  Radius: 0px
Typography:
  Title Size: 42px
  Title Weight: 800
  Letter Spacing: 1.5px
Button:
  Hover BG: #0056b3
  Border Radius: 4px
```

### Recipe 3: Soft & Subtle
```yaml
Border:
  Width: 0px
  Radius: 16px
Shadow:
  Enabled: ✓
  Color: rgba(0,0,0,0.12)
  Blur: 30px
Typography:
  Title Size: 28px
  Title Weight: 600
  Letter Spacing: 0.5px
Button:
  Border Radius: 25px (pill shape)
```

### Recipe 4: Minimal Flat
```yaml
Border:
  Width: 0px
Shadow:
  Enabled: ✗
Typography:
  Title Size: 32px
  Title Weight: 500
  Letter Spacing: 0px
Container:
  Width: Full
  (Edge-to-edge layout)
```

---

## 📊 Field Summary

| Category | Fields | Total |
|----------|--------|-------|
| Border | Width, Style, Color, Radius | 4 |
| Shadow | Enable, Color, Blur | 3 |
| Typography | Title Size/Weight, Text Size, Button Size/Weight, Spacing | 6 |
| Button Hover | Background, Text Color, Radius | 3 |
| Container | Width Type, Max Width | 2 |
| **TOTAL** | **NEW FIELDS** | **19** |

---

## 🔄 Workflow Tips

1. **Start with borders or shadows** - They define the CTA container
2. **Then adjust typography** - Set font sizes and weights
3. **Fine-tune spacing** - Use existing padding controls + new container width
4. **Add button hover** - Creates interactive feedback
5. **Test on frontend** - View with different content lengths

---

## ⚠️ Important Notes

### Shadow Color Format
Supports both hex and rgba:
- **Hex:** `#000000` (solid black)
- **rgba:** `rgba(0,0,0,0.1)` (black with 10% opacity) ✅ Recommended

### Container Width Behavior
- **Standard** - Uses theme's default container (usually 1140px-1320px)
- **Full** - Sets `max-width: 100%` (edge-to-edge)
- **Custom** - Uses your specified max-width value

### Button Border Radius
- **0px** - Sharp square corners
- **4px** - Subtle rounding (default)
- **8-12px** - Noticeable rounded corners
- **25px+** - Pill-shaped button

---

## 🎨 Color Picker Tips

### Using the WordPress Color Picker
1. Click the color box to open picker
2. Drag circle to select hue
3. Click triangle to adjust shade
4. Or enter hex value directly

### For rgba Shadow Colors
1. Enter value directly in text field
2. Format: `rgba(red, green, blue, alpha)`
3. Example: `rgba(0,0,0,0.15)` = black at 15% opacity

---

## 📱 Responsive Considerations

All typography settings apply globally. For best responsive results:

- **Title Font Size:** Use 28-42px range
- **Text Font Size:** Keep 14-18px for readability
- **Button Font Size:** 14-16px works well on mobile
- **Letter Spacing:** Keep under 2px to avoid wrapping issues

---

## 🚀 Performance Notes

- Settings generate inline CSS (no additional HTTP requests)
- Only non-default values are output
- CSS is minified and cached with page
- No JavaScript dependencies for static controls

---

**Quick Start:** Set border to 1px solid #e0e0e0, enable shadow with rgba(0,0,0,0.1) and 15px blur, increase title size to 36px. Save and view! 🎉
