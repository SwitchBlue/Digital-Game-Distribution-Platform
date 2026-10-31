<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section><h2>Người dùng</h2>
    <table style="width:100%;border-collapse:collapse"><thead><tr><th>Tên hiển thị</th><th>Tên đăng nhập</th><th>Email</th><th>Trạng thái</th><th>Ngày tạo</th></tr></thead><tbody>
    <?php foreach ($users as $account): ?><tr>
        <td><?php echo $escape($account['ten_hien_thi']); ?></td><td><?php echo $escape($account['ten_dang_nhap']); ?></td>
        <td><?php echo $escape($account['email']); ?></td><td><?php echo $escape($account['trang_thai']); ?></td><td><?php echo $escape($account['ngay_tao']); ?></td>
    </tr><?php endforeach; ?>
    </tbody></table>
</section>
