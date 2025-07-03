# 🔐 Hướng Dẫn Sử Dụng Environment Variables

## Tại sao cần Environment Variables?

Khi push code lên Git, **KHÔNG BAO GIỜ** được commit các thông tin nhạy cảm như:
- Database passwords
- API keys
- Secret tokens
- Email passwords

Environment Variables giúp bạn:
- ✅ Bảo mật thông tin nhạy cảm
- ✅ Dễ dàng deploy trên nhiều môi trường khác nhau
- ✅ Không lo lộ thông tin khi push lên Git

## 🚀 Cách Setup

### Bước 1: Tạo file .env

```bash
# Copy từ template
cp env.example .env
```

### Bước 2: Cập nhật thông tin trong .env

Mở file `.env` và cập nhật thông tin database của bạn:

```env
# Database Configuration
DB_HOST=learnenglish-dental-st.b.aivencloud.com
DB_PORT=13647
DB_NAME=web_ratingbook
DB_USER=avnadmin
DB_PASS=AVNS_PABpPxTbYo7xMw3ictV
DB_CHARSET=utf8mb4

# Site Configuration
SITE_NAME=BookReview
SITE_URL=https://yourdomain.com
SITE_DESCRIPTION="Đánh giá và chia sẻ những cuốn sách tuyệt vời nhất"

# Environment
ENVIRONMENT=production

# Security (for production)
SESSION_SECURE=true
SESSION_HTTPONLY=true
```

### Bước 3: Install Composer Dependencies (Tùy chọn)

Nếu bạn muốn sử dụng thư viện vlucas/phpdotenv:

```bash
# Install Composer (nếu chưa có)
# Tải từ: https://getcomposer.org/download/

# Install dependencies
composer install
```

**Lưu ý:** Ngay cả khi không có Composer, hệ thống vẫn hoạt động bình thường vì có fallback method.

### Bước 4: Test kết nối

Truy cập: `http://localhost/your-project/config/test_connection.php`

## 📁 Cấu trúc Files

```
your-project/
├── .env                    # File chứa thông tin thực (KHÔNG commit)
├── env.example            # Template file (commit được)
├── .gitignore            # Đã config ignore .env
├── composer.json         # Composer config
├── config/
│   ├── env.php          # Environment loader
│   ├── database.php     # Database config (đã update)
│   └── test_connection.php # Test script (đã update)
```

## 🔥 Commands để push lên Git an toàn

```bash
# 1. Add .gitignore trước
git add .gitignore

# 2. Add các files khác (KHÔNG add .env)
git add .
git status  # Kiểm tra .env KHÔNG được add

# 3. Commit và push
git commit -m "Add environment variables support"
git push origin main
```

## 🌐 Deploy lên Production

### Cách 1: Tạo .env trên server

```bash
# SSH vào server
ssh user@your-server

# Tạo file .env
nano /path/to/your-project/.env

# Paste nội dung với thông tin production
# Save và exit
```

### Cách 2: Sử dụng Server Environment Variables

Một số hosting provider cho phép set environment variables từ control panel.

### Cách 3: Sử dụng hosting-specific config

Ví dụ với Heroku:
```bash
heroku config:set DB_HOST=your-production-host
heroku config:set DB_USER=your-production-user
# ... etc
```

## 🛠️ Troubleshooting

### Lỗi: "Database connection failed"

1. **Kiểm tra file .env có tồn tại không:**
   ```bash
   ls -la | grep .env
   ```

2. **Kiểm tra syntax trong .env:**
   - Không có spaces around `=`
   - Sử dụng quotes cho values có spaces
   - Không có trailing spaces

3. **Test connection:**
   ```
   http://localhost/your-project/config/test_connection.php
   ```

### File .env bị lộ lên Git

```bash
# Nếu đã commit .env, remove khỏi Git history
git rm --cached .env
git commit -m "Remove .env from tracking"

# Đảm bảo .gitignore có .env
echo ".env" >> .gitignore
git add .gitignore
git commit -m "Add .env to gitignore"
```

## ✅ Checklist trước khi push

- [ ] File `.env` có trong `.gitignore`
- [ ] Chạy `git status` và đảm bảo `.env` KHÔNG được track
- [ ] Test website hoạt động với environment variables
- [ ] File `env.example` có tất cả keys cần thiết (nhưng không có values thật)

## 🔍 Additional Security Tips

1. **Đối với production:**
   - Set `ENVIRONMENT=production`
   - Set `SESSION_SECURE=true` (nếu dùng HTTPS)
   - Disable error display

2. **Regular rotation:**
   - Thay đổi database passwords định kỳ
   - Update .env files trên tất cả servers

3. **Backup:**
   - Backup file .env production một cách an toàn
   - Không gửi qua email/chat tools

Chúc bạn deploy thành công! 🚀 