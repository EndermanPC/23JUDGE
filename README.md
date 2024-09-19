# 23JUDGE - ITK23 Online Judge

## 1. Cài đặt
### 1.1 Yêu cầu:
- `composer` để quản lý thư viện.
- `PHP 8.3` hoặc phiên bản mới hơn.

### 1.2 Cài đặt gói phụ thuộc:
Chạy lệnh sau để cài đặt thư viện cần thiết:
```bash
composer require erusev/parsedown
```

## 2. Host hệ thống
### 2.1 Host qua XAMPP hoặc tương tự:
- Yêu cầu sử dụng XAMPP hoặc phần mềm tương tự để host trên cổng `80`.
- **Khuyến nghị**: Sử dụng `ngrok` để host 23JUDGE công khai. Thực hiện các bước sau:
  - Tải `ngrok.exe` vào thư mục `host`.
  - Cấu hình API key của bạn vào `ngrok`.
  - Thay đổi tên miền trong file `run.bat` theo tên miền được cung cấp bởi `ngrok`, sau đó chạy file `run.bat`.

### 2.2 Khởi tạo hệ thống chấm bài Judge0:
- Cài đặt Docker và chạy file `docker.bat` trong thư mục `docker`.
- Chờ cho đến khi cửa sổ cmd tự động đóng lại.

**Lưu ý**: Mặc định, 23JUDGE không thể tự động nhận diện địa chỉ của Judge0. Bạn cần thực hiện các bước sau để cấu hình thủ công:
1. Mở file `judge.php` và tìm đến dòng 152 và 153.
2. Chỉnh sửa 2 biến `$apiUrl` và `$apiUrlCfg` như sau:
   ```php
   $apiUrl = "http://<Tên miền hoặc IP của Judge0>/submissions/";
   $apiUrlCfg = "http://<Tên miền hoặc IP của Judge0>/submissions/?wait=false/";
   ```
3. Nếu bạn host Judge0 cục bộ (mặc định trên cổng `2358`), bạn có thể cấu hình như sau:
   ```php
   $apiUrl = "http://localhost:2358/submissions/";
   $apiUrlCfg = "http://localhost:2358/submissions/?wait=false/";
   ```

Nếu bạn host 23JUDGE trên một server khác, hãy cấu hình file `judge.bat` để tự động chạy Judge0 (nếu bạn tự host Judge0), đồng thời chỉnh sửa tên miền trong mã nguồn như trên.

## 3. Tạo Problem (Bài tập):
### 3.1 Cấu trúc thư mục Problem:
1. Trong thư mục `/problems`, tạo một thư mục mới với tên là tên của bài tập. **Lưu ý**: Tên của bài không được chứa ký tự đặc biệt (ngoại trừ chữ cái và số).
2. Bên trong thư mục vừa tạo, tạo các file cấu hình:
   - `MemoryLimit.cfg`: Bộ nhớ tối đa (KB) được cấp cho chương trình khi chạy. Ví dụ: `128000` (128MB).
   - `TimeLimit.cfg`: Thời gian chạy tối đa của chương trình (giây). Ví dụ: `1` (1 giây).
   - `RScore.cfg`: Điểm xếp hạng của bài khi người dùng A/C (Accepted) lần đầu. Điểm này chỉ được cấp một lần. Ví dụ: `0.1`.
   - `Score.cfg`: Điểm tổng của bài tập, sẽ được chia đều cho số lượng test. Ví dụ: `10`.
   - `ShowTest.cfg`: Cấu hình `true` hoặc `false` để quyết định người dùng có được xem test khi nộp bài không. Khuyến nghị đặt `false` trong contest.
   - `StopWhenFail.cfg`: Cấu hình `true` hoặc `false` để quyết định máy chấm có dừng khi gặp test sai không. Khuyến nghị đặt `true` để giảm tài nguyên server.
   - `Problem.md`: Nội dung bài tập được viết bằng định dạng markdown (có hỗ trợ MathJax để viết công thức toán).

### 3.2 Tạo Test Case cho Problem:
- Bên trong thư mục bài tập, tạo thư mục `tests` và các thư mục con với định dạng như sau:
```
tests/TEST<name>/input.txt
tests/TEST<name>/output.txt
```
- `<name>` có thể là số hoặc tên tùy ý, ví dụ:
```
tests/TEST1/input.txt
tests/TEST1/output.txt
tests/TEST2/input.txt
tests/TEST2/output.txt
```

## 4. Tạo Contest:
1. Tạo file cấu hình `contest.cfg` với hai trường thông tin quan trọng:
   - `Opening`: Thời gian mở cửa contest.
   - `Closing`: Thời gian đóng cửa contest.

**Lưu ý**: Khi contest đóng, người đã tham gia vẫn có thể tiếp tục nộp bài cho đến khi bị loại hoặc contest chính thức kết thúc.

2. Tạo các thư mục cần thiết cho contest:
   - `submissions`: Lưu trữ danh sách bài nộp, bên trong thư mục này tạo file `Log.txt` để ghi lại lịch sử nộp bài.
   - `users`: Lưu danh sách và xếp hạng người dùng.
   - `problems`: Thư mục lưu trữ bài tập, tạo cấu trúc bài tập giống như phần **Tạo Problem** ở trên.

## 5. Một số tiện ích đi kèm:
- `root/itk23.php`: Tạo nhanh các tài khoản với cùng một mật khẩu.
- `root/passgen.php`: Tạo mật khẩu đã mã hóa hash thông qua phương thức GET (truy cập bằng trình duyệt).
- `root/clean.php`: Xóa các file submissions đã tồn tại hơn 1 tháng.
- `cses-tests/auto.bat`: Chuyển đổi các bộ test từ hệ thống khác (như CSES) sang định dạng của 23JUDGE. Bạn chỉ cần dán các file `.in` và `.out` để tự động chuyển đổi.

## 6. Tạo tài khoản người dùng:
23JUDGE không hỗ trợ tạo tài khoản qua giao diện, vì vậy bạn cần tạo tài khoản thủ công khi cần.

### 6.1 Tạo tài khoản thủ công:
1. Bạn cần có tên tài khoản (chỉ gồm chữ cái và số) và mật khẩu đã mã hóa (hash) bằng tiện ích `passgen.php`.
2. Sau khi có thông tin cần thiết, chỉnh sửa file `users.txt` trong thư mục `databases`:
   ```
   <tên tài khoản>:<mật khẩu đã hash>
   ```

### 6.2 Tài khoản mặc định:
- Tên tài khoản: `admin`
- Mật khẩu: `itk23maidinh`

## 7. Ảnh chụp màn hình:
![image](https://github.com/user-attachments/assets/0257c678-0984-4c79-b1e3-ffb79d3d2e96)
![image](https://github.com/user-attachments/assets/5e4dc15b-ad45-4ffd-974c-305ff1025246)
![image](https://github.com/user-attachments/assets/06a97331-3a1f-462b-9b78-702bc0dc94a0)

