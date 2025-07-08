/**
 * Vietnamese Text Handling Script
 * Script xử lý text tiếng Việt trong DOM
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Hàm để áp dụng font và style cho text tiếng Việt
    function applyVietnameseFont() {
        // Các selector cần xử lý
        const selectors = [
            '.card-title', '.card-text', '.book-title', '.book-author',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', '.nav-link',
            '.btn', 'p', 'span', '.text-content', 'td', 'th'
        ];
        
        // Áp dụng class vietnamese-text cho tất cả các phần tử chứa text
        selectors.forEach(selector => {
            document.querySelectorAll(selector).forEach(el => {
                // Thêm class
                el.classList.add('vietnamese-text');
                
                // Đánh dấu ngôn ngữ
                if (!el.hasAttribute('lang')) {
                    el.setAttribute('lang', 'vi');
                }
                
                // Đảm bảo text được hiển thị đúng
                el.style.visibility = 'visible';
                el.style.opacity = '1';
            });
        });
        
        // Xử lý đặc biệt cho tên sách và tên tác giả
        document.querySelectorAll('.card-title, h2.fw-bold').forEach(el => {
            el.classList.add('book-title');
        });
        
        // Xử lý đặc biệt cho text mô tả
        document.querySelectorAll('.card-text, p.text-muted').forEach(el => {
            if (el.textContent.includes('Tác giả:')) {
                el.classList.add('book-author');
            }
        });
    }
    
    // Chạy ngay khi trang được load
    applyVietnameseFont();
    
    // Chạy lại sau một khoảng thời gian để đảm bảo tất cả nội dung đã load xong
    setTimeout(applyVietnameseFont, 500);
    
    // Chạy lại khi có thay đổi trong DOM (như AJAX load)
    const observer = new MutationObserver(function(mutations) {
        applyVietnameseFont();
    });
    
    // Bắt đầu quan sát DOM
    observer.observe(document.body, { 
        childList: true, 
        subtree: true 
    });
    
    // Xử lý khi tất cả font đã được load
    document.fonts.ready.then(() => {
        document.body.classList.add('fonts-loaded');
        applyVietnameseFont();
    });
});
