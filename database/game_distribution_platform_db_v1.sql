


CREATE DATABASE IF NOT EXISTS game_distribution_platform_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE game_distribution_platform_db;

-- ============================================================
-- 1. TAI KHOAN, VAI TRO, HO SO, THEO DOI, THONG BAO
-- ============================================================
CREATE TABLE nguoi_dung (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ten_dang_nhap VARCHAR(50) NOT NULL,
    email VARCHAR(254) NOT NULL,
    mat_khau_da_bam VARCHAR(255) NOT NULL,
    ten_hien_thi VARCHAR(100) NOT NULL,
    gioi_thieu TEXT NULL,
    url_anh_dai_dien VARCHAR(1000) NULL,
    url_anh_bia VARCHAR(1000) NULL,
    url_website VARCHAR(500) NULL,
    ma_quoc_gia CHAR(2) NULL,
    ngay_xac_minh_email DATETIME NULL,
    trang_thai ENUM('active','suspended','deleted') NOT NULL DEFAULT 'active',
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_username (ten_dang_nhap),
    UNIQUE KEY uq_users_email (email),
    INDEX idx_users_status (trang_thai)
) ENGINE=InnoDB;

CREATE TABLE vai_tro (
    ma SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ma_vai_tro VARCHAR(40) NOT NULL UNIQUE,
    ten_vai_tro VARCHAR(80) NOT NULL
) ENGINE=InnoDB;

INSERT INTO vai_tro (ma_vai_tro, ten_vai_tro) VALUES
('player','Người chơi'),
('developer','Nhà phát triển'),
('moderator','Điều hành viên'),
('admin','Quản trị viên');

CREATE TABLE nguoi_dung_vai_tro (
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    vai_tro_id SMALLINT UNSIGNED NOT NULL,
    ngay_phan_vai TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (nguoi_dung_id, vai_tro_id),
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE,
    FOREIGN KEY (vai_tro_id) REFERENCES vai_tro(ma) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE cai_dat_nguoi_dung (
    nguoi_dung_id BIGINT UNSIGNED PRIMARY KEY,
    pham_vi_hien_thi_ho_so ENUM('public','private') NOT NULL DEFAULT 'public',
    hien_thi_game_so_huu BOOLEAN NOT NULL DEFAULT TRUE,
    cho_phep_nhan_tin BOOLEAN NOT NULL DEFAULT TRUE,
    email_cap_nhat_san_pham BOOLEAN NOT NULL DEFAULT TRUE,
    email_cap_nhat_cong_dong BOOLEAN NOT NULL DEFAULT TRUE,
    ngon_ngu_uu_tien VARCHAR(10) NOT NULL DEFAULT 'vi',
    tien_te_uu_tien CHAR(3) NOT NULL DEFAULT 'VND',
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE theo_doi_nguoi_dung (
    nguoi_theo_doi_id BIGINT UNSIGNED NOT NULL,
    nguoi_duoc_theo_doi_id BIGINT UNSIGNED NOT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (nguoi_theo_doi_id, nguoi_duoc_theo_doi_id),
    FOREIGN KEY (nguoi_theo_doi_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_duoc_theo_doi_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE,
    CHECK (nguoi_theo_doi_id <> nguoi_duoc_theo_doi_id)
) ENGINE=InnoDB;

CREATE TABLE thong_bao (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    loai_thong_bao VARCHAR(60) NOT NULL,
    tieu_de VARCHAR(200) NOT NULL,
    noi_dung_thong_bao TEXT NULL,
    url_dich VARCHAR(1000) NULL,
    da_doc BOOLEAN NOT NULL DEFAULT FALSE,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_doc DATETIME NULL,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE,
    INDEX idx_notifications_user_read (nguoi_dung_id, da_doc, ngay_tao)
) ENGINE=InnoDB;

-- ============================================================


-- ============================================================
-- 2. TRANG TRO CHOI / DU AN, TAC GIA VA THONG TIN (KHONG LUU TEP GAME)
-- ============================================================
CREATE TABLE tro_choi (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_tao_id BIGINT UNSIGNED NOT NULL,
    tieu_de VARCHAR(200) NOT NULL,
    duong_dan_rut_gon VARCHAR(220) NOT NULL,
    mo_ta_ngan VARCHAR(500) NULL,
    mo_ta MEDIUMTEXT NULL,
    loai_du_an ENUM('game','asset','tool','soundtrack','comic','book','physical','other') NOT NULL DEFAULT 'game',
    trang_thai ENUM('draft','published','unlisted','hidden','under_review','suspended') NOT NULL DEFAULT 'draft',
    trang_thai_phat_hanh ENUM('prototype','in_development','early_access','released','cancelled') NOT NULL DEFAULT 'in_development',
    mo_hinh_gia ENUM('free','paid','pay_what_you_want','donation') NOT NULL DEFAULT 'free',
    gia_toi_thieu DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    gia_de_xuat DECIMAL(12,2) NULL,
    ma_tien_te CHAR(3) NOT NULL DEFAULT 'USD',
    phan_loai_do_tuoi VARCHAR(40) NULL,
    ten_giay_phep VARCHAR(120) NULL,
    url_choi_ngoai VARCHAR(1000) NULL,
    cho_phep_binh_luan BOOLEAN NOT NULL DEFAULT TRUE,
    cho_phep_danh_gia BOOLEAN NOT NULL DEFAULT TRUE,
    ngay_cong_bo DATETIME NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_games_slug (duong_dan_rut_gon),
    INDEX idx_games_creator_status (nguoi_tao_id, trang_thai),
    INDEX idx_games_status_published (trang_thai, ngay_cong_bo),
    INDEX idx_games_title (tieu_de),
    FOREIGN KEY (nguoi_tao_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    CHECK (gia_toi_thieu >= 0),
    CHECK (gia_de_xuat IS NULL OR gia_de_xuat >= 0)
) ENGINE=InnoDB;

-- Mot game co the co mot hoac nhieu tac gia.
-- phan_tram_huong_doanh_thu la ty le chia doanh thu cua tac gia cho game do.
-- Tong ty le cua tat ca tac gia trong mot game phai bang 100%; PHP can kiem tra quy tac tong nay.
-- Neu game chi co mot tac gia, ty le cua nguoi do la 100.00.
CREATE TABLE tac_gia_tro_choi (
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    phan_tram_huong_doanh_thu DECIMAL(5,2) NOT NULL,
    thu_tu_hien_thi SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ngay_them TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (tro_choi_id, nguoi_dung_id),
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    CHECK (phan_tram_huong_doanh_thu >= 0 AND phan_tram_huong_doanh_thu <= 100),
    INDEX idx_game_authors_user (nguoi_dung_id, tro_choi_id),
    INDEX idx_game_authors_order (tro_choi_id, thu_tu_hien_thi)
) ENGINE=InnoDB;

CREATE TABLE cai_dat_trang_tro_choi (
    tro_choi_id BIGINT UNSIGNED PRIMARY KEY,
    mau_nen VARCHAR(20) NULL,
    mau_chu VARCHAR(20) NULL,
    mau_lien_ket VARCHAR(20) NULL,
    bo_cuc_trang ENUM('default','wide','minimal') NOT NULL DEFAULT 'default',
    bat_cong_dong BOOLEAN NOT NULL DEFAULT TRUE,
    hien_thi_nha_sang_tao BOOLEAN NOT NULL DEFAULT TRUE,
    css_tuy_chinh MEDIUMTEXT NULL,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Luu URL anh bia, anh chup man hinh, trailer va anh thu nho; khong luu tep game.
CREATE TABLE media_tro_choi (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    loai_media ENUM('cover','thumbnail','screenshot','trailer','external_link','other') NOT NULL,
    url_media VARCHAR(1500) NOT NULL,
    chu_thich VARCHAR(300) NULL,
    van_ban_thay_the VARCHAR(300) NULL,
    thu_tu_sap_xep INT NOT NULL DEFAULT 0,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    INDEX idx_game_media_order (tro_choi_id, loai_media, thu_tu_sap_xep)
) ENGINE=InnoDB;

CREATE TABLE nen_tang (
    ma SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ma_nen_tang VARCHAR(40) NOT NULL UNIQUE,
    ten_nen_tang VARCHAR(80) NOT NULL
) ENGINE=InnoDB;

INSERT INTO nen_tang (ma_nen_tang, ten_nen_tang) VALUES
('windows','Windows'),('linux','Linux'),('macos','macOS'),
('android','Android'),('ios','iOS'),('browser','Trình duyệt web'),
('web3d','WebGL / WebGPU'),('other','Khác');

CREATE TABLE tro_choi_nen_tang (
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    nen_tang_id SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (tro_choi_id, nen_tang_id),
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    FOREIGN KEY (nen_tang_id) REFERENCES nen_tang(ma) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE the_loai (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ten VARCHAR(100) NOT NULL,
    duong_dan_rut_gon VARCHAR(120) NOT NULL,
    mo_ta VARCHAR(500) NULL,
    UNIQUE KEY uq_categories_name (ten),
    UNIQUE KEY uq_categories_slug (duong_dan_rut_gon)
) ENGINE=InnoDB;

INSERT INTO the_loai (ten, duong_dan_rut_gon, mo_ta) VALUES
('Hành động','action','Game hành động'),
('Phiêu lưu','adventure','Game phiêu lưu'),
('Nhập vai','role-playing','Game nhập vai'),
('Kinh dị','horror','Game kinh dị'),
('Giải đố','puzzle','Game giải đố'),
('Chiến thuật','strategy','Game chiến thuật'),
('Mô phỏng','simulation','Game mô phỏng'),
('Đi cảnh','platformer','Game đi cảnh'),
('Tiểu thuyết tương tác','visual-novel','Tiểu thuyết tương tác'),
('Khác','other','Thể loại khác');

CREATE TABLE tro_choi_the_loai (
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    the_loai_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (tro_choi_id, the_loai_id),
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    FOREIGN KEY (the_loai_id) REFERENCES the_loai(ma) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE nhan (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ten VARCHAR(80) NOT NULL,
    duong_dan_rut_gon VARCHAR(100) NOT NULL,
    UNIQUE KEY uq_tags_name (ten),
    UNIQUE KEY uq_tags_slug (duong_dan_rut_gon)
) ENGINE=InnoDB;

CREATE TABLE tro_choi_nhan (
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    nhan_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (tro_choi_id, nhan_id),
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    FOREIGN KEY (nhan_id) REFERENCES nhan(ma) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Chi luu ghi chu/lich su phien ban; khong luu cac tep co the tai xuong.
CREATE TABLE phien_ban_phat_hanh (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    ten_phien_ban VARCHAR(60) NOT NULL,
    kenh_phat_hanh ENUM('stable','beta','alpha','preview') NOT NULL DEFAULT 'stable',
    ghi_chu_phat_hanh MEDIUMTEXT NULL,
    cong_khai BOOLEAN NOT NULL DEFAULT TRUE,
    ngay_phat_hanh DATETIME NULL,
    nguoi_tao_id BIGINT UNSIGNED NOT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_game_release_version (tro_choi_id, ten_phien_ban, kenh_phat_hanh),
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_tao_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    INDEX idx_releases_game_date (tro_choi_id, ngay_phat_hanh)
) ENGINE=InnoDB;


CREATE TABLE tro_choi_yeu_thich (
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (nguoi_dung_id, tro_choi_id),
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE,
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE theo_doi_tro_choi (
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    thong_bao_cap_nhat BOOLEAN NOT NULL DEFAULT TRUE,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (nguoi_dung_id, tro_choi_id),
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE,
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE binh_luan_tro_choi (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    binh_luan_cha_id BIGINT UNSIGNED NULL,
    noi_dung MEDIUMTEXT NOT NULL,
    trang_thai ENUM('published','hidden','removed') NOT NULL DEFAULT 'published',
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE,
    FOREIGN KEY (binh_luan_cha_id) REFERENCES binh_luan_tro_choi(ma) ON DELETE CASCADE,
    INDEX idx_game_comments_game_date (tro_choi_id, ngay_tao),
    INDEX idx_game_comments_parent (binh_luan_cha_id)
) ENGINE=InnoDB;

CREATE TABLE danh_gia (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    diem_danh_gia TINYINT UNSIGNED NOT NULL,
    tieu_de VARCHAR(200) NULL,
    noi_dung_danh_gia MEDIUMTEXT NULL,
    da_xac_minh_so_huu BOOLEAN NOT NULL DEFAULT FALSE,
    trang_thai ENUM('published','hidden','removed') NOT NULL DEFAULT 'published',
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_reviews_user_game (nguoi_dung_id, tro_choi_id),
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE,
    CHECK (diem_danh_gia BETWEEN 1 AND 5),
    INDEX idx_reviews_game_date (tro_choi_id, ngay_tao)
) ENGINE=InnoDB;

CREATE TABLE binh_chon_danh_gia (
    danh_gia_id BIGINT UNSIGNED NOT NULL,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    co_ich BOOLEAN NOT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (danh_gia_id, nguoi_dung_id),
    FOREIGN KEY (danh_gia_id) REFERENCES danh_gia(ma) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE nhat_ky_phat_trien (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    tac_gia_nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    tieu_de VARCHAR(200) NOT NULL,
    duong_dan_rut_gon VARCHAR(220) NOT NULL,
    noi_dung MEDIUMTEXT NOT NULL,
    trang_thai ENUM('draft','published','hidden') NOT NULL DEFAULT 'draft',
    ngay_cong_bo DATETIME NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_devlog_game_slug (tro_choi_id, duong_dan_rut_gon),
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    FOREIGN KEY (tac_gia_nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    INDEX idx_devlogs_game_published (tro_choi_id, trang_thai, ngay_cong_bo)
) ENGINE=InnoDB;

CREATE TABLE binh_luan_nhat_ky (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nhat_ky_phat_trien_id BIGINT UNSIGNED NOT NULL,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    binh_luan_cha_id BIGINT UNSIGNED NULL,
    noi_dung TEXT NOT NULL,
    trang_thai ENUM('published','hidden','removed') NOT NULL DEFAULT 'published',
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nhat_ky_phat_trien_id) REFERENCES nhat_ky_phat_trien(ma) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE,
    FOREIGN KEY (binh_luan_cha_id) REFERENCES binh_luan_nhat_ky(ma) ON DELETE CASCADE,
    INDEX idx_devlog_comments_post (nhat_ky_phat_trien_id, ngay_tao)
) ENGINE=InnoDB;

-- ============================================================
-- 3. BO SUU TAP / CUOC THI LAM GAME / THAO LUAN CONG DONG
-- ============================================================
CREATE TABLE bo_suu_tap (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_so_huu_id BIGINT UNSIGNED NOT NULL,
    tieu_de VARCHAR(180) NOT NULL,
    duong_dan_rut_gon VARCHAR(200) NOT NULL,
    mo_ta MEDIUMTEXT NULL,
    pham_vi_hien_thi ENUM('public','unlisted','private') NOT NULL DEFAULT 'public',
    bo_cuc ENUM('grid','list') NOT NULL DEFAULT 'grid',
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_collection_owner_slug (nguoi_dung_so_huu_id, duong_dan_rut_gon),
    FOREIGN KEY (nguoi_dung_so_huu_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE tro_choi_bo_suu_tap (
    bo_suu_tap_id BIGINT UNSIGNED NOT NULL,
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    nguoi_them_id BIGINT UNSIGNED NOT NULL,
    vi_tri INT UNSIGNED NOT NULL DEFAULT 0,
    ghi_chu VARCHAR(500) NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (bo_suu_tap_id, tro_choi_id),
    FOREIGN KEY (bo_suu_tap_id) REFERENCES bo_suu_tap(ma) ON DELETE CASCADE,
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_them_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE,
    INDEX idx_collection_games_position (bo_suu_tap_id, vi_tri)
) ENGINE=InnoDB;

CREATE TABLE cuoc_thi_lam_game (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_tao_id BIGINT UNSIGNED NOT NULL,
    tieu_de VARCHAR(200) NOT NULL,
    duong_dan_rut_gon VARCHAR(220) NOT NULL UNIQUE,
    mo_ta MEDIUMTEXT NULL,
    the_le MEDIUMTEXT NULL,
    ngay_bat_dau DATETIME NOT NULL,
    han_nop_bai DATETIME NOT NULL,
    ngay_bat_dau_binh_chon DATETIME NULL,
    ngay_ket_thuc_binh_chon DATETIME NULL,
    trang_thai ENUM('draft','published','active','judging','completed','cancelled') NOT NULL DEFAULT 'draft',
    bat_binh_chon BOOLEAN NOT NULL DEFAULT FALSE,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_tao_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    INDEX idx_jams_status_dates (trang_thai, ngay_bat_dau, han_nop_bai)
) ENGINE=InnoDB;

CREATE TABLE thanh_vien_cuoc_thi (
    cuoc_thi_id BIGINT UNSIGNED NOT NULL,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    vai_tro_thanh_vien ENUM('host','admin','judge','moderator') NOT NULL DEFAULT 'moderator',
    ngay_tham_gia TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (cuoc_thi_id, nguoi_dung_id),
    FOREIGN KEY (cuoc_thi_id) REFERENCES cuoc_thi_lam_game(ma) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bai_du_thi (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cuoc_thi_id BIGINT UNSIGNED NOT NULL,
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    nguoi_nop_id BIGINT UNSIGNED NOT NULL,
    trang_thai ENUM('submitted','withdrawn','disqualified') NOT NULL DEFAULT 'submitted',
    ngay_nop TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_jam_game (cuoc_thi_id, tro_choi_id),
    FOREIGN KEY (cuoc_thi_id) REFERENCES cuoc_thi_lam_game(ma) ON DELETE CASCADE,
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_nop_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE binh_chon_bai_du_thi (
    bai_du_thi_id BIGINT UNSIGNED NOT NULL,
    nguoi_binh_chon_id BIGINT UNSIGNED NOT NULL,
    tieu_chi VARCHAR(80) NOT NULL DEFAULT 'overall',
    diem_so TINYINT UNSIGNED NOT NULL,
    binh_luan VARCHAR(1000) NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (bai_du_thi_id, nguoi_binh_chon_id, tieu_chi),
    FOREIGN KEY (bai_du_thi_id) REFERENCES bai_du_thi(ma) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_binh_chon_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE,
    CHECK (diem_so BETWEEN 1 AND 5)
) ENGINE=InnoDB;

CREATE TABLE dien_dan (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    loai_dien_dan ENUM('platform','game','jam') NOT NULL,
    tro_choi_id BIGINT UNSIGNED NULL,
    cuoc_thi_id BIGINT UNSIGNED NULL,
    tieu_de VARCHAR(180) NOT NULL,
    mo_ta VARCHAR(1000) NULL,
    cong_khai BOOLEAN NOT NULL DEFAULT TRUE,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_board_game (tro_choi_id),
    UNIQUE KEY uq_board_jam (cuoc_thi_id),
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    FOREIGN KEY (cuoc_thi_id) REFERENCES cuoc_thi_lam_game(ma) ON DELETE CASCADE,
    CHECK (
      (loai_dien_dan = 'platform' AND tro_choi_id IS NULL AND cuoc_thi_id IS NULL) OR
      (loai_dien_dan = 'game' AND tro_choi_id IS NOT NULL AND cuoc_thi_id IS NULL) OR
      (loai_dien_dan = 'jam' AND tro_choi_id IS NULL AND cuoc_thi_id IS NOT NULL)
    )
) ENGINE=InnoDB;

CREATE TABLE chu_de_thao_luan (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dien_dan_id BIGINT UNSIGNED NOT NULL,
    tac_gia_nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    tieu_de VARCHAR(220) NOT NULL,
    noi_dung_mo_dau MEDIUMTEXT NOT NULL,
    trang_thai ENUM('published','hidden','removed') NOT NULL DEFAULT 'published',
    da_ghim BOOLEAN NOT NULL DEFAULT FALSE,
    da_khoa BOOLEAN NOT NULL DEFAULT FALSE,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (dien_dan_id) REFERENCES dien_dan(ma) ON DELETE CASCADE,
    FOREIGN KEY (tac_gia_nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE,
    INDEX idx_community_topics_board (dien_dan_id, da_ghim, ngay_tao)
) ENGINE=InnoDB;

CREATE TABLE bai_viet_thao_luan (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chu_de_id BIGINT UNSIGNED NOT NULL,
    tac_gia_nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    bai_viet_cha_id BIGINT UNSIGNED NULL,
    noi_dung MEDIUMTEXT NOT NULL,
    trang_thai ENUM('published','hidden','removed') NOT NULL DEFAULT 'published',
    ngay_chinh_sua DATETIME NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (chu_de_id) REFERENCES chu_de_thao_luan(ma) ON DELETE CASCADE,
    FOREIGN KEY (tac_gia_nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE,
    FOREIGN KEY (bai_viet_cha_id) REFERENCES bai_viet_thao_luan(ma) ON DELETE CASCADE,
    INDEX idx_community_posts_topic_date (chu_de_id, ngay_tao),
    INDEX idx_community_posts_parent (bai_viet_cha_id)
) ENGINE=InnoDB;

CREATE TABLE tuong_tac_bai_viet (
    bai_viet_id BIGINT UNSIGNED NOT NULL,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    phan_ung ENUM('like','helpful','love','laugh') NOT NULL DEFAULT 'like',
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (bai_viet_id, nguoi_dung_id, phan_ung),
    FOREIGN KEY (bai_viet_id) REFERENCES bai_viet_thao_luan(ma) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 4. GOI GAME, GIAM GIA, GIO HANG, DON HANG, THANH TOAN, QUYEN SO HUU
-- ============================================================
CREATE TABLE goi_game (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_tao_id BIGINT UNSIGNED NOT NULL,
    tieu_de VARCHAR(200) NOT NULL,
    duong_dan_rut_gon VARCHAR(220) NOT NULL UNIQUE,
    mo_ta MEDIUMTEXT NULL,
    loai_goi_game ENUM('creator_created','platform_curated') NOT NULL DEFAULT 'creator_created',
    gia DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    ma_tien_te CHAR(3) NOT NULL DEFAULT 'USD',
    trang_thai ENUM('draft','published','hidden','ended') NOT NULL DEFAULT 'draft',
    ngay_bat_dau DATETIME NULL,
    ngay_ket_thuc DATETIME NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_tao_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    CHECK (gia >= 0),
    INDEX idx_bundles_status_dates (trang_thai, ngay_bat_dau, ngay_ket_thuc)
) ENGINE=InnoDB;

CREATE TABLE tro_choi_goi_game (
    goi_game_id BIGINT UNSIGNED NOT NULL,
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    vi_tri INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (goi_game_id, tro_choi_id),
    FOREIGN KEY (goi_game_id) REFERENCES goi_game(ma) ON DELETE CASCADE,
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE RESTRICT,
    INDEX idx_bundle_games_game (tro_choi_id)
) ENGINE=InnoDB;


CREATE TABLE chien_dich_giam_gia (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_tao_id BIGINT UNSIGNED NOT NULL,
    tieu_de VARCHAR(200) NOT NULL,
    mo_ta MEDIUMTEXT NULL,
    phan_tram_giam_gia DECIMAL(5,2) NOT NULL,
    trang_thai ENUM('draft','scheduled','active','ended','cancelled') NOT NULL DEFAULT 'draft',
    ngay_bat_dau DATETIME NOT NULL,
    ngay_ket_thuc DATETIME NOT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_tao_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    CHECK (phan_tram_giam_gia >= 0 AND phan_tram_giam_gia <= 100),
    CHECK (ngay_ket_thuc > ngay_bat_dau),
    INDEX idx_discount_campaign_dates (trang_thai, ngay_bat_dau, ngay_ket_thuc)
) ENGINE=InnoDB;

CREATE TABLE tro_choi_giam_gia (
    chien_dich_id BIGINT UNSIGNED NOT NULL,
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (chien_dich_id, tro_choi_id),
    FOREIGN KEY (chien_dich_id) REFERENCES chien_dich_giam_gia(ma) ON DELETE CASCADE,
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE gio_hang (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    trang_thai ENUM('active','checked_out','abandoned') NOT NULL DEFAULT 'active',
    ma_tien_te CHAR(3) NOT NULL DEFAULT 'USD',
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_carts_user (nguoi_dung_id),
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE muc_gio_hang (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gio_hang_id BIGINT UNSIGNED NOT NULL,
    loai_muc ENUM('game','bundle') NOT NULL,
    tro_choi_id BIGINT UNSIGNED NULL,
    goi_game_id BIGINT UNSIGNED NULL,
    ngay_them TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gio_hang_id) REFERENCES gio_hang(ma) ON DELETE CASCADE,
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    FOREIGN KEY (goi_game_id) REFERENCES goi_game(ma) ON DELETE CASCADE,
    UNIQUE KEY uq_cart_game (gio_hang_id, tro_choi_id),
    UNIQUE KEY uq_cart_bundle (gio_hang_id, goi_game_id),
    CHECK (
      (loai_muc = 'game' AND tro_choi_id IS NOT NULL AND goi_game_id IS NULL) OR
      (loai_muc = 'bundle' AND tro_choi_id IS NULL AND goi_game_id IS NOT NULL)
    )
) ENGINE=InnoDB;

CREATE TABLE don_hang (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ma_don_hang VARCHAR(40) NOT NULL UNIQUE,
    nguoi_mua_id BIGINT UNSIGNED NOT NULL,
    trang_thai ENUM('pending','paid','partially_refunded','refunded','cancelled','failed') NOT NULL DEFAULT 'pending',
    ma_tien_te CHAR(3) NOT NULL,
    tong_tien_hang DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tong_tien_giam DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tong_tien_thue DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tong_thanh_toan DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    quoc_gia_thanh_toan CHAR(2) NULL,
    ngay_dat_hang TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_thanh_toan DATETIME NULL,
    FOREIGN KEY (nguoi_mua_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    CHECK (tong_tien_hang >= 0 AND tong_tien_giam >= 0 AND tong_tien_thue >= 0 AND tong_thanh_toan >= 0),
    INDEX idx_orders_buyer_date (nguoi_mua_id, ngay_dat_hang),
    INDEX idx_orders_status_date (trang_thai, ngay_dat_hang)
) ENGINE=InnoDB;

CREATE TABLE chi_tiet_don_hang (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    don_hang_id BIGINT UNSIGNED NOT NULL,
    loai_muc ENUM('game','bundle') NOT NULL,
    tro_choi_id BIGINT UNSIGNED NULL,
    goi_game_id BIGINT UNSIGNED NULL,
    nguoi_nhan_qua_id BIGINT UNSIGNED NULL,
    trang_thai_qua ENUM('not_gift','pending','claimed','cancelled') NOT NULL DEFAULT 'not_gift',
    tieu_de_luc_mua VARCHAR(220) NOT NULL,
    so_luong SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    don_gia DECIMAL(12,2) NOT NULL,
    so_tien_giam DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    tien_thue DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    thanh_tien_dong DECIMAL(12,2) NOT NULL,
    ma_tien_te CHAR(3) NOT NULL,
    FOREIGN KEY (don_hang_id) REFERENCES don_hang(ma) ON DELETE RESTRICT,
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE RESTRICT,
    FOREIGN KEY (goi_game_id) REFERENCES goi_game(ma) ON DELETE RESTRICT,
    FOREIGN KEY (nguoi_nhan_qua_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    CHECK (
      (loai_muc = 'game' AND tro_choi_id IS NOT NULL AND goi_game_id IS NULL) OR
      (loai_muc = 'bundle' AND tro_choi_id IS NULL AND goi_game_id IS NOT NULL)
    ),
    CHECK (so_luong > 0 AND don_gia >= 0 AND so_tien_giam >= 0 AND tien_thue >= 0 AND thanh_tien_dong >= 0),
    INDEX idx_order_items_order (don_hang_id),
    INDEX idx_order_items_game (tro_choi_id),
    INDEX idx_order_items_bundle (goi_game_id)
) ENGINE=InnoDB;

CREATE TABLE thanh_toan (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    don_hang_id BIGINT UNSIGNED NOT NULL,
    nha_cung_cap VARCHAR(60) NOT NULL,
    ma_giao_dich_nha_cung_cap VARCHAR(190) NULL,
    so_tien DECIMAL(12,2) NOT NULL,
    ma_tien_te CHAR(3) NOT NULL,
    trang_thai ENUM('initiated','pending','succeeded','failed','cancelled') NOT NULL DEFAULT 'initiated',
    ma_phan_hoi_nha_cung_cap VARCHAR(100) NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_thanh_toan DATETIME NULL,
    FOREIGN KEY (don_hang_id) REFERENCES don_hang(ma) ON DELETE RESTRICT,
    UNIQUE KEY uq_payment_provider_tx (nha_cung_cap, ma_giao_dich_nha_cung_cap),
    CHECK (so_tien >= 0),
    INDEX idx_payments_order_status (don_hang_id, trang_thai)
) ENGINE=InnoDB;

CREATE TABLE hoan_tien (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    thanh_toan_id BIGINT UNSIGNED NOT NULL,
    chi_tiet_don_hang_id BIGINT UNSIGNED NULL,
    nguoi_yeu_cau_id BIGINT UNSIGNED NULL,
    so_tien DECIMAL(12,2) NOT NULL,
    ma_tien_te CHAR(3) NOT NULL,
    ly_do TEXT NULL,
    trang_thai ENUM('requested','approved','rejected','processing','completed','failed') NOT NULL DEFAULT 'requested',
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_xu_ly DATETIME NULL,
    FOREIGN KEY (thanh_toan_id) REFERENCES thanh_toan(ma) ON DELETE RESTRICT,
    FOREIGN KEY (chi_tiet_don_hang_id) REFERENCES chi_tiet_don_hang(ma) ON DELETE RESTRICT,
    FOREIGN KEY (nguoi_yeu_cau_id) REFERENCES nguoi_dung(ma) ON DELETE SET NULL,
    CHECK (so_tien > 0),
    INDEX idx_refunds_status_date (trang_thai, ngay_tao)
) ENGINE=InnoDB;

-- Luu quyen so huu game; khong luu tep thuc thi hay tep game.
CREATE TABLE quyen_so_huu_tro_choi (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    chi_tiet_don_hang_id BIGINT UNSIGNED NULL,
    loai_nhan_quyen ENUM('purchase','bundle','gift','free_claim','access_key','admin_grant') NOT NULL,
    trang_thai ENUM('active','revoked','refunded') NOT NULL DEFAULT 'active',
    ngay_nhan_quyen TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_thu_hoi DATETIME NULL,
    UNIQUE KEY uq_ownership_user_game (nguoi_dung_id, tro_choi_id),
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE RESTRICT,
    FOREIGN KEY (chi_tiet_don_hang_id) REFERENCES chi_tiet_don_hang(ma) ON DELETE SET NULL,
    INDEX idx_ownership_game (tro_choi_id),
    INDEX idx_ownership_order_item (chi_tiet_don_hang_id)
) ENGINE=InnoDB;

CREATE TABLE ma_giam_gia (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_tao_id BIGINT UNSIGNED NULL,
    ma_uu_dai VARCHAR(80) NOT NULL UNIQUE,
    loai_giam_gia ENUM('percent','fixed_amount') NOT NULL,
    gia_tri_giam_gia DECIMAL(12,2) NOT NULL,
    ma_tien_te CHAR(3) NULL,
    so_luot_dung_toi_da INT UNSIGNED NULL,
    so_luot_da_dung INT UNSIGNED NOT NULL DEFAULT 0,
    ngay_bat_dau DATETIME NULL,
    ngay_ket_thuc DATETIME NULL,
    dang_hoat_dong BOOLEAN NOT NULL DEFAULT TRUE,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_tao_id) REFERENCES nguoi_dung(ma) ON DELETE SET NULL,
    INDEX idx_coupon_creator (nguoi_tao_id),
    CHECK (gia_tri_giam_gia > 0),
    CHECK (loai_giam_gia <> 'percent' OR gia_tri_giam_gia <= 100),
    CHECK (ngay_ket_thuc IS NULL OR ngay_bat_dau IS NULL OR ngay_ket_thuc > ngay_bat_dau)
) ENGINE=InnoDB;

CREATE TABLE luot_dung_ma_giam_gia (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ma_giam_gia_id BIGINT UNSIGNED NOT NULL,
    don_hang_id BIGINT UNSIGNED NOT NULL,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    so_tien_giam DECIMAL(12,2) NOT NULL,
    ngay_su_dung TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_coupon_order (ma_giam_gia_id, don_hang_id),
    FOREIGN KEY (ma_giam_gia_id) REFERENCES ma_giam_gia(ma) ON DELETE RESTRICT,
    FOREIGN KEY (don_hang_id) REFERENCES don_hang(ma) ON DELETE RESTRICT,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    CHECK (so_tien_giam >= 0)
) ENGINE=InnoDB;

-- ============================================================
-- 5. DOANH THU TAC GIA, CHI TRA, MA TRUY CAP, THONG KE
-- ============================================================
CREATE TABLE so_ke_toan_tac_gia (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chi_tiet_don_hang_id BIGINT UNSIGNED NOT NULL,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    phan_tram_huong_doanh_thu DECIMAL(5,2) NOT NULL,
    doanh_thu_truoc_phi DECIMAL(12,2) NOT NULL,
    phi_nen_tang DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    thue_da_khau_tru DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    so_tien_thuc_nhan DECIMAL(12,2) NOT NULL,
    ma_tien_te CHAR(3) NOT NULL,
    trang_thai ENUM('pending','available','held','paid','reversed') NOT NULL DEFAULT 'pending',
    ngay_co_the_chi_tra DATETIME NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chi_tiet_don_hang_id) REFERENCES chi_tiet_don_hang(ma) ON DELETE RESTRICT,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    UNIQUE KEY uq_ledger_order_author (chi_tiet_don_hang_id, nguoi_dung_id),
    CHECK (phan_tram_huong_doanh_thu >= 0 AND phan_tram_huong_doanh_thu <= 100),
    CHECK (doanh_thu_truoc_phi >= 0 AND phi_nen_tang >= 0 AND thue_da_khau_tru >= 0 AND so_tien_thuc_nhan >= 0),
    INDEX idx_creator_ledger_user_status (nguoi_dung_id, trang_thai, ngay_tao)
) ENGINE=InnoDB;

CREATE TABLE tai_khoan_nhan_tien_nguoi_dung (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    nha_cung_cap VARCHAR(80) NOT NULL,
    ma_tai_khoan_ben_ngoai VARCHAR(255) NOT NULL,
    ten_tai_khoan VARCHAR(120) NULL,
    ma_tien_te CHAR(3) NOT NULL,
    mac_dinh BOOLEAN NOT NULL DEFAULT FALSE,
    trang_thai ENUM('pending_verification','active','disabled') NOT NULL DEFAULT 'pending_verification',
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE CASCADE,
    INDEX idx_payout_accounts_user (nguoi_dung_id, trang_thai)
) ENGINE=InnoDB;

CREATE TABLE dot_chi_tra (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id BIGINT UNSIGNED NOT NULL,
    tai_khoan_nhan_tien_id BIGINT UNSIGNED NOT NULL,
    so_tien DECIMAL(12,2) NOT NULL,
    ma_tien_te CHAR(3) NOT NULL,
    trang_thai ENUM('requested','processing','paid','failed','cancelled') NOT NULL DEFAULT 'requested',
    ma_chi_tra_nha_cung_cap VARCHAR(190) NULL,
    ngay_yeu_cau TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_xu_ly DATETIME NULL,
    ly_do_that_bai VARCHAR(1000) NULL,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    FOREIGN KEY (tai_khoan_nhan_tien_id) REFERENCES tai_khoan_nhan_tien_nguoi_dung(ma) ON DELETE RESTRICT,
    CHECK (so_tien > 0),
    INDEX idx_payouts_user_status (nguoi_dung_id, trang_thai, ngay_yeu_cau)
) ENGINE=InnoDB;

CREATE TABLE chi_tiet_chi_tra (
    dot_chi_tra_id BIGINT UNSIGNED NOT NULL,
    so_ke_toan_id BIGINT UNSIGNED NOT NULL,
    so_tien DECIMAL(12,2) NOT NULL,
    PRIMARY KEY (dot_chi_tra_id, so_ke_toan_id),
    FOREIGN KEY (dot_chi_tra_id) REFERENCES dot_chi_tra(ma) ON DELETE CASCADE,
    FOREIGN KEY (so_ke_toan_id) REFERENCES so_ke_toan_tac_gia(ma) ON DELETE RESTRICT,
    CHECK (so_tien > 0)
) ENGINE=InnoDB;

CREATE TABLE ma_truy_cap_tro_choi (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    nguoi_tao_ma_id BIGINT UNSIGNED NOT NULL,
    ma_bam CHAR(64) NOT NULL UNIQUE,
    trang_thai ENUM('available','claimed','revoked','expired') NOT NULL DEFAULT 'available',
    nguoi_nhan_ma_id BIGINT UNSIGNED NULL,
    ngay_nhan_ma DATETIME NULL,
    ngay_het_han DATETIME NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    FOREIGN KEY (nguoi_tao_ma_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    FOREIGN KEY (nguoi_nhan_ma_id) REFERENCES nguoi_dung(ma) ON DELETE SET NULL,
    INDEX idx_access_keys_game_status (tro_choi_id, trang_thai)
) ENGINE=InnoDB;

CREATE TABLE thong_ke_tro_choi_hang_ngay (
    tro_choi_id BIGINT UNSIGNED NOT NULL,
    ngay_thong_ke DATE NOT NULL,
    luot_xem_trang BIGINT UNSIGNED NOT NULL DEFAULT 0,
    luot_nhan_tai_xuong BIGINT UNSIGNED NOT NULL DEFAULT 0,
    so_luot_mua BIGINT UNSIGNED NOT NULL DEFAULT 0,
    doanh_thu_gop DECIMAL(14,2) NOT NULL DEFAULT 0.00,
    luot_them_yeu_thich BIGINT UNSIGNED NOT NULL DEFAULT 0,
    so_luong_binh_luan BIGINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (tro_choi_id, ngay_thong_ke),
    FOREIGN KEY (tro_choi_id) REFERENCES tro_choi(ma) ON DELETE CASCADE,
    CHECK (doanh_thu_gop >= 0)
) ENGINE=InnoDB;

-- ============================================================
-- 6. BAO CAO, KIEM DUYET, NHAT KY HE THONG
-- ============================================================
CREATE TABLE bao_cao_noi_dung (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_bao_cao_id BIGINT UNSIGNED NULL,
    loai_doi_tuong ENUM('user','game','review','game_comment','devlog','community_topic','community_post') NOT NULL,
    doi_tuong_id BIGINT UNSIGNED NOT NULL,
    ma_ly_do VARCHAR(60) NOT NULL,
    chi_tiet MEDIUMTEXT NULL,
    trang_thai ENUM('open','investigating','resolved','rejected') NOT NULL DEFAULT 'open',
    nguoi_duoc_phan_cong_id BIGINT UNSIGNED NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ngay_giai_quyet DATETIME NULL,
    FOREIGN KEY (nguoi_bao_cao_id) REFERENCES nguoi_dung(ma) ON DELETE SET NULL,
    FOREIGN KEY (nguoi_duoc_phan_cong_id) REFERENCES nguoi_dung(ma) ON DELETE SET NULL,
    INDEX idx_reports_status_date (trang_thai, ngay_tao),
    INDEX idx_reports_target (loai_doi_tuong, doi_tuong_id)
) ENGINE=InnoDB;

CREATE TABLE hanh_dong_kiem_duyet (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_kiem_duyet_id BIGINT UNSIGNED NOT NULL,
    loai_doi_tuong ENUM('user','game','review','game_comment','devlog','community_topic','community_post') NOT NULL,
    doi_tuong_id BIGINT UNSIGNED NOT NULL,
    loai_hanh_dong ENUM('warning','hide','remove','suspend','restore','other') NOT NULL,
    ly_do TEXT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_kiem_duyet_id) REFERENCES nguoi_dung(ma) ON DELETE RESTRICT,
    INDEX idx_moderation_target (loai_doi_tuong, doi_tuong_id),
    INDEX idx_moderation_moderator_date (nguoi_kiem_duyet_id, ngay_tao)
) ENGINE=InnoDB;

CREATE TABLE nhat_ky_he_thong (
    ma BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nguoi_dung_id BIGINT UNSIGNED NULL,
    ma_hanh_dong VARCHAR(100) NOT NULL,
    loai_thuc_the VARCHAR(80) NULL,
    thuc_the_id BIGINT UNSIGNED NULL,
    dia_chi_ip VARCHAR(45) NULL,
    trinh_duyet_nguoi_dung VARCHAR(500) NULL,
    du_lieu_bo_sung TEXT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(ma) ON DELETE SET NULL,
    INDEX idx_audit_user_date (nguoi_dung_id, ngay_tao),
    INDEX idx_audit_entity (loai_thuc_the, thuc_the_id)
) ENGINE=InnoDB;

-- Liet ke cac bang da tao trong phien ban khong co nha phat hanh va cong tac vien game.
SHOW TABLES;
