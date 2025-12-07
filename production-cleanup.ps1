# Ross Theme Production Cleanup Script
# Removes test files, console statements, and optimizes for production

Write-Host "=== Ross Theme Production Cleanup ===" -ForegroundColor Cyan
Write-Host ""

$themeRoot = $PSScriptRoot
$backupDir = Join-Path $themeRoot "backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')"

# Create backup
Write-Host "Creating backup in: $backupDir" -ForegroundColor Yellow
New-Item -ItemType Directory -Path $backupDir -Force | Out-Null

# 1. Remove test/debug PHP files
Write-Host "`n[1/7] Removing test/debug files..." -ForegroundColor Green
$testFiles = @(
    "test-topbar.php",
    "test-social-render.php", 
    "debug-social.php"
)

foreach ($file in $testFiles) {
    $path = Join-Path $themeRoot $file
    if (Test-Path $path) {
        Copy-Item $path (Join-Path $backupDir $file)
        Remove-Item $path -Force
        Write-Host "  ✓ Removed: $file" -ForegroundColor Gray
    }
}

# 2. Remove backup files
Write-Host "`n[2/7] Removing .bak files..." -ForegroundColor Green
Get-ChildItem -Path $themeRoot -Recurse -Filter "*.bak" | ForEach-Object {
    Copy-Item $_.FullName (Join-Path $backupDir $_.Name)
    Remove-Item $_.FullName -Force
    Write-Host "  ✓ Removed: $($_.Name)" -ForegroundColor Gray
}

# 3. Remove duplicate/test JS files
Write-Host "`n[3/7] Removing duplicate JS files..." -ForegroundColor Green
$jsTestFiles = @(
    "assets/js/admin/header-options-test.js",
    "assets/js/admin/general-options-test.js",
    "assets/js/admin/general-options-new.js"
)

foreach ($file in $jsTestFiles) {
    $path = Join-Path $themeRoot $file
    if (Test-Path $path) {
        Copy-Item $path (Join-Path $backupDir (Split-Path $file -Leaf))
        Remove-Item $path -Force
        Write-Host "  ✓ Removed: $file" -ForegroundColor Gray
    }
}

# 4. Clean console.log from JavaScript files  
Write-Host "`n[4/7] Removing console statements from JS files..." -ForegroundColor Green
$jsFiles = Get-ChildItem -Path (Join-Path $themeRoot "assets\js") -Recurse -Filter "*.js"

foreach ($jsFile in $jsFiles) {
    if ($jsFile.Name -like "*-test.js" -or $jsFile.Name -like "*.min.js") {
        continue
    }
    
    $content = Get-Content $jsFile.FullName -Raw
    $originalSize = $content.Length
    
    # Remove console statements
    $content = $content -replace '(?m)^\s*console\.(log|warn|error|debug|info|trace)\([^;]*\);\s*$\r?\n?', ''
    $content = $content -replace '\s*console\.(log|warn|error|debug|info|trace)\([^;]*\);\s*', ''
    
    # Remove debug comments
    $content = $content -replace '(?m)^\s*//\s*(Debug|TODO|FIXME|XXX):.*$\r?\n?', ''
    
    # Remove try-catch blocks that only contain console statements
    $content = $content -replace '(?s)try\s*\{\s*console\.[^}]*\}\s*catch[^}]*\}', ''
    
    if ($content.Length -ne $originalSize) {
        Copy-Item $jsFile.FullName (Join-Path $backupDir $jsFile.Name)
        Set-Content -Path $jsFile.FullName -Value $content -NoNewline
        $saved = $originalSize - $content.Length
        Write-Host "  ✓ Cleaned: $($jsFile.Name) (removed $saved bytes)" -ForegroundColor Gray
    }
}

# 5. Clean PHP debug statements
Write-Host "`n[5/7] Removing PHP debug statements..." -ForegroundColor Green
$phpFiles = Get-ChildItem -Path (Join-Path $themeRoot "inc") -Recurse -Filter "*.php"

foreach ($phpFile in $phpFiles) {
    $content = Get-Content $phpFile.FullName -Raw
    $originalSize = $content.Length
    
    # Remove error_log calls that are debugging only
    $content = $content -replace '(?m)^\s*if\s*\(defined\(''WP_DEBUG''\).*?error_log\([^;]*\);\s*\}\s*$\r?\n?', ''
    
    # Remove debug comments
    $content = $content -replace '(?m)^\s*//\s*(Debug|TODO|FIXME|XXX):.*$\r?\n?', ''
    $content = $content -replace '(?m)^\s*/\*\*?\s*(Debug|TODO|FIXME).*?\*/\s*$\r?\n?', ''
    
    if ($content.Length -ne $originalSize) {
        Copy-Item $phpFile.FullName (Join-Path $backupDir $phpFile.Name)
        Set-Content -Path $phpFile.FullName -Value $content -NoNewline
        Write-Host "  ✓ Cleaned: $($phpFile.Name)" -ForegroundColor Gray
    }
}

# 6. Remove TOPBAR_EXAMPLES.js (documentation file)
Write-Host "`n[6/7] Removing documentation examples file..." -ForegroundColor Green
$examplesFile = Join-Path $themeRoot "TOPBAR_EXAMPLES.js"
if (Test-Path $examplesFile) {
    Copy-Item $examplesFile (Join-Path $backupDir "TOPBAR_EXAMPLES.js")
    Remove-Item $examplesFile -Force
    Write-Host "  ✓ Removed: TOPBAR_EXAMPLES.js" -ForegroundColor Gray
}

# 7. Optimize: Remove empty lines (more than 2 consecutive)
Write-Host "`n[7/7] Optimizing file formatting..." -ForegroundColor Green
$allCodeFiles = Get-ChildItem -Path $themeRoot -Recurse -Include "*.php","*.js","*.css" | 
    Where-Object { $_.FullName -notlike "*node_modules*" -and $_.FullName -notlike "*vendor*" }

foreach ($file in $allCodeFiles) {
    $content = Get-Content $file.FullName -Raw
    # Replace 3+ consecutive newlines with just 2
    $cleaned = $content -replace '(\r?\n){3,}', "`n`n"
    
    if ($cleaned -ne $content) {
        Set-Content -Path $file.FullName -Value $cleaned -NoNewline
    }
}
Write-Host "  ✓ Optimized whitespace in all code files" -ForegroundColor Gray

# Summary
Write-Host "`n=== Cleanup Complete ===" -ForegroundColor Cyan
Write-Host "✓ All test files removed" -ForegroundColor Green
Write-Host "✓ Console statements cleaned" -ForegroundColor Green
Write-Host "✓ Debug comments removed" -ForegroundColor Green
Write-Host "✓ Backup saved to: $backupDir" -ForegroundColor Yellow
Write-Host "`nNext steps:" -ForegroundColor Cyan
Write-Host "1. Test theme functionality" -ForegroundColor White
Write-Host "2. Check browser console for errors" -ForegroundColor White
Write-Host "3. Verify admin panels work correctly" -ForegroundColor White
Write-Host "4. Run npm run test:e2e if tests exist" -ForegroundColor White
