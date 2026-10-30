<<<<<<< HEAD
# Nen Tang Game Doc Lap Vi

## Run with XAMPP

1. Place the project folder in `C:\xampp\htdocs\BTT`.
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Open phpMyAdmin, select **Import**, and import `test.sql`. The script creates and selects `nen_tang_game_doc_lap_vi_v2`.
4. Confirm the database settings in `config/config.php`. They match the default XAMPP MySQL account (`root`, no password).
5. Open `http://localhost/BTT/` and register an account.

The project uses Apache rewrite rules to route page URLs through the front controller. If URLs return 404, enable `mod_rewrite` and allow `.htaccess` overrides (`AllowOverride All`) for `htdocs`.

The schema stores game metadata and media URLs; cover images uploaded from the game form are stored under `public/uploads/game-covers` (JPG, PNG, WebP or GIF, up to 5 MB), while downloadable game files are not stored. Cart checkout is a demo flow only: it records a simulated paid order and grants the selected games to the account library without charging money or contacting a payment provider.

## Admin access

After registering the account that should manage the platform, run this once in phpMyAdmin (replace `YOUR_USERNAME` with that account's login name) to grant it the initial admin role:

```sql
INSERT IGNORE INTO nguoi_dung_vai_tro (nguoi_dung_id, vai_tro_id)
SELECT u.ma, r.ma
FROM nguoi_dung u
JOIN vai_tro r ON r.ma_vai_tro = 'admin'
WHERE u.ten_dang_nhap = 'YOUR_USERNAME';
```

Admins can remove games from public access in **Quản trị → Trò chơi**; the game is hidden rather than physically deleted so related order and ownership history remains intact. In **Quản trị → Người dùng**, admins can assign roles. The current admin cannot change their own roles, and the final admin role cannot be removed.
=======
# Digital Game Distribution Platform

An open-source digital game distribution platform built with PHP and MySQL, inspired by Steam, itch.io.

This project was developed as part of the "Open Source Software Development" course.

# Contributors

- Nguyễn Thành Đạt
- Nguyễn Thị Ngọc Thảo
- Nguyễn Võ Hoàng Anh Thư
- Hồ Xuân Phúc
>>>>>>> origin/main
