Start-Process -FilePath "C:\wamp64\bin\php\php8.3.28\php.exe" -ArgumentList "artisan", "serve", "--port=3002", "--host=0.0.0.0" -WorkingDirectory "C:\sununews" -NoNewWindow -PassThru | Out-Null
Write-Host "Server started on port 3002"
