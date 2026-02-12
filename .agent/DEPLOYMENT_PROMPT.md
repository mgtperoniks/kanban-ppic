# 🔒 PROMPT: Safe Production Deployment untuk Kanban PPIC

Gunakan prompt ini untuk di-copy paste ke AI agent lain ketika mau deploy ke production.

---

## Copy Prompt Berikut:

```
Tolong bantu saya deploy kode dari local (Laragon) ke server production dengan AMAN.

ATURAN PENTING:
1. JANGAN PERNAH jalankan `migrate:fresh` atau `migrate:rollback` di production - ini MENGHAPUS semua data!
2. SELALU backup database dulu jika ada migration baru
3. Perubahan PHP/Blade biasa TIDAK butuh migration

LANGKAH-LANGKAH:

1. Di LOCAL (Laragon):
   - cd c:\laragon\www\kanban-ppic
   - git status → git add -A → git commit -m "pesan" → git push origin main

2. Di SERVER (SSH):
   - ssh peroniks@peroniks-ppicserver
   - cd /srv/docker/apps/kanban-ppic
   - (Jika ada migration baru) Backup DB dulu:
     docker compose exec db mysqldump -u root -p[PASSWORD] kanban-ppic > ~/backups/kanban_backup_$(date +%Y%m%d_%H%M%S).sql
   - Pull kode: git pull origin main
   - Clear cache:
     docker compose exec app php artisan config:clear
     docker compose exec app php artisan view:clear
     docker compose exec app php artisan route:clear
   - (Jika ada migration baru) docker compose exec app php artisan migrate
   - Re-cache: docker compose exec app php artisan config:cache && docker compose exec app php artisan route:cache

3. Verifikasi aplikasi berjalan normal

PERINTAH TERLARANG DI PRODUCTION:
❌ php artisan migrate:fresh
❌ php artisan migrate:fresh --seed
❌ php artisan migrate:rollback
❌ php artisan db:wipe
```

---

## Versi Singkat (Quick Reference)

```
DEPLOY AMAN:
1. Local: git add -A → git commit → git push origin main
2. Server: git pull → clear cache → migrate (BUKAN migrate:fresh!)
3. Verify

⛔ JANGAN: migrate:fresh, migrate:rollback, db:wipe
```
