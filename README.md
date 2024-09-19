# 23JUDGE - ITK23 Online Judge

## 1. Cài đặt
### 1.1 Yêu cầu:
- `Composer`
- `PHP 8.3` hoặc mới hơn

### 1.2 Cài đặt gói phụ thuộc:
Chạy lệnh sau để cài đặt thư viện cần thiết:
```
composer require erusev/parsedown
```

## 2. Host hệ thống
### 2.1 Yêu cầu host:
- Sử dụng `XAMPP` hoặc phần mềm tương tự để host trên cổng `80`.
- **Khuyến nghị:** Dùng `ngrok` để host. Tải `ngrok.exe` vào thư mục `host`, cấu hình API key và thay đổi tên miền trong file `run.bat`, sau đó chạy file này.

### 2.2 Khởi tạo hệ thống Judge:
1. Cài đặt `Docker` và chạy file `docker.bat` trong thư mục `docker`.
2. Chờ cho đến khi cửa sổ `cmd` đóng lại.

**Lưu ý:** Mã nguồn không thể tự động nhận diện địa chỉ của `Judge0`. Bạn cần chỉnh sửa file `judge.php` như sau:
- Tìm đến dòng 152 và 153 trong file `judge.php`.
- Chỉnh sửa 2 biến `$apiUrl` và `$apiUrlCfg`:
```php
$apiUrl = "http://<Tên miền của Judge0>/submissions/";
$apiUrlCfg = "http://<Tên miền của Judge0>/submissions/?wait=false/";
```
- Nếu host `Judge0` trên localhost (cổng mặc định `2358`), thay đổi mã nguồn như sau:
```php
$apiUrl = "http://localhost:2358/submissions/";
$apiUrlCfg = "http://localhost:2358/submissions/?wait=false/";
```

Nếu host `23JUDGE` trên server khác, hãy cấu hình file `judge.bat` để khởi chạy `Judge0` tương tự như file `run.bat` và thay đổi tên miền trong mã nguồn.

## 3. Tạo Problem:
1. Trong thư mục `/problems`, tạo một thư mục mới với tên là tên của câu hỏi (chỉ chứa ký tự chữ cái và số).
2. Trong thư mục mới, tạo các file cấu hình sau:
   - `MemoryLimit.cfg`: Giới hạn bộ nhớ (KB). Ví dụ: `128000`.
   - `TimeLimit.cfg`: Thời gian thực thi tối đa (giây). Ví dụ: `1`.
   - `RScore.cfg`: Điểm xếp hạng của bài khi A/C. Ví dụ: `0.1`.
   - `Score.cfg`: Điểm của bài, được chia đều cho số lượng test. Ví dụ: `10`.
   - `ShowTest.cfg`: Cấu hình xem test (`true` hoặc `false`).
   - `StopWhenFail.cfg`: Dừng khi gặp lỗi (`true` hoặc `false`).
   - `Problem.md`: Nội dung câu hỏi (Markdown, hỗ trợ MathJax).

### 3.1 Tạo test case:
Tạo thư mục `tests` bên trong thư mục câu hỏi với cấu trúc:
```
tests/TEST<name>/input.txt
tests/TEST<name>/output.txt
```
Ví dụ:
```
tests/TEST1/input.txt
tests/TEST1/output.txt
tests/TEST2/input.txt
tests/TEST2/output.txt
...
```

## 4. Tạo Contest:
1. Tạo file `contest.cfg` với 2 thông số:
   - `Opening`: Thời gian mở contest.
   - `Closing`: Thời gian đóng contest.

**Lưu ý:** Người đã tham gia contest sẽ không bị đẩy ra khi contest đóng.

2. Tạo các thư mục phục vụ contest:
   - `submissions`: Lưu trữ bài nộp, bao gồm file `Log.txt` để lưu lịch sử.
   - `users`: Lưu xếp hạng người dùng.
   - `problems`: Lưu câu hỏi, tạo câu hỏi giống như trong phần "Tạo Problem".

## 5. Tiện ích hỗ trợ:
- `root/itk23.php`: Tạo tài khoản với cùng một mật khẩu.
- `root/passgen.php`: Truy cập GET để tạo mật khẩu hash.
- `root/clean.php`: Xóa các bài nộp đã hơn 1 tháng.
- `cses-tests/auto.bat`: Chuyển đổi bộ test của CSES hoặc các hệ thống khác sang định dạng của 23JUDGE.

## 6. Tạo tài khoản người dùng:
23JUDGE không hỗ trợ tạo tài khoản tự động qua giao diện người dùng, bạn cần tạo thủ công.

### 6.1 Tạo tài khoản:
1. Sử dụng tên và mật khẩu đã hash (dùng tiện ích trong phần trên).
2. Chỉnh sửa file `users.txt` trong thư mục `databases`:
```
<tên tài khoản>:<mật khẩu đã hash>
```

### 6.2 Tài khoản có sẵn:
- Tên tài khoản: `admin`
- Mật khẩu: `itk23maidinh`

## 7. Ảnh chụp màn hình:
![image](https://github.com/user-attachments/assets/11e39e19-97dd-49ed-973d-ce2f54e76546)
![image](https://github.com/user-attachments/assets/0257c678-0984-4c79-b1e3-ffb79d3d2e96)
![image](https://github.com/user-attachments/assets/80c08d92-e5fe-42fc-83ce-5da86c18cd64)
