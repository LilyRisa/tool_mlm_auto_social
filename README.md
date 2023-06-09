
# Công cụ upload short video lên các nền tảng cho mục đích cá nhân

## Các tính năng chính

- Đảo ngược khung hình video và ghép nhạc hàng loạt
- Upload video lên các nền tảng như youtube short (phải xác minh ứng dụng nếu không video mặc định sẽ bị private và không thể chỉnh sửa)
- Upload lên facebook page reel
- Upload lên instagram (chưa hoàn thành)
- Upload lên twitter (chưa hoàn thành)

## Cách sử dụng
[+] Chỉ hoạt động trên windows cli

[+] Do ứng dụng chạy trên cli nên vấn đề xác thực có chút khó khăn

### Cài đặt
- Sao chép kho lưu trữ này ``` https://github.com/LilyRisa/tool_mlm_auto_social.git ``` 
- Đảm bảo sử dụng php 7 và cấu hình environment php [tại đây](https://dinocajic.medium.com/add-xampp-php-to-environment-variables-in-windows-10-af20a765b0ce)
- Cài đặt [composer](https://getcomposer.org/)
- Tại thư mục gốc chạy lệnh ``` composer install ```
- Mở file .env sửa các giá trị như facebook app, google Oauth
- Chạy ``` php index.php ```
- Phải Xác thực để lấy thông tin xác thực cho những tính năng khác (Get access token facebook, Login google)
 

### Khởi chạy
- Import file db.sql vào database
- Ứng dụng đã sử dụng database để lưu trữ nhiều tài khoản. Tải file ```server.php``` lên server và chỉnh sửa thông tin connect database để làm api cho ứng dụng chính
- Lần đầu khởi chạy hãy khởi chạy qua giao diện cli ``` php index.php ``` để tạo thông tin đăng nhập và lưu trữ database thông qua api
- Chạy các tính năng cụ thể :
- ``` php index.php --convert {video folder path} {audio folder path} {excute path}``` hoặc ``` php index.php -c {video folder path} {audio folder path} {excute path}``` để chỉnh sửa tất cả video trong thư mục {video folder path} và ghép ngẫu nhiên các bản nhạc từ {audio folder path} sau đó xuất video mới ra {excute path}
    - Ví dụ: ``` php index.php -c "K:\reup\video" "K:\reup\audio" "K:\reup\excute" ```
- ``` php index.php --show-page-facebook``` hoặc ``` php index.php -spf``` để hiển thị ra các page mà tài khoản facebook có quyền upload post và chọn page muốn đăng tải video
- ``` php index.php --cron-upload-facebook {video folder path} {page id}``` hoặc ``` php index.php -cuf {video folder path}``` để upload ngẫu nhiên 1 video trong thư mục video lên facebook reel. Nếu thành công sẽ lưu lại đường dẫn tránh trùng lặp video upload (page id là thông tin lưu trữ trong database)
    - ví dụ: ``` php index.php ----cron-upload-facebook "K:\reup\excute" "2312421471236"```
- ``` php index.php --upload-youtube {video folder path} {tên kênh}``` hoặc ``` php index.php -uy {video folder path} {tên kênh}``` để upload ngẫu nhiên 1 video trong thư mục video lên youtube short (Tên kênh lưu trữ trong database)


## Tech Stack

**Công nghệ:** php 7, facebook api, google api



## Hỗ trợ

For support, email bui.nthl@gmail.com or telegram @Bronoz.