#!/usr/bin/env sh
# Entrypoint container aplikasi AgentBuff KostCloud.
# 1) Menunggu MySQL siap  2) migrate (+ seed saat DB baru)  3) menjalankan server.
set -e
cd /var/www/html

# ---------------------------------------------------------------------------
# Sinkronkan environment dari docker-compose ke file .env.
# Penting: `php artisan serve` meneruskan variabel dari FILE .env ke server
# built-in-nya, sehingga override lewat `environment:` compose diabaikan untuk
# request HTTP. Menulis nilai ini ke .env membuat CLI & HTTP konsisten.
# ---------------------------------------------------------------------------
set_env() {
    key="$1"; val="$2"
    if grep -qE "^${key}=" .env 2>/dev/null; then
        esc=$(printf '%s' "$val" | sed -e 's/[&|\\]/\\&/g')
        sed -i "s|^${key}=.*|${key}=${esc}|" .env
    else
        printf '%s=%s\n' "$key" "$val" >> .env
    fi
}

for k in APP_NAME APP_ENV APP_KEY APP_DEBUG APP_URL \
         APP_LOCALE APP_FALLBACK_LOCALE APP_FAKER_LOCALE APP_MAINTENANCE_DRIVER \
         BCRYPT_ROUNDS LOG_CHANNEL LOG_STACK LOG_DEPRECATIONS_CHANNEL LOG_LEVEL \
         DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD \
         SESSION_DRIVER SESSION_LIFETIME SESSION_ENCRYPT SESSION_PATH SESSION_DOMAIN SESSION_SECURE_COOKIE \
         CACHE_STORE QUEUE_CONNECTION BROADCAST_CONNECTION FILESYSTEM_DISK \
         MAIL_MAILER MAIL_FROM_ADDRESS MAIL_FROM_NAME \
         GOOGLE_CLIENT_ID GOOGLE_CLIENT_SECRET GOOGLE_REDIRECT_URI \
         AGENTBUFF_ENTITLEMENT_URL AGENTBUFF_PARTNER_SECRET KOSTCLOUD_PRODUCT_KEY AGENTBUFF_GATE_DISABLED; do
    if printenv "$k" >/dev/null 2>&1; then
        set_env "$k" "$(printenv "$k")"
    fi
done
echo "🔧 .env disinkronkan dari environment compose."

: "${DB_HOST:=db}"
: "${DB_PORT:=3306}"

echo "⏳ Menunggu MySQL di ${DB_HOST}:${DB_PORT} ..."
ATTEMPTS=0
until php -r '
    try {
        new PDO(
            "mysql:host=".getenv("DB_HOST").";port=".(getenv("DB_PORT") ?: "3306").";dbname=".getenv("DB_DATABASE"),
            getenv("DB_USERNAME"),
            getenv("DB_PASSWORD"),
            [PDO::ATTR_TIMEOUT => 3]
        );
    } catch (Throwable $e) {
        fwrite(STDERR, $e->getMessage().PHP_EOL);
        exit(1);
    }
'; do
    ATTEMPTS=$((ATTEMPTS + 1))
    if [ "$ATTEMPTS" -ge 60 ]; then
        echo "❌ Database tidak siap setelah 60 percobaan. Keluar."
        exit 1
    fi
    echo "   ...belum siap, coba lagi dalam 3 detik (percobaan ${ATTEMPTS})"
    sleep 3
done
echo "✅ MySQL siap."

# Symlink storage publik (aman bila sudah ada)
php artisan storage:link 2>/dev/null || true

# Migrasi. Seed HANYA saat database masih fresh (belum ada tabel migrations),
# supaya restart container tidak menduplikasi data dummy.
if php artisan migrate:status >/dev/null 2>&1; then
    echo "↻ Menerapkan migrasi yang tertunda..."
    php artisan migrate --force
else
    echo "🌱 Database baru terdeteksi — menjalankan migrate + seed..."
    php artisan migrate --seed --force
fi

# Bersihkan cache config & compiled view agar kode terbaru selalu dipakai
# (storage ikut volume, jadi tanpa ini blade lama bisa tersaji setelah rebuild).
php artisan config:clear >/dev/null 2>&1 || true
php artisan view:clear >/dev/null 2>&1 || true

echo "🚀 Aplikasi berjalan di http://localhost:8000"
exec php artisan serve --host=0.0.0.0 --port=8000
