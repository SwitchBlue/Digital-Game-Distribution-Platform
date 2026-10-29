<section class="card" style="max-width:480px;margin:auto">
    <h2>Đăng nhập</h2>
    <form method="post" action="<?php echo app_url('/auth/login'); ?>">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" required autocomplete="email" style="width:100%">
        <label for="password">Mật khẩu</label>
        <input id="password" type="password" name="password" required autocomplete="current-password" style="width:100%">
        <p style="margin-top:16px"><button class="button" type="submit">Đăng nhập</button></p>
    </form>
    <p>Chưa có tài khoản? <a href="<?php echo app_url('/auth/register'); ?>">Đăng ký</a></p>
</section>
