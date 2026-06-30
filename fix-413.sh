#!/bin/bash
# fix-413.sh — fixes nginx 413 "Request Entity Too Large" for file upload forms
# Run with: sudo bash fix-413.sh

set -e

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  413 Upload Fix — nginx + PHP"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# ── 1. Find the active nginx site config ─────────────────────────────────────

NGINX_CONF=""

# Priority: sites-enabled (symlinks) first, then sites-available, then nginx.conf
for candidate in \
    /etc/nginx/sites-enabled/default \
    /etc/nginx/sites-available/default \
    /etc/nginx/conf.d/default.conf \
    /etc/nginx/nginx.conf; do
    if [ -f "$candidate" ]; then
        NGINX_CONF="$candidate"
        break
    fi
done

# Also check sites-enabled for any non-default entry
if [ -z "$NGINX_CONF" ] && [ -d /etc/nginx/sites-enabled ]; then
    NGINX_CONF=$(ls /etc/nginx/sites-enabled/ 2>/dev/null | head -1)
    [ -n "$NGINX_CONF" ] && NGINX_CONF="/etc/nginx/sites-enabled/$NGINX_CONF"
fi

if [ -z "$NGINX_CONF" ]; then
    echo "ERROR: Could not find nginx config. Edit manually and add:"
    echo "  client_max_body_size 20M;"
    exit 1
fi

echo "[nginx] Using config: $NGINX_CONF"

# Back up the config
cp "$NGINX_CONF" "${NGINX_CONF}.bak-413fix"
echo "[nginx] Backup saved to ${NGINX_CONF}.bak-413fix"

# Check if client_max_body_size already exists in this file
if grep -q "client_max_body_size" "$NGINX_CONF"; then
    # Update the existing value
    sed -i 's/client_max_body_size\s*[^;]*;/client_max_body_size 20M;/g' "$NGINX_CONF"
    echo "[nginx] Updated existing client_max_body_size → 20M"
else
    # Insert after the first 'server {' line
    sed -i '/^[[:space:]]*server[[:space:]]*{/a\    client_max_body_size 20M;' "$NGINX_CONF"
    echo "[nginx] Inserted client_max_body_size 20M into server block"
fi

# ── 2. Fix PHP upload limits ──────────────────────────────────────────────────

# Auto-detect the running PHP version and its fpm php.ini
PHP_VERSION=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || echo "")
PHP_INI=""

if [ -n "$PHP_VERSION" ]; then
    for candidate in \
        "/etc/php/${PHP_VERSION}/fpm/php.ini" \
        "/etc/php/${PHP_VERSION}/cli/php.ini" \
        "/etc/php/php.ini"; do
        if [ -f "$candidate" ]; then
            PHP_INI="$candidate"
            break
        fi
    done
fi

# Fallback: ask php --ini
if [ -z "$PHP_INI" ]; then
    PHP_INI=$(php --ini 2>/dev/null | grep "Loaded Configuration" | awk '{print $NF}')
fi

if [ -z "$PHP_INI" ] || [ ! -f "$PHP_INI" ]; then
    echo "[php] WARNING: Could not find php.ini. Skipping PHP limits."
    echo "      Set manually: upload_max_filesize=10M  post_max_size=25M"
else
    echo "[php] Using ini: $PHP_INI"
    cp "$PHP_INI" "${PHP_INI}.bak-413fix"

    # upload_max_filesize
    if grep -q "^upload_max_filesize" "$PHP_INI"; then
        sed -i 's/^upload_max_filesize\s*=.*/upload_max_filesize = 10M/' "$PHP_INI"
    else
        echo "upload_max_filesize = 10M" >> "$PHP_INI"
    fi

    # post_max_size
    if grep -q "^post_max_size" "$PHP_INI"; then
        sed -i 's/^post_max_size\s*=.*/post_max_size = 25M/' "$PHP_INI"
    else
        echo "post_max_size = 25M" >> "$PHP_INI"
    fi

    echo "[php] Set upload_max_filesize=10M  post_max_size=25M"
fi

# ── 3. Test nginx config and reload ──────────────────────────────────────────

echo ""
echo "Testing nginx config…"
nginx -t

echo "Reloading nginx…"
systemctl reload nginx
echo "[nginx] Reloaded ✓"

# ── 4. Restart PHP-FPM ───────────────────────────────────────────────────────

FPM_SERVICE=""
for svc in "php${PHP_VERSION}-fpm" "php-fpm" "php8.2-fpm" "php8.1-fpm" "php8.0-fpm"; do
    if systemctl is-active --quiet "$svc" 2>/dev/null; then
        FPM_SERVICE="$svc"
        break
    fi
done

if [ -n "$FPM_SERVICE" ]; then
    systemctl restart "$FPM_SERVICE"
    echo "[php-fpm] Restarted $FPM_SERVICE ✓"
else
    echo "[php-fpm] WARNING: Could not find running php-fpm service. Restart it manually."
fi

# ── 5. Verify ────────────────────────────────────────────────────────────────

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  Verification"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo -n "nginx client_max_body_size : "
grep "client_max_body_size" "$NGINX_CONF" || echo "(not found — check manually)"

if [ -n "$PHP_INI" ] && [ -f "$PHP_INI" ]; then
    echo -n "PHP upload_max_filesize    : "
    grep "^upload_max_filesize" "$PHP_INI" || echo "(not found)"
    echo -n "PHP post_max_size          : "
    grep "^post_max_size" "$PHP_INI" || echo "(not found)"
fi

echo ""
echo "Done. Try submitting the application form again."
echo ""
