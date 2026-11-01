#!/usr/bin/env bash
#
# setup-scheduler.sh — install the Laravel scheduler cron entry for Xibar360.
#
# Laravel does not run scheduled tasks by itself: something has to call
# `php artisan schedule:run` once per minute, and the framework then decides
# which registered tasks are due (here: `news:scan` every two hours).
#
# Usage:
#   sudo -u www-data bash deploy/setup-scheduler.sh
#   bash deploy/setup-scheduler.sh --user www-data --php /usr/bin/php8.3
#   bash deploy/setup-scheduler.sh --dry-run
#
# Options:
#   --app <dir>    Application root (default: this script's parent directory)
#   --php <path>   PHP CLI binary (default: `php` from PATH)
#   --user <name>  Unix user whose crontab receives the entry (default: you)
#   --dry-run      Show what would be installed without touching crontab
#
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PHP_BIN="$(command -v php || true)"
RUN_USER="$(id -un)"
DRY_RUN=0

usage() {
    sed -n '3,19p' "${BASH_SOURCE[0]}" | sed 's/^# \{0,1\}//'
}

while [[ $# -gt 0 ]]; do
    case "$1" in
        --app)     APP_DIR="${2:?--app needs a directory}"; shift 2 ;;
        --php)     PHP_BIN="${2:?--php needs a path}"; shift 2 ;;
        --user)    RUN_USER="${2:?--user needs a name}"; shift 2 ;;
        --dry-run) DRY_RUN=1; shift ;;
        -h|--help) usage; exit 0 ;;
        *) echo "Unknown option: $1" >&2; usage >&2; exit 1 ;;
    esac
done

# --- validation -----------------------------------------------------------

# cron runs with a minimal PATH, so the entry must use an absolute binary.
if [[ -n "$PHP_BIN" && "$PHP_BIN" != /* ]]; then
    PHP_BIN="$(command -v "$PHP_BIN" || true)"
fi

if [[ -z "$PHP_BIN" || ! -x "$PHP_BIN" ]]; then
    echo "error: PHP CLI not found. Pass it explicitly, e.g. --php /usr/bin/php8.3" >&2
    exit 1
fi

if [[ ! -f "$APP_DIR/artisan" ]]; then
    echo "error: $APP_DIR/artisan not found. Pass the app root, e.g. --app /var/www/xibar360" >&2
    exit 1
fi

if ! id "$RUN_USER" >/dev/null 2>&1; then
    echo "error: user '$RUN_USER' does not exist." >&2
    exit 1
fi

if [[ "$(id -u)" -ne 0 && "$RUN_USER" != "$(id -un)" ]]; then
    echo "error: only root can install a crontab for another user." >&2
    echo "       Run as $RUN_USER, or use: sudo -u $RUN_USER bash $0" >&2
    exit 1
fi

if [[ "$(id -u)" -eq 0 && "$RUN_USER" == "root" ]]; then
    echo "error: refusing to install the scheduler in root's crontab." >&2
    echo "       Root-owned imports would create files the web server cannot write." >&2
    echo "       Run as the app owner instead: sudo -u www-data bash $0" >&2
    echo "       or pass --user www-data." >&2
    exit 1
fi

LOG_FILE="$APP_DIR/storage/logs/scheduler.log"
if [[ ! -d "$APP_DIR/storage/logs" ]]; then
    mkdir -p "$APP_DIR/storage/logs"
    if [[ "$(id -u)" -eq 0 && "$RUN_USER" != "root" ]]; then
        chown -R "$RUN_USER": "$APP_DIR/storage/logs"
    fi
fi

CRON_LINE="* * * * * cd $APP_DIR && $PHP_BIN artisan schedule:run >> $LOG_FILE 2>&1"

# --- install --------------------------------------------------------------

crontab_cmd=(crontab)
if [[ "$(id -u)" -eq 0 ]]; then
    crontab_cmd=(crontab -u "$RUN_USER")
fi

current="$("${crontab_cmd[@]}" -l 2>/dev/null || true)"

if printf '%s\n' "$current" | grep -Fqx "$CRON_LINE"; then
    echo "Scheduler entry already installed for $RUN_USER:"
    echo "  $CRON_LINE"
elif [[ "$DRY_RUN" -eq 1 ]]; then
    echo "[dry-run] would install for user $RUN_USER:"
    echo "  $CRON_LINE"
else
    # Drop any older entry pointing at this same app, then append the fresh one,
    # so re-running the script after a PHP upgrade updates the binary path.
    kept="$(printf '%s\n' "$current" | grep -vF "$APP_DIR" || true)"
    { printf '%s\n' "$kept"; printf '%s\n' "$CRON_LINE"; } | "${crontab_cmd[@]}" -
    echo "Installed scheduler entry for $RUN_USER:"
    echo "  $CRON_LINE"
fi

# --- verification ---------------------------------------------------------

if command -v systemctl >/dev/null 2>&1; then
    if systemctl is-active --quiet cron 2>/dev/null || systemctl is-active --quiet crond 2>/dev/null; then
        echo "Cron daemon: running"
    else
        echo "warning: cron does not look active." >&2
        echo "         Start it with: sudo systemctl enable --now cron" >&2
    fi
fi

echo
echo "Registered tasks:"
if [[ "$(id -u)" -eq 0 && "$RUN_USER" != "root" ]] && command -v runuser >/dev/null 2>&1; then
    runuser -u "$RUN_USER" -- "$PHP_BIN" "$APP_DIR/artisan" schedule:list
elif [[ "$(id -u)" -eq 0 && "$RUN_USER" != "root" ]] && command -v sudo >/dev/null 2>&1; then
    sudo -u "$RUN_USER" "$PHP_BIN" "$APP_DIR/artisan" schedule:list
else
    "$PHP_BIN" "$APP_DIR/artisan" schedule:list
fi

echo
echo "Done. The scheduler will refresh news on the next even GMT hour."
echo "Follow it with: tail -f $LOG_FILE"
