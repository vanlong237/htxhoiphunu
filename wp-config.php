<?php
/**
 * Cấu hình cơ bản cho WordPress
 *
 * Trong quá trình cài đặt, file "wp-config.php" sẽ được tạo dựa trên nội dung 
 * mẫu của file này. Bạn không bắt buộc phải sử dụng giao diện web để cài đặt, 
 * chỉ cần lưu file này lại với tên "wp-config.php" và điền các thông tin cần thiết.
 *
 * File này chứa các thiết lập sau:
 *
 * * Thiết lập MySQL
 * * Các khóa bí mật
 * * Tiền tố cho các bảng database
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Thiết lập MySQL - Bạn có thể lấy các thông tin này từ host/server ** //
/** Tên database MySQL */
define( 'DB_NAME', 'shop' );

/** Username của database */
define( 'DB_USER', 'root' );

/** Mật khẩu của database */
define( 'DB_PASSWORD', '' );

/** Hostname của database */
define( 'DB_HOST', 'localhost' );

/** Database charset sử dụng để tạo bảng database. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Kiểu database collate. Đừng thay đổi nếu không hiểu rõ. */
define('DB_COLLATE', '');

/**#@+
 * Khóa xác thực và salt.
 *
 * Thay đổi các giá trị dưới đây thành các khóa không trùng nhau!
 * Bạn có thể tạo ra các khóa này bằng công cụ
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Bạn có thể thay đổi chúng bất cứ lúc nào để vô hiệu hóa tất cả
 * các cookie hiện có. Điều này sẽ buộc tất cả người dùng phải đăng nhập lại.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '(tO0Fx~od*Y-])<#}2*4w C(pF~Fr$YyBqvg!zXcuRp|!CP!M9)7`cEtjJr^5^q}' );
define( 'SECURE_AUTH_KEY',  'e>S0|<2/cI9I-}a. v/y,PQVVp)q}(H4 b2DGC;oW%djA4N.J:E:AV8XN61J=iXD' );
define( 'LOGGED_IN_KEY',    'y;H5[|$$:bpMK?UE;Fp~Qhd!]rh <WPVbDkQoBjgW=LmX)4[&8[eH+5M+<-JAc,5' );
define( 'NONCE_KEY',        '@JJYQvu:^@IR|YQW@40Wx;J)`3kp?aU37g5E$U!1V5 V(Fway(HtcI~gWN>+b.b+' );
define( 'AUTH_SALT',        'gasZv!B/Nqe !(I7.}Fr7EJ|WLBs0/N;6D2rs*8wW!>SFzbtFXQqOpNjq5r-.rpV' );
define( 'SECURE_AUTH_SALT', '}~y,&k*d$Dvp%I68rn-QICnrU$0r~Z]*u6%}HyzRQO3 3<4N+BGf4Q#t0>,W8@57' );
define( 'LOGGED_IN_SALT',   '-b&W|-:5$8o=u:Iqg90%bPggaA3#b7IIp|n<$}!c975L=fx-8,/=;pqyQ[QnV52y' );
define( 'NONCE_SALT',       'ftqQAx52fb#ZSa8Q</bX~8Jbr7zVOB1ME:fDE5A*^Mw]Ip0f/7BSk[%Rkv>(kGz7' );

/**#@-*/

/**
 * Tiền tố cho bảng database.
 *
 * Đặt tiền tố cho bảng giúp bạn có thể cài nhiều site WordPress vào cùng một database.
 * Chỉ sử dụng số, ký tự và dấu gạch dưới!
 */
$table_prefix  = 'wp_';

/**
 * Dành cho developer: Chế độ debug.
 *
 * Thay đổi hằng số này thành true sẽ làm hiện lên các thông báo trong quá trình phát triển.
 * Chúng tôi khuyến cáo các developer sử dụng WP_DEBUG trong quá trình phát triển plugin và theme.
 *
 * Để có thông tin về các hằng số khác có thể sử dụng khi debug, hãy xem tại Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);
define('FS_METHOD', 'direct');

/* Đó là tất cả thiết lập, ngưng sửa từ phần này trở xuống. Chúc bạn viết blog vui vẻ. */

/** Đường dẫn tuyệt đối đến thư mục cài đặt WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Thiết lập biến và include file. */
require_once(ABSPATH . 'wp-settings.php');

