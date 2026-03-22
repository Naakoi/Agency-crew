# Server Configuration Guide

This guide contains everything you need to manage the **Agency Crew Accommodation** server on Oracle Cloud.

## SSH Login Details
To log in from your local computer's terminal:

```bash
ssh -i /home/user/Documents/Projects/Oracle/ssh-key-2026-03-20.key ubuntu@140.238.201.159
```

| Detail | Value |
| :--- | :--- |
| **Server IP** | `140.238.201.159` |
| **Domain** | `https://agencycrew.cppl.com.ki` |
| **Mobile App (APK)** | `https://agencycrew.cppl.com.ki/agency-crew.apk` |
| **Username** | `ubuntu` |
| **Identity Key** | `/home/user/Documents/Projects/Oracle/ssh-key-2026-03-20.key` |

---

## Important File Paths

### 1. Application Code
- **Root Directory**: `/var/www/agency-crew`
- **Laravel Backend**: `/var/www/agency-crew/backend`
- **Env File**: `/var/www/agency-crew/backend/.env`

### 2. Server Configuration
- **Nginx Config**: `/etc/nginx/sites-available/agency-crew`
- **Error Logs**: `/var/www/agency-crew/backend/storage/logs/laravel.log`
- **Nginx Access Logs**: `/var/log/nginx/access.log`

---

## Database (MySQL)
- **Database Name**: `agency_crew_accommodation`
- **Username**: `agency_user`
- **Password**: (Stored in `.env` on server)

---

## Maintenance Commands

### Restart Nginx
```bash
sudo systemctl reload nginx
```

### Apply Code Changes (Git Pull)
```bash
cd /var/www/agency-crew
git pull origin main
cd backend
php artisan migrate
```

### SSL Certificate Renewal
Certbot is set to auto-renew, but you can check it manually:
```bash
sudo certbot renew --dry-run
```
