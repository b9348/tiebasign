# 百度贴吧云签到 - Vercel版

基于原版[Tieba-Cloud-Sign](https://github.com/MoeNetwork/Tieba-Cloud-Sign)迁移到Vercel Serverless平台。

## ✨ 特性

- ✅ **完全免费** - 使用Vercel免费托管
- ✅ **自动签到** - 每天自动执行签到任务
- ✅ **全球加速** - Vercel全球CDN加速
- ✅ **零运维** - 无需服务器管理
- ✅ **高可用** - Serverless架构自动扩展

## 🚀 快速开始

### 1. 准备工作

- 一个Vercel账号（免费）
- 一个MySQL数据库（可以使用你现有的）
- Git基础知识

### 2. 部署步骤

#### 2.1 Fork本项目

点击右上角Fork按钮，将项目Fork到你的GitHub账号。

#### 2.2 导入到Vercel

1. 访问 [Vercel Dashboard](https://vercel.com/dashboard)
2. 点击 "Add New Project"
3. 选择你Fork的仓库
4. 点击 "Import"

#### 2.3 配置环境变量

在Vercel项目设置中添加以下环境变量：

```env
DB_HOST=your-mysql-host.com
DB_USER=your-mysql-username
DB_PASSWD=your-mysql-password
DB_NAME=tiebacloud
DB_PREFIX=tc_
DB_SSL=0
SYSTEM_SALT=your-random-salt
ANTI_CSRF=true
CRON_SECRET=your-random-secret
```

#### 2.4 导入数据库

使用 `setup/install.template.sql` 文件创建数据库表结构。

```bash
mysql -u your-username -p your-database < setup/install.template.sql
```

#### 2.5 部署

点击 "Deploy" 按钮，等待部署完成。

### 3. 访问你的站点

部署完成后，Vercel会提供一个域名，例如：`https://your-project.vercel.app`

## 📋 功能说明

### 自动签到

系统会在每天凌晨2点自动执行签到任务，凌晨3点执行重试任务。

你可以在 `vercel.json` 中修改Cron表达式：

```json
{
  "crons": [
    {
      "path": "/api/cron.php?task=sign",
      "schedule": "0 2 * * *"  // 每天凌晨2点
    },
    {
      "path": "/api/cron.php?task=retry",
      "schedule": "0 3 * * *"  // 每天凌晨3点
    }
  ]
}
```

### 手动触发

你也可以手动触发签到任务：

```bash
curl -X GET "https://your-project.vercel.app/api/cron.php?task=sign" \
  -H "Authorization: Bearer your-cron-secret"
```

## ⚙️ 配置说明

### 环境变量

| 变量名 | 说明 | 必需 | 默认值 |
|--------|------|------|--------|
| `DB_HOST` | MySQL主机地址 | ✅ | - |
| `DB_USER` | MySQL用户名 | ✅ | - |
| `DB_PASSWD` | MySQL密码 | ✅ | - |
| `DB_NAME` | 数据库名称 | ✅ | tiebacloud |
| `DB_PREFIX` | 表前缀 | ❌ | tc_ |
| `DB_SSL` | 启用SSL | ❌ | 0 |
| `SYSTEM_SALT` | 加密盐 | ⚠️ | - |
| `ANTI_CSRF` | CSRF防护 | ❌ | true |
| `CRON_SECRET` | Cron密钥 | ⚠️ | - |

### Vercel配置

`vercel.json` 文件包含了所有Vercel相关配置：

- **Runtime**: 使用 `vercel-php@0.7.4`
- **Memory**: 1024MB
- **Max Duration**: 10秒（Hobby版）
- **Cron Jobs**: 自动签到任务

## 🔧 本地开发

### 安装PHP

确保你的系统安装了PHP 8.0+：

```bash
php -v
```

### 运行开发服务器

```bash
php -S localhost:8000 api/index.php
```

访问 `http://localhost:8000`

### 测试Cron任务

```bash
php api/cron.php task=sign
```

## 📝 与原版的区别

### 移除的功能

- ❌ **在线更新** - Serverless环境只读
- ❌ **文件上传** - 需要使用对象存储
- ❌ **本地缓存** - 使用数据库或外部缓存

### 调整的功能

- ✅ **配置管理** - 使用环境变量
- ✅ **Cron任务** - 使用Vercel Cron Jobs
- ✅ **路径处理** - 适配Serverless结构

## 🐛 故障排除

### 数据库连接失败

检查环境变量是否正确配置，特别是 `DB_HOST`、`DB_USER`、`DB_PASSWD`。

### Cron任务不执行

1. 检查Vercel项目是否在Pro计划（Hobby计划Cron不保证准时）
2. 检查 `CRON_SECRET` 是否配置
3. 查看Vercel Logs确认任务是否被触发

### 签到失败

1. 检查百度账号BDUSS是否过期
2. 查看错误日志
3. 确认网络连接正常

## 📚 相关链接

- [原版项目](https://github.com/MoeNetwork/Tieba-Cloud-Sign)
- [Vercel文档](https://vercel.com/docs)
- [vercel-php文档](https://github.com/vercel-community/php)

## 📄 许可证

MIT License - 继承自原项目

## 🙏 致谢

- 原项目作者和贡献者
- Vercel团队
- vercel-php社区

## ⚠️ 免责声明

本项目仅供学习交流使用，请勿用于非法用途。使用本项目所产生的一切后果由使用者自行承担。

