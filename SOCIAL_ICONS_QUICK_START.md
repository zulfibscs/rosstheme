# Social Icons V2 - Quick Start Guide

## 🎯 What You'll See

### Admin Page: Footer → Social Icons

After navigating to **Ross Theme Settings → Footer → Social Icons** tab, you'll see:

```
┌─────────────────────────────────────────────────────────────────┐
│  🔘 Show social media icons in footer                           │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐
│  ┌───┐           │  │  ┌───┐           │  │  ┌───┐           │
│  │ f │  Facebook │  │  │ ⬛ │ Instagram │  │  │ 🐦 │  Twitter  │
│  └───┘           │  │  └───┘           │  │  └───┘           │
│                  │  │                  │  │                  │
│  [ON] ────o      │  │  [ON] ────o      │  │  [OFF] o────     │
│                  │  │                  │  │                  │
│  URL:            │  │  URL:            │  │  URL:            │
│  [____________]  │  │  [____________]  │  │  [____________]  │
│                  │  │                  │  │  (disabled)      │
└──────────────────┘  └──────────────────┘  └──────────────────┘

┌──────────────────┐  ┌────────────────────────────────────────┐
│  ┌───┐           │  │  ┌───┐                                 │
│  │in │  LinkedIn │  │  │ ? │  Custom Platform                │
│  └───┘           │  │  └───┘                                 │
│                  │  │                                         │
│  [ON] ────o      │  │  [ON] ────o                            │
│                  │  │                                         │
│  URL:            │  │  Platform Name:                        │
│  [____________]  │  │  [Discord_____________________]        │
│                  │  │                                         │
│                  │  │  Icon:                                 │
│                  │  │  [🎨 Choose Icon]                      │
│                  │  │                                         │
│                  │  │  URL:                                  │
│                  │  │  [_________________________]           │
│                  │  │                                         │
│                  │  │  Icon Color:                           │
│                  │  │  [#7289DA] 🎨                          │
└──────────────────┘  └────────────────────────────────────────┘
```

---

## 📝 Step-by-Step Setup

### 1. Access Social Icons Settings
```
WordPress Admin → Ross Theme Settings → Footer → Social Icons tab
```

### 2. Enable Core Platforms (4 options)

**Facebook:**
- Toggle: **ON** (blue switch)
- URL: `https://facebook.com/yourpage`

**Instagram:**
- Toggle: **ON**
- URL: `https://instagram.com/yourprofile`

**Twitter/X:**
- Toggle: **OFF** (if not using)
- URL: (leave empty)

**LinkedIn:**
- Toggle: **ON**
- URL: `https://linkedin.com/company/yourcompany`

### 3. Configure Custom Platform (Optional)

**Example: Discord**
1. Toggle custom platform **ON**
2. Platform Name: `Discord`
3. Click **[🎨 Choose Icon]** button
   - Modal opens with 30+ icons
   - Search: type "discord"
   - Click Discord icon
   - Modal closes automatically
4. URL: `https://discord.gg/yourserver`
5. Icon Color: Pick purple `#7289DA` using color picker

**Example: Behance**
1. Toggle **ON**
2. Platform Name: `Behance`
3. Choose Icon: Search "behance" → Select
4. URL: `https://behance.net/yourportfolio`
5. Icon Color: `#1769FF` (Behance blue)

### 4. Adjust Display Order (Optional)
```
Display Order dropdown:
┌────────────────────┐
│ facebook          │
│ instagram         │
│ twitter           │
│ linkedin          │
│ custom            │
└────────────────────┘

Drag to reorder (coming soon), or leave default order.
```

### 5. Configure Icon Style
Scroll to:
- **Icon Style**: Circle / Square / Rounded / Plain
- **Icon Size**: 36px (default)
- **Icon Color**: (optional override)
- **Hover Color**: (optional custom hover)

### 6. Save Settings
Click **[Save Changes]** button at bottom of page.

---

## 🎨 Icon Picker Modal

When you click **Choose Icon** on custom platform:

```
┌─────────────────────────────────────────────────────┐
│  Choose Icon                                    [×] │
├─────────────────────────────────────────────────────┤
│                                                     │
│  Search: [discord________________]    🔍           │
│                                                     │
│  ┌────┐ ┌────┐ ┌────┐ ┌────┐ ┌────┐              │
│  │ 💬 │ │ 🎨 │ │ 🎮 │ │ 📱 │ │ 📧 │              │
│  └────┘ └────┘ └────┘ └────┘ └────┘              │
│  Discord Behance Dribbble Medium Email            │
│                                                     │
│  ┌────┐ ┌────┐ ┌────┐ ┌────┐ ┌────┐              │
│  │ 👾 │ │ 📷 │ │ 🎵 │ │ 📺 │ │ 💻 │              │
│  └────┘ └────┘ └────┘ └────┘ └────┘              │
│  Reddit Snapchat Spotify Twitch GitHub            │
│                                                     │
│  ... (scroll for more icons)                       │
│                                                     │
└─────────────────────────────────────────────────────┘
```

**30 Available Icons:**
- **Social**: Discord, Behance, Dribbble, Medium, Reddit, Snapchat, Spotify, Twitch
- **Video**: Vimeo, YouTube, TikTok (if needed as custom)
- **Dev**: GitHub, GitLab, Stack Overflow
- **Chat**: Slack, Skype, WhatsApp, Telegram, WeChat, Weibo
- **Media**: Tumblr, SoundCloud, Patreon, Kickstarter, Product Hunt, Bandcamp
- **Utility**: RSS, Email, Phone, Link, Globe

---

## 🖼️ Frontend Display Examples

### Example 1: Facebook + Instagram + LinkedIn
```
Footer appears with:
[f] [⬛] [in]
```
Clicking icons opens platform pages in new tab.

### Example 2: Instagram + Twitter + Custom (Discord)
```
Footer appears with:
[⬛] [🐦] [💬]
```
Discord icon uses custom color (#7289DA purple).

### Example 3: All 5 Platforms
```
Footer appears with:
[f] [⬛] [🐦] [in] [custom icon]
```
Icons render in order specified by display_order setting.

---

## 🎛️ Toggle Switch Behavior

**When Enabled (ON):**
- Switch slides right (blue background)
- Card border becomes blue
- Card gets subtle shadow
- URL input is active (white background)

**When Disabled (OFF):**
- Switch slides left (gray background)
- Card becomes translucent
- Card border stays gray
- URL input is grayed out (disabled)

---

## 🔧 Common Use Cases

### Case 1: Simple Social Presence
```
Setup:
- Facebook: ON → yourpage
- Instagram: ON → yourprofile
- Others: OFF

Result: 2 icons in footer
```

### Case 2: Professional Network
```
Setup:
- LinkedIn: ON → company profile
- Twitter: ON → company handle
- Custom (Medium): ON → company blog
- Others: OFF

Result: 3 icons (LinkedIn, Twitter, Medium)
```

### Case 3: Creative Portfolio
```
Setup:
- Instagram: ON → portfolio
- Custom (Behance): ON → projects
- Custom (Dribbble): Wait... only 1 custom allowed!

Solution: Choose most important platform for custom slot
```

### Case 4: Gaming Community
```
Setup:
- Twitter: ON → community updates
- Custom (Discord): ON → community server
- Custom (Twitch): Wait... only 1 custom!

Solution: Use Discord for primary community hub
Future: Request multiple custom slots from developer
```

---

## ✅ Testing Your Setup

1. **Enable 2-3 platforms** with URLs
2. **Save settings**
3. **Visit your website** (not admin)
4. **Scroll to footer**
5. **Verify icons appear**
6. **Click each icon** → Opens correct page in new tab
7. **Test mobile view** → Icons stack/wrap properly
8. **Change icon style** → Circle vs Square appearance
9. **Test hover effect** → Color change on mouseover
10. **Disable a platform** → Icon disappears from footer

---

## 🚨 Troubleshooting

### Icons Not Showing in Admin
- Clear browser cache (Ctrl+F5)
- Check if Font Awesome CDN loaded (Network tab)
- Disable other plugins temporarily

### Icons Not Showing in Frontend
- Ensure platform is **enabled** (toggle ON)
- Ensure URL is **not empty**
- Check footer template includes social icons
- Verify `rosstheme_render_footer_social()` is called

### Toggle Switch Not Working
- Check browser console for JS errors
- Verify jQuery is loaded
- Ensure `social-icons-manager.js` loaded
- Try different browser

### Icon Picker Not Opening
- Check for modal backdrop (dark overlay)
- Inspect element to see if modal exists
- Check z-index conflicts with other plugins
- Verify button click event binding

### Custom Color Not Applying
- Ensure custom platform toggle is **ON**
- Pick color using WordPress color picker (not hex input)
- Save settings after color change
- Clear frontend cache

---

## 💡 Pro Tips

1. **Icon Order**: Put most important platform first in display order
2. **Custom Platform**: Use for your primary community hub (Discord, Slack, etc.)
3. **Icon Style**: Use "plain" for minimalist footers, "circle" for modern look
4. **Hover Colors**: Leave empty for automatic brand color hover effects
5. **Mobile**: Icons automatically resize and stack on small screens
6. **Accessibility**: Each icon has proper aria-label for screen readers

---

## 📊 Settings Summary Table

| Platform  | Toggle Field          | URL Field        | Always Available |
|-----------|-----------------------|------------------|------------------|
| Facebook  | facebook_enabled      | facebook_url     | ✅ Yes           |
| Instagram | instagram_enabled     | instagram_url    | ✅ Yes           |
| Twitter   | twitter_enabled       | twitter_url      | ✅ Yes           |
| LinkedIn  | linkedin_enabled      | linkedin_url     | ✅ Yes           |
| Custom    | custom_social_enabled | custom_social_url| ✅ Yes           |

**Custom Platform Extra Fields:**
- `custom_social_label` - Platform name (e.g., "Discord")
- `custom_social_icon` - Font Awesome class (e.g., "fab fa-discord")
- `custom_social_color` - Hex color (e.g., "#7289DA")

---

## 🎯 Next Steps

1. **Navigate to Footer admin page**
2. **Enable your platforms**
3. **Add URLs**
4. **Configure custom platform** (if needed)
5. **Save and view frontend**
6. **Enjoy your modern social icons!**

---

*Need more custom platforms? Contact the theme developer to request expansion from 1 to 3 custom slots.*
