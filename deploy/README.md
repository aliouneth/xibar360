# Deployment

## Scheduled news refresh

`routes/console.php` registers the `news:scan` command to run **every two hours**
(at 00:00, 02:00, 04:00 … GMT):

```php
Schedule::command('news:scan')
    ->cron('0 */2 * * *')
    ->timezone('GMT')
    ->withoutOverlapping(30)
    ->name('news-scan');
```

Laravel does not run that schedule by itself. On the server, one cron entry must
call `php artisan schedule:run` every minute; the framework then fires the tasks
that are due.

### Install on the VPS

Run the installer as the user that owns the application files (usually
`www-data`, or your deploy user):

```bash
cd /var/www/xibar360
sudo -u www-data bash deploy/setup-scheduler.sh
```

If `php` is not on cron's PATH, pass the binary explicitly:

```bash
sudo -u www-data bash deploy/setup-scheduler.sh --php /usr/bin/php8.3
```

Preview without changing anything:

```bash
bash deploy/setup-scheduler.sh --dry-run
```

The script is idempotent: re-running it updates the existing entry instead of
adding a duplicate.

### What gets installed

```cron
* * * * * cd /var/www/xibar360 && /usr/bin/php8.3 artisan schedule:run >> /var/www/xibar360/storage/logs/scheduler.log 2>&1
```

### Verify

```bash
# the schedule definition
sudo -u www-data /usr/bin/php8.3 artisan schedule:list

# run every registered task immediately
sudo -u www-data /usr/bin/php8.3 artisan schedule:test

# scheduler output
tail -f storage/logs/scheduler.log
```

After the next even GMT hour the admin dashboard's **Last refresh** line and the
`news-last-refresh` cache entry should update without anyone clicking
**Refresh News**.

### Requirements

- `CACHE_STORE` must not be `array` in `.env`. `withoutOverlapping()` uses the
  cache as a lock; the `array` store is per-process and cannot prevent
  overlapping runs. `file`, `database` or `redis` all work.
- The cron daemon must be running: `sudo systemctl enable --now cron`
  (or `crond` on RHEL-family systems).
- Run the scheduler as the same user that owns `storage/`, otherwise imports
  create root-owned files the web server cannot write.

### Troubleshooting

If a run hangs and blocks every following slot, clear the lock:

```bash
php artisan schedule:clear-cache
```

### Remove

```bash
crontab -u www-data -l | grep -v '/var/www/xibar360' | crontab -u www-data -
```
