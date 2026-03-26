# Travel Star - PHP Server Setup Script
# Run this in PowerShell as Administrator

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  Travel Star - PHP Server Setup" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

$projectPath = "c:\Users\mohamed samir\Downloads\New folder"
$phpZip = "$projectPath\php.zip"
$phpFolder = "$projectPath\php"

# Check if PHP already exists
if (Test-Path "$phpFolder\php.exe") {
    Write-Host "[OK] PHP already installed!" -ForegroundColor Green
    Write-Host ""
    Write-Host "To start the server, run: start-server.bat" -ForegroundColor Yellow
    exit
}

Write-Host "[INFO] Downloading PHP..." -ForegroundColor Yellow
Write-Host "[INFO] This may take a few minutes..." -ForegroundColor Gray
Write-Host ""

try {
    # Try to download PHP
    $url = "https://windows.php.net/downloads/releases/php-8.3.6-Win32-vs16-x64.zip"
    
    # Create WebClient with TLS 1.2
    [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
    $webClient = New-Object System.Net.WebClient
    $webClient.DownloadFile($url, $phpZip)
    
    Write-Host "[OK] Download complete!" -ForegroundColor Green
    Write-Host ""
    
    # Extract PHP
    Write-Host "[INFO] Extracting PHP..." -ForegroundColor Yellow
    Expand-Archive -Path $phpZip -DestinationPath $phpFolder -Force
    
    Write-Host "[OK] PHP extracted!" -ForegroundColor Green
    Write-Host ""
    
    # Clean up
    Remove-Item $phpZip -Force
    
    Write-Host "==========================================" -ForegroundColor Green
    Write-Host "  Setup Complete!" -ForegroundColor Green
    Write-Host "==========================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "To start the server:" -ForegroundColor Cyan
    Write-Host "  1. Open VS Code terminal" -ForegroundColor White
    Write-Host "  2. Run: .\start-server.bat" -ForegroundColor White
    Write-Host ""
    Write-Host "Or manually:" -ForegroundColor Cyan
    Write-Host "  php\php.exe -S localhost:8000" -ForegroundColor White
    Write-Host ""
    Write-Host "Then open: http://localhost:8000/admin.html" -ForegroundColor Yellow
    
} catch {
    Write-Host "[ERROR] Download failed!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please download PHP manually:" -ForegroundColor Yellow
    Write-Host "1. Go to: https://windows.php.net/download" -ForegroundColor Cyan
    Write-Host "2. Download: VS16 x64 Non Thread Safe" -ForegroundColor Cyan
    Write-Host "3. Extract to: $phpFolder" -ForegroundColor Cyan
    Write-Host "4. Run: .\start-server.bat" -ForegroundColor Cyan
}
