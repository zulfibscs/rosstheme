# Social Icons - Quick Fix Summary

## ✅ Problem Fixed
Social icons were not displaying in the footer.

## 🔧 What Was Done
Added automatic migration that sets all platforms to "enabled" by default on first admin page visit.

## 📋 What You Need to Do

### Step 1: Visit Admin (Triggers Migration)
Navigate to any WordPress admin page. Migration runs automatically.

### Step 2: Add Your Social URLs
1. Go to: **Ross Theme Settings → Footer → Social Icons**
2. Find the platforms you want to use (Facebook, Instagram, Twitter, LinkedIn)
3. Make sure toggle is **ON** (blue switch)
4. Enter your URLs:
   - Facebook: `https://facebook.com/yourpage`
   - Instagram: `https://instagram.com/yourprofile`  
   - Twitter: `https://twitter.com/yourhandle`
   - LinkedIn: `https://linkedin.com/company/yourcompany`
5. Click **Save Changes**

### Step 3: Check Frontend
Visit your website and scroll to the footer. Icons should appear!

## 🎯 Important Rules

Icons will ONLY display if:
1. ✅ Master toggle "Show social media icons in footer" is checked
2. ✅ Platform toggle is ON (blue)
3. ✅ Platform has a URL entered

**Example:**
```
Facebook:
- Toggle: ON ✅
- URL: https://facebook.com/mypage ✅
Result: Icon displays ✅

Twitter:
- Toggle: ON ✅  
- URL: (empty) ❌
Result: Icon does NOT display ❌
```

## 🔍 Quick Test

**Debug Page**: Visit `http://theme.dev/wp-content/themes/rosstheme5/rosstheme/debug-social.php`

Should show:
```
Enable Social Icons: int(1) ✅
Facebook Enabled: int(1) ✅
Facebook URL: (your URL or empty)
```

## 📱 Custom Platform (Optional)

Want to add Discord, Behance, or other platforms?

1. Toggle **Custom Platform** to ON
2. Enter platform name
3. Click **Choose Icon** button
4. Select icon from 30+ options
5. Enter URL
6. Pick color
7. Save

## ❓ Still Not Working?

1. **Clear cache** (WordPress + browser)
2. **Check debug page** for values
3. **View page source** → Search for "footer-social"
4. **Check console** (F12) for errors
5. **Verify Font Awesome** is loading

## 📞 Support

See full documentation:
- `SOCIAL_ICONS_V2_COMPLETE.md` - Technical details
- `SOCIAL_ICONS_QUICK_START.md` - User guide
- `SOCIAL_ICONS_FRONTEND_FIX.md` - This fix details

---

**Status**: FIXED ✅  
**Action Required**: Add URLs to platforms → Icons appear  
**Time to Fix**: 2 minutes
