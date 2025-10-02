<?php

/**
 * MySQL 自动按需加载和配置
 * @author Kenvix
 */

/**
 * 数据库连接模式
 * mysqli 或 mysql
 */
define('SQLMODE', 'mysqli');
/**
 * 是否开启数据库长连接
 * bool true=开启 | false=关闭
 */
define('LONGSQL', false);

log_debug("[DB] Starting database connection...");
log_debug("[DB] Host: " . DB_HOST . ", DB: " . DB_NAME);

if (class_exists("mysqli") && SQLMODE != 'mysql') {
    require SYSTEM_ROOT . '/lib/class.mysqli.php';
} else {
    require SYSTEM_ROOT . '/lib/class.mysql.php';
}
require SYSTEM_ROOT . '/lib/class.S.php';

$start_time = microtime(true);
$m = new S(DB_HOST, DB_USER, DB_PASSWD, DB_NAME, LONGSQL, DB_SSL); //以后直接使用$m->函数()即可操作数据库
$connect_time = round((microtime(true) - $start_time) * 1000, 2);
log_info("[DB] Database connected successfully in {$connect_time}ms");
