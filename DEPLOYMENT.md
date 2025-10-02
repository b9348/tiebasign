# 部署指南

## 📋 前置要求

1. **Vercel账号** - 访问 [vercel.com](https://vercel.com) 注册（免费）
2. **MySQL数据库** - 你已有的VPS MySQL服务
3. **GitHub账号** - 用于托管代码

## 🚀 部署步骤

### 第一步：准备数据库

#### 1.1 创建数据库

```bash
mysql -uroot -p123456 -e "CREATE DATABASE IF NOT EXISTS tiebacloud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

#### 1.2 导入数据表

```bash
mysql -uroot -p123456 tiebacloud < api/setup/install.template.sql
```

#### 1.3 验证数据库

```bash
mysql -uroot -p123456 tiebacloud -e "SHOW TABLES;"
```

应该看到以下表：
- `tc_users`
- `tc_baiduid`
- `tc_tieba`
- `tc_cron`
- `tc_options`
- `tc_plugins`

### 第二步：推送代码到GitHub

#### 2.1 初始化Git仓库

```bash
git init
git add .
git commit -m "Initial commit: Tieba Cloud Sign for Vercel"
```

#### 2.2 创建GitHub仓库

1. 访问 [github.com/new](https://github.com/new)
2. 仓库名：`tieba-cloud-sign-vercel`
3. 设置为Private（推荐）
4. 不要初始化README

#### 2.3 推送代码

```bash
git remote add origin https://github.com/你的用户名/tieba-cloud-sign-vercel.git
git branch -M main
git push -u origin main
```

### 第三步：部署到Vercel

#### 3.1 导入项目

1. 访问 [vercel.com/dashboard](https://vercel.com/dashboard)
2. 点击 "Add New..." → "Project"
3. 选择你的GitHub仓库 `tieba-cloud-sign-vercel`
4. 点击 "Import"

#### 3.2 配置项目

**Framework Preset**: 选择 "Other"

**Root Directory**: 保持默认 `./`

**Build Command**: 留空

**Output Directory**: 留空

#### 3.3 配置环境变量

点击 "Environment Variables"，添加以下变量：

```env
# 数据库配置
DB_HOST=你的MySQL主机地址
DB_USER=root
DB_PASSWD=123456
DB_NAME=tiebacloud
DB_PREFIX=tc_
DB_SSL=0

# 系统配置
SYSTEM_SALT=随机字符串（建议32位）
ANTI_CSRF=true

# Cron密钥
CRON_SECRET=随机字符串（建议32位）

# 时区
PHP_TIMEZONE=Asia/Shanghai
```

**生成随机字符串**：
```bash
# Linux/Mac
openssl rand -hex 16

# Windows PowerShell
-join ((48..57) + (65..90) + (97..122) | Get-Random -Count 32 | % {[char]$_})
```

#### 3.4 部署

点击 "Deploy" 按钮，等待部署完成（约1-2分钟）。

### 第四步：配置Cron Jobs

#### 4.1 升级到Pro计划（可选）

- **Hobby计划**：Cron Jobs每小时执行一次（不精确）
- **Pro计划**：Cron Jobs按分钟精确执行

如果你使用Hobby计划，Vercel会在指定时间的前后1小时内执行任务。

#### 4.2 验证Cron配置

Cron Jobs已在 `vercel.json` 中配置：

```json
{
  "crons": [
    {
      "path": "/api/cron.php?task=sign",
      "schedule": "0 2 * * *"
    },
    {
      "path": "/api/cron.php?task=retry",
      "schedule": "0 3 * * *"
    }
  ]
}
```

部署后，Vercel会自动启用这些Cron Jobs。

### 第五步：初始化系统

#### 5.1 访问你的站点

部署完成后，Vercel会提供一个域名，例如：
```
https://tieba-cloud-sign-vercel.vercel.app
```

#### 5.2 创建管理员账号

首次访问时，系统会提示创建管理员账号。

#### 5.3 添加百度账号

1. 登录后台
2. 点击 "百度账号管理"
3. 添加你的百度账号BDUSS

**获取BDUSS**：
1. 登录百度贴吧网页版
2. 按F12打开开发者工具
3. 切换到 "Application" → "Cookies"
4. 找到 `BDUSS` 字段，复制值

#### 5.4 添加贴吧

1. 点击 "贴吧管理"
2. 添加要签到的贴吧

### 第六步：测试

#### 6.1 手动触发签到

```bash
curl -X GET "https://你的域名.vercel.app/api/cron.php?task=sign" \
  -H "Authorization: Bearer 你的CRON_SECRET"
```

#### 6.2 查看日志

在Vercel Dashboard中：
1. 进入你的项目
2. 点击 "Deployments"
3. 点击最新的部署
4. 点击 "Functions" 查看日志

## 🔧 高级配置

### 自定义域名

1. 在Vercel Dashboard中点击 "Settings" → "Domains"
2. 添加你的域名
3. 按照提示配置DNS

### 配置SMTP邮件

在环境变量中添加：

```env
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USER=your-email@example.com
MAIL_PASS=your-password
MAIL_FROM=noreply@example.com
```

### 启用插件

插件已自动迁移到 `api/plugins/` 目录，在后台管理界面启用即可。

## 🐛 故障排除

### 数据库连接失败

**症状**：页面显示数据库连接错误

**解决方案**：
1. 检查环境变量 `DB_HOST`、`DB_USER`、`DB_PASSWD` 是否正确
2. 确认MySQL允许远程连接
3. 检查防火墙是否开放3306端口

```bash
# 允许远程连接
mysql -uroot -p123456 -e "GRANT ALL PRIVILEGES ON tiebacloud.* TO 'root'@'%' IDENTIFIED BY '123456';"
mysql -uroot -p123456 -e "FLUSH PRIVILEGES;"
```

### Cron任务不执行

**症状**：签到任务没有自动执行

**解决方案**：
1. 检查Vercel Dashboard → Functions → Logs
2. 确认 `CRON_SECRET` 环境变量已配置
3. Hobby计划的Cron不保证准时，考虑升级Pro

### 页面500错误

**症状**：访问页面显示500错误

**解决方案**：
1. 查看Vercel Functions日志
2. 检查PHP语法错误
3. 确认所有环境变量已配置

### 签到失败

**症状**：签到任务执行但失败

**解决方案**：
1. 检查BDUSS是否过期（重新获取）
2. 查看错误日志
3. 确认网络连接正常

## 📊 监控和维护

### 查看执行日志

```bash
# 使用Vercel CLI
vercel logs --follow

# 或在Dashboard中查看
# Deployments → 最新部署 → Functions → Logs
```

### 更新代码

```bash
git add .
git commit -m "Update: 描述你的更改"
git push

# Vercel会自动重新部署
```

### 备份数据库

```bash
mysqldump -uroot -p123456 tiebacloud > backup_$(date +%Y%m%d).sql
```

## 🎉 完成！

现在你的贴吧云签到系统已经成功部署到Vercel！

- 🌐 访问地址：`https://你的域名.vercel.app`
- ⏰ 自动签到：每天凌晨2点
- 🔄 自动重试：每天凌晨3点

## 📚 相关资源

- [Vercel文档](https://vercel.com/docs)
- [vercel-php文档](https://github.com/vercel-community/php)
- [原项目文档](https://github.com/MoeNetwork/Tieba-Cloud-Sign/wiki)

## 💡 提示

- 定期检查BDUSS是否过期
- 定期备份数据库
- 关注Vercel的使用配额
- 考虑使用自定义域名提升访问速度

