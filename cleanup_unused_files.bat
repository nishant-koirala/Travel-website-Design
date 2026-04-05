@echo off
echo ========================================
echo Travel Website - Cleanup Unused Files
echo ========================================
echo.
echo This script will move unused files to the 'unused' folder
echo Press any key to continue or Ctrl+C to cancel...
pause >nul
echo.

REM Create unused folder if it doesn't exist
if not exist "unused" mkdir unused
echo Created unused folder
echo.

REM Move unused admin package files
echo Moving unused admin package files...
if exist "admin_setup\package\add_package_enhanced.php" (
    move "admin_setup\package\add_package_enhanced.php" "unused\"
    echo   - Moved add_package_enhanced.php
)

if exist "admin_setup\package\add_package_fixed.php" (
    move "admin_setup\package\add_package_fixed.php" "unused\"
    echo   - Moved add_package_fixed.php
)

if exist "admin_setup\package\add_package_simple.php" (
    move "admin_setup\package\add_package_simple.php" "unused\"
    echo   - Moved add_package_simple.php
)

if exist "admin_setup\package\update_package_enhanced_process.php" (
    move "admin_setup\package\update_package_enhanced_process.php" "unused\"
    echo   - Moved update_package_enhanced_process.php
)

echo.

REM Move unused database files
echo Moving unused database files...
if exist "database\update_nepali_packages_fixed.php" (
    move "database\update_nepali_packages_fixed.php" "unused\"
    echo   - Moved update_nepali_packages_fixed.php
)

if exist "database\direct_update.php" (
    move "database\direct_update.php" "unused\"
    echo   - Moved direct_update.php
)

if exist "database\simple_test.php" (
    move "database\simple_test.php" "unused\"
    echo   - Moved simple_test.php
)

if exist "database\test_packages.php" (
    move "database\test_packages.php" "unused\"
    echo   - Moved test_packages.php
)

if exist "database\quick_update.php" (
    move "database\quick_update.php" "unused\"
    echo   - Moved quick_update.php
)

echo.

REM Move unused root files
echo Moving unused root files...
if exist "check_updates.html" (
    move "check_updates.html" "unused\"
    echo   - Moved check_updates.html
)

if exist "packages_enhanced.php" (
    move "packages_enhanced.php" "unused\"
    echo   - Moved packages_enhanced.php
)

echo.

REM Create a report of what was moved
echo ========================================
echo Cleanup Complete!
echo ========================================
echo.
echo Files moved to unused folder:
echo.
echo Admin Package Files:
echo   - add_package_enhanced.php
echo   - add_package_fixed.php  
echo   - add_package_simple.php
echo   - update_package_enhanced_process.php
echo.
echo Database Files:
echo   - update_nepali_packages_fixed.php
echo   - direct_update.php
echo   - simple_test.php
echo   - test_packages.php
echo   - quick_update.php
echo.
echo Root Files:
echo   - check_updates.html
echo   - packages_enhanced.php
echo.
echo Active files that remain in use:
echo   - package_form.php (main unified form)
echo   - edit_package_enhanced.php (edit functionality)
echo   - packages_enhanced.php (package list)
echo   - update_nepali_packages.php (database updates)
echo   - package_details.php (product details)
echo.
echo ========================================
echo Cleanup completed successfully!
echo ========================================
echo.
echo Press any key to exit...
pause >nul
