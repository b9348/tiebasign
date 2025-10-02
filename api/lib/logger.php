<?php

/**
 * Vercel-compatible logging helper
 * 在 Vercel Serverless 环境下，error_log() 不会输出到 runtime logs
 * 需要使用 stderr 流来确保日志可见
 */

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}

/**
 * 输出日志到 stderr (Vercel-compatible)
 * @param string $message 日志消息
 * @param string $level 日志级别 (INFO, DEBUG, ERROR, WARNING)
 */
function vercel_log($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $formatted_message = "[{$timestamp}] [{$level}] {$message}\n";

    // 同时输出到 stderr 和 error_log
    if (defined('STDERR')) {
        fwrite(STDERR, $formatted_message);
    }
    error_log(trim($formatted_message));
}

/**
 * 快捷函数
 */
function log_info($message) {
    vercel_log($message, 'INFO');
}

function log_debug($message) {
    vercel_log($message, 'DEBUG');
}

function log_error($message) {
    vercel_log($message, 'ERROR');
}

function log_warning($message) {
    vercel_log($message, 'WARNING');
}
