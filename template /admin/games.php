<?php $escape = static function ($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }; ?>
<section><h2>Quản lý trò chơi</h2>
    <table style="width:100%;border-collapse:collapse"><thead><tr><th>Tiêu đề</th><th>Tác giả ID</th><th>Trạng thái</th><th>Ngày tạo</th></tr></thead><tbody>
    <?php foreach ($games as $game): ?><tr>
        <td><a href="<?php echo app_url('/games/' . rawurlencode($game['duong_dan_rut_gon'])); ?>"><?php echo $escape($game['tieu_de']); ?></a></td>
        <td><?php echo (int)$game['nguoi_tao_id']; ?></td><td><?php echo $escape($game['trang_thai']); ?></td><td><?php echo $escape($game['ngay_tao']); ?></td>
    </tr><?php endforeach; ?>
    </tbody></table>
</section>
