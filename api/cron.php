<?php

/**
 * Vercel Cron Jobs 入口
 * 处理定时签到任务
 */

// 验证Vercel Cron请求
// Vercel Cron会在请求头中添加特殊标识
$isVercelCron = isset($_SERVER['HTTP_USER_AGENT']) && 
                strpos($_SERVER['HTTP_USER_AGENT'], 'vercel-cron') !== false;

// 如果配置了CRON_SECRET，则验证Authorization头
$cronSecret = getenv('CRON_SECRET');
if ($cronSecret) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if ($authHeader !== 'Bearer ' . $cronSecret) {
        http_response_code(401);
        die(json_encode(['error' => 'Unauthorized']));
    }
}

// 如果不是Vercel Cron且没有正确的密钥，拒绝访问
if (!$isVercelCron && !$cronSecret) {
    http_response_code(403);
    die(json_encode(['error' => 'Forbidden']));
}

// 设置为不需要登录
define('SYSTEM_DO_NOT_LOGIN', true);

// 加载初始化文件
require __DIR__ . '/_init.php';

// 忽略用户中断
ignore_user_abort(true);

// 设置执行时间限制（Vercel Hobby: 10s, Pro: 60s）
set_time_limit(0);

// 获取任务类型
$task = $_GET['task'] ?? 'sign';

// 记录开始时间
$startTime = microtime(true);

try {
    switch ($task) {
        case 'sign':
            // 执行签到任务
            $result = cron::run('lib/cron_system_sign.php', 'system_sign');
            $message = '签到任务执行完成';
            break;
            
        case 'retry':
            // 执行重试任务
            $result = cron::run('lib/cron_system_sign_retry.php', 'system_sign_retry');
            $message = '重试任务执行完成';
            break;
            
        default:
            http_response_code(400);
            die(json_encode(['error' => 'Invalid task type']));
    }
    
    // 计算执行时间
    $executionTime = round(microtime(true) - $startTime, 2);
    
    // 返回成功响应
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'task' => $task,
        'message' => $message,
        'execution_time' => $executionTime . 's',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    // 错误处理
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'task' => $task,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

