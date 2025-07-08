# Hướng dẫn sử dụng Font Roboto Local

## Tổng quan
Website hiện đã được cấu hình để sử dụng font Roboto từ file local thay vì Google Fonts. Điều này giúp:
- Tăng tốc độ load trang
- Giảm phụ thuộc vào dịch vụ bên ngoài
- Hoạt động tốt hơn trên hosting giới hạn như InfinityFree

## Cấu trúc files

### Font files (trong thư mục `static/`)
```
static/
├── Roboto-Regular.ttf          # Font thường (weight: 400)
├── Roboto-Medium.ttf           # Font trung bình (weight: 500)
├── Roboto-SemiBold.ttf         # Font nửa đậm (weight: 600)
├── Roboto-Bold.ttf             # Font đậm (weight: 700)
├── Roboto-Light.ttf            # Font mỏng (weight: 300)
├── Roboto-Thin.ttf             # Font rất mỏng (weight: 100)
├── Roboto-Italic.ttf           # Font nghiêng thường
├── Roboto-MediumItalic.ttf     # Font nghiêng trung bình
└── Roboto-BoldItalic.ttf       # Font nghiêng đậm
```

### CSS files
```
assets/css/
├── roboto-local.css            # Định nghĩa @font-face cho Roboto
└── font-fallback.css           # CSS fallback (đã cập nhật)
```

### JavaScript files
```
assets/js/
├── roboto-handler.js           # Handler chính cho Roboto
└── font-handler.js             # Handler cũ (vẫn giữ để fallback)
```

### Test file
```
test-roboto.html                # File test font Roboto
```

## Cách hoạt động

1. **Load CSS**: `roboto-local.css` định nghĩa các `@font-face` cho tất cả variants của Roboto
2. **JavaScript Handler**: `roboto-handler.js` kiểm tra xem font đã load thành công chưa
3. **Fallback**: Nếu font không load được, tự động chuyển sang system fonts
4. **Classes**: Tự động thêm class `roboto-loaded` hoặc `roboto-fallback` để apply styles

## Font weights có sẵn

| Weight | File | Sử dụng CSS |
|--------|------|-------------|
| 100 | Roboto-Thin.ttf | `font-weight: 100` |
| 300 | Roboto-Light.ttf | `font-weight: 300` |
| 400 | Roboto-Regular.ttf | `font-weight: 400` (mặc định) |
| 500 | Roboto-Medium.ttf | `font-weight: 500` |
| 600 | Roboto-SemiBold.ttf | `font-weight: 600` |
| 700 | Roboto-Bold.ttf | `font-weight: 700` |

## Cách sử dụng trong CSS

### Sử dụng biến CSS
```css
/* Đã được định nghĩa sẵn */
:root {
    --font-roboto: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* Áp dụng */
.my-element {
    font-family: var(--font-roboto);
}
```

### Sử dụng trực tiếp
```css
.my-element {
    font-family: 'Roboto', sans-serif;
    font-weight: 500; /* Medium */
}

.my-heading {
    font-family: 'Roboto', sans-serif;
    font-weight: 700; /* Bold */
}
```

## Kiểm tra và debug

### 1. Mở file test
Truy cập `test-roboto.html` để kiểm tra:
- Font có load thành công không
- So sánh với system fonts
- Test các font weights khác nhau
- Debug tools

### 2. Console commands
```javascript
// Kiểm tra trạng thái
RobotoFontHandler.state

// Force reload
RobotoFontHandler.forceReload()

// Kiểm tra files
RobotoFontHandler.checkFiles()
```

### 3. Kiểm tra trong Developer Tools
1. Mở F12 > Network tab
2. Reload trang
3. Filter "Font" để xem font files có load không
4. Kiểm tra Console để xem logs

## Xử lý sự cố

### Font không hiển thị
1. **Kiểm tra file paths**: Đảm bảo các file .ttf có trong thư mục `static/`
2. **Kiểm tra permissions**: File phải có quyền đọc
3. **Kiểm tra Console**: Xem có lỗi 404 hoặc lỗi CORS không

### Font load chậm
1. **Check network**: Font files có thể lớn (khoảng 100-200KB mỗi file)
2. **Preload fonts**: Có thể thêm `<link rel="preload">` cho các font chính
3. **Optimize**: Có thể dùng font subset nếu cần

### Fallback không hoạt động
1. **Check JavaScript**: Đảm bảo `roboto-handler.js` load thành công
2. **Check CSS order**: `roboto-local.css` phải load trước `style.css`
3. **Check classes**: Element phải có class `roboto-fallback` khi fallback

## Tối ưu hóa

### Preload critical fonts
Thêm vào `<head>`:
```html
<link rel="preload" href="static/Roboto-Regular.ttf" as="font" type="font/ttf" crossorigin>
<link rel="preload" href="static/Roboto-Medium.ttf" as="font" type="font/ttf" crossorigin>
<link rel="preload" href="static/Roboto-Bold.ttf" as="font" type="font/ttf" crossorigin>
```

### Subset fonts (nếu cần)
Nếu muốn giảm kích thước file, có thể tạo subset fonts chỉ chứa ký tự cần thiết.

### Lazy loading
Có thể lazy load các font weights ít dùng.

## Backup và di chuyển

### Khi deploy lên hosting
1. Upload toàn bộ thư mục `static/` với các file .ttf
2. Đảm bảo file permissions đúng
3. Test trên hosting thực tế

### Backup fonts
Lưu trữ các file font gốc ở nơi an toàn để có thể khôi phục khi cần.

## Events để lắng nghe

```javascript
// Khi Roboto load thành công
window.addEventListener('robotoLoaded', (event) => {
    console.log('Roboto loaded!', event.detail);
});

// Khi fallback được áp dụng
window.addEventListener('robotoFallback', (event) => {
    console.log('Fallback applied!', event.detail);
});
```

## Lưu ý quan trọng

1. **File size**: Mỗi font file khoảng 100-200KB, tổng cộng có thể 1-2MB
2. **Caching**: Browser sẽ cache fonts, lần đầu có thể chậm
3. **CORS**: Font files phải cùng domain hoặc có CORS headers đúng
4. **Mobile**: Test kỹ trên mobile vì có thể có hạn chế về bandwidth

## Rollback về Poppins (nếu cần)

Nếu muốn quay lại Poppins, chỉ cần:
1. Comment dòng `<link href="assets/css/roboto-local.css">` trong header.php
2. Comment dòng `<script src="assets/js/roboto-handler.js">` trong header.php
3. Hệ thống sẽ tự động fallback về Poppins/system fonts 