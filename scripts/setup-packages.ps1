# Setup script: install packages for permissions, websockets and printing
# Run this from project root in PowerShell (as administrator if needed)

Write-Host "Installing composer packages..."
composer require spatie/laravel-permission pusher/pusher-php-server beyondcode/laravel-websockets mike42/escpos-php --no-interaction

Write-Host "Publishing vendor files and migrations..."
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="config" --force
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations" --force || Write-Host "Spatie migrations may be published automatically by package"
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config" --force
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="migrations" --force || Write-Host "WebSockets migrations may be published automatically"

Write-Host "Running migrations..."
php artisan migrate

Write-Host "Clearing and caching config"
php artisan config:clear
php artisan config:cache

Write-Host "Packages installed and published. Update your .env with the following keys as needed:" 
Write-Host "  BROADCAST_DRIVER=pusher (or reverb)
  PUSHER_APP_ID=your-id
  PUSHER_APP_KEY=your-key
  PUSHER_APP_SECRET=your-secret
  PUSHER_APP_CLUSTER=mt1
  LARAVEL_WEBSOCKETS_PORT=6001
  WEBSOCKETS_SSL_LOCAL_CERT=path/to/cert.pem
  WEBSOCKETS_SSL_LOCAL_PK=path/to/key.pem"

Write-Host "If using Spatie permissions, run:
  php artisan permission:cache-reset
"

Write-Host "Done."