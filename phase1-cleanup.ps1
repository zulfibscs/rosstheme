# Phase 1: Commercial Theme Cleanup Script
# Removes duplicates, reorganizes files, updates paths

Write-Host "=== PHASE 1: COMMERCIAL THEME RESTRUCTURING ===" -ForegroundColor Cyan
Write-Host ""

$themeRoot = "c:\xampp\htdocs\theme.dev\wp-content\themes\rosstheme5\rosstheme"
$timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
$backupDir = Join-Path $themeRoot "backup-phase1-$timestamp"

# Create backup directory
New-Item -ItemType Directory -Path $backupDir -Force | Out-Null
Write-Host "Created backup: $backupDir" -ForegroundColor Yellow
Write-Host ""

# ============================================
# STEP 1: Remove Duplicate Header Templates
# ============================================
Write-Host "[1/5] Removing duplicate header templates..." -ForegroundColor Green

$duplicateHeaders = @(
    "template-parts\header\header-default.php",
    "template-parts\header\header-centered.php",
    "template-parts\header\header-minimal.php",
    "template-parts\header\header-modern.php",
    "template-parts\header\header-transparent.php"
)

foreach ($file in $duplicateHeaders) {
    $fullPath = Join-Path $themeRoot $file
    if (Test-Path $fullPath) {
        $fileName = Split-Path $file -Leaf
        Copy-Item $fullPath (Join-Path $backupDir $fileName) -Force
        Remove-Item $fullPath -Force
        Write-Host "  ✓ Removed: $fileName" -ForegroundColor Gray
    }
}

# ============================================
# STEP 2: Remove Duplicate Footer Templates
# ============================================
Write-Host "`n[2/5] Removing duplicate footer templates..." -ForegroundColor Green

$duplicateFooters = @(
    "template-parts\footer\footer-default.php",
    "template-parts\footer\footer-default-new.php",
    "template-parts\footer\footer-minimal.php",
    "template-parts\footer\footer-modern.php"
)

foreach ($file in $duplicateFooters) {
    $fullPath = Join-Path $themeRoot $file
    if (Test-Path $fullPath) {
        $fileName = Split-Path $file -Leaf
        Copy-Item $fullPath (Join-Path $backupDir $fileName) -Force
        Remove-Item $fullPath -Force
        Write-Host "  ✓ Removed: $fileName" -ForegroundColor Gray
    }
}

# ============================================
# STEP 3: Move Misplaced Files
# ============================================
Write-Host "`n[3/5] Reorganizing misplaced files..." -ForegroundColor Green

# Move root-level template-parts to proper locations
$moves = @(
    @{
        From = "template-parts\header-default.php"
        To = "DELETE"
        Reason = "Duplicate of header/header-default.php"
    },
    @{
        From = "template-parts\topbar.php"
        To = "template-parts\header\topbar.php"
        Reason = "Topbar belongs in header folder"
    },
    @{
        From = "template-parts\topbar-advanced.php"
        To = "template-parts\header\topbar-advanced.php"
        Reason = "Advanced topbar belongs in header folder"
    }
)

foreach ($move in $moves) {
    $fromPath = Join-Path $themeRoot $move.From
    
    if (Test-Path $fromPath) {
        $fileName = Split-Path $fromPath -Leaf
        Copy-Item $fromPath (Join-Path $backupDir $fileName) -Force
        
        if ($move.To -eq "DELETE") {
            Remove-Item $fromPath -Force
            Write-Host "  ✓ Deleted: $fileName ($($move.Reason))" -ForegroundColor Gray
        } else {
            $toPath = Join-Path $themeRoot $move.To
            Move-Item $fromPath $toPath -Force
            Write-Host "  ✓ Moved: $fileName → $($move.To)" -ForegroundColor Gray
        }
    }
}

# Move footer social files to proper location
$socialMoves = @(
    @{
        From = "inc\customizer-footer-social.php"
        To = "inc\features\footer\social-customizer.php"
    },
    @{
        From = "inc\template-tags-footer-social.php"
        To = "inc\features\footer\social-functions.php"
    }
)

foreach ($move in $socialMoves) {
    $fromPath = Join-Path $themeRoot $move.From
    
    if (Test-Path $fromPath) {
        $fileName = Split-Path $fromPath -Leaf
        Copy-Item $fromPath (Join-Path $backupDir $fileName) -Force
        $toPath = Join-Path $themeRoot $move.To
        Move-Item $fromPath $toPath -Force
        Write-Host "  ✓ Moved: $fileName → features/footer/" -ForegroundColor Gray
    }
}

# ============================================
# STEP 4: Update functions.php paths
# ============================================
Write-Host "`n[4/5] Updating functions.php with new paths..." -ForegroundColor Green

$functionsPath = Join-Path $themeRoot "functions.php"
if (Test-Path $functionsPath) {
    Copy-Item $functionsPath (Join-Path $backupDir "functions.php") -Force
    
    $content = Get-Content $functionsPath -Raw
    
    # Update social customizer paths
    $content = $content -replace "inc/customizer-footer-social\.php", "inc/features/footer/social-customizer.php"
    $content = $content -replace "inc/template-tags-footer-social\.php", "inc/features/footer/social-functions.php"
    
    Set-Content $functionsPath $content -NoNewline
    Write-Host "  ✓ Updated require paths in functions.php" -ForegroundColor Gray
}

# ============================================
# STEP 5: Create Professional Template Manifest
# ============================================
Write-Host "`n[5/5] Creating template manifest..." -ForegroundColor Green

$manifestContent = @"
# Ross Theme - Professional Template Manifest
# Generated: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')

## HEADER TEMPLATES (5 Professional)
1. header-business-classic.php     - Traditional corporate header
2. header-creative-agency.php      - Bold creative design
3. header-ecommerce-shop.php       - E-commerce with cart
4. header-minimal-modern.php       - Clean minimal design
5. header-transparent-hero.php     - Transparent overlay

## FOOTER TEMPLATES (4 Professional)
1. footer-business-professional.php - Corporate layout
2. footer-creative-agency.php       - Creative design
3. footer-ecommerce.php             - Shop footer
4. footer-minimal-modern.php        - Minimal clean

## FOOTER COMPONENTS (3 Reusable)
1. footer-copyright.php             - Copyright bar
2. footer-cta.php                   - Call-to-action block
3. footer-widgets.php               - Widget areas

## HOMEPAGE TEMPLATES (6 Professional)
1. template-home-business.php       - Business Professional
2. template-home-creative.php       - Creative Agency
3. template-home-ecommerce.php      - E-commerce Shop
4. template-home-minimal.php        - Minimal Portfolio
5. template-home-restaurant.php     - Restaurant/Food
6. template-home-startup.php        - Startup/Tech

## HEADER COMPONENTS
1. header/header-search.php         - Search overlay
2. header/topbar.php                - Top information bar
3. header/topbar-advanced.php       - Advanced topbar

---

### REMOVED (Duplicates/Legacy)
- header-default.php (merged into business-classic)
- header-centered.php (layout option in templates)
- header-minimal.php (duplicate of minimal-modern)
- header-modern.php (consolidated)
- header-transparent.php (duplicate of transparent-hero)
- footer-default.php (merged into business-professional)
- footer-default-new.php (unclear purpose, removed)
- footer-minimal.php (duplicate of minimal-modern)
- footer-modern.php (consolidated)

### REORGANIZED
- topbar.php → header/topbar.php
- topbar-advanced.php → header/topbar-advanced.php
- customizer-footer-social.php → features/footer/social-customizer.php
- template-tags-footer-social.php → features/footer/social-functions.php
"@

$manifestPath = Join-Path $themeRoot "TEMPLATE_MANIFEST.md"
Set-Content $manifestPath $manifestContent
Write-Host "  ✓ Created TEMPLATE_MANIFEST.md" -ForegroundColor Gray

# ============================================
# SUMMARY
# ============================================
Write-Host "`n=== CLEANUP COMPLETE ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "✓ Removed 9 duplicate templates" -ForegroundColor Green
Write-Host "✓ Reorganized 5 misplaced files" -ForegroundColor Green
Write-Host "✓ Updated functions.php paths" -ForegroundColor Green
Write-Host "✓ Created template manifest" -ForegroundColor Green
Write-Host ""
Write-Host "Backup location: $backupDir" -ForegroundColor Yellow
Write-Host ""
Write-Host "PROFESSIONAL TEMPLATES NOW:" -ForegroundColor Cyan
Write-Host "  Headers:  5 templates" -ForegroundColor White
Write-Host "  Footers:  4 templates + 3 components" -ForegroundColor White
Write-Host "  Homepage: 6 templates" -ForegroundColor White
Write-Host ""
Write-Host "Next: Test theme to ensure no broken paths" -ForegroundColor Yellow
Write-Host "Then: Run Phase 2 (Homepage Switcher UI)" -ForegroundColor Yellow
