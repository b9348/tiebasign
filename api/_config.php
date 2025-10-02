<?php

/**
 * 贴吧云签到 - Vercel配置文件
 * 从环境变量读取配置，适配Serverless环境
 */

// 特别警告：禁止使用记事本编辑！

////////////////////////////数据库配置////////////////////////////
// MySQL 数据库地址
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');

// MySQL 数据库用户名
define('DB_USER', getenv('DB_USER') ?: 'root');

// MySQL 数据库密码
define('DB_PASSWD', getenv('DB_PASSWD') ?: '');

// MySQL 数据库名称
define('DB_NAME', getenv('DB_NAME') ?: 'tiebacloud');

// MySQL 启用SSL连接
define('DB_SSL', (int)(getenv('DB_SSL') ?: 0));

////////////////////////////数据库前缀////////////////////////////
// 数据库前缀，建议保持默认
define('DB_PREFIX', getenv('DB_PREFIX') ?: 'tc_');

///////////////////////////////////////其他设置///////////////////////////////////////
// CSRF防御
define('ANTI_CSRF', filter_var(
    getenv('ANTI_CSRF') ?: 'true',
    FILTER_VALIDATE_BOOLEAN
));

// 加密用盐
define('SYSTEM_SALT', getenv('SYSTEM_SALT') ?: '');

// Vercel Serverless环境标识（仅在未定义时定义）
if (!defined('IS_VERCEL')) {
    define('IS_VERCEL', true);
}
if (!defined('IS_SERVERLESS')) {
    define('IS_SERVERLESS', true);
}

// 禁用文件写入相关功能
define('DISABLE_FILE_WRITE', true);
define('DISABLE_UPDATE', true);

