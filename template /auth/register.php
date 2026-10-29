<section class="card" style="max-width:520px;margin:auto">
    <h2>Tạo tài khoản</h2>
    <form method="post" action="<?php echo app_url('/auth/register'); ?>">
        <label for="username">Tên đăng nhập</label>
        <input id="username" name="username" maxlength="50" required autocomplete="username" style="width:100%">
        <label for="display-name">Tên hiển thị</label>
        <input id="display-name" name="display_name" maxlength="100" required style="width:100%">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" maxlength="254" required autocomplete="email" style="width:100%">
        <label for="password">Mật khẩu</label>
        <input id="password" type="password" name="password" minlength="8" required autocomplete="new-password" style="width:100%">
        <label for="confirm-password">Nhập lại mật khẩu</label>
        <input id="confirm-password" type="password" name="confirm_password" minlength="8" required autocomplete="new-password" style="width:100%">
        <p style="margin-top:16px"><button class="button" type="submit">Đăng ký</button></p>
    </form>
</section>
