# 贡献指南

感谢你对 SnowmanBlog 的兴趣！本文档将帮助你快速参与项目开发。

## 开发环境准备

### 环境要求

- PHP >= 8.2
- Composer
- MySQL 5.7+ 或 SQLite
- Node.js >= 18 + npm

### 本地安装

1. Fork 本仓库并克隆到本地：

```bash
git clone https://github.com/你的用户名/SnowmanBlog.git
cd SnowmanBlog
```

2. 安装依赖：

```bash
composer install
npm install
npm run build
```

3. 配置环境：

```bash
cp .env.example .env
php artisan key:generate
```

4. 编辑 `.env` 配置数据库（推荐使用 SQLite 快速开始）：

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/SnowmanBlog/database/database.sqlite
```

5. 创建数据库并运行迁移：

```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed --class=SettingSeeder
```

6. 创建管理员账号：

```bash
php artisan tinker
```

```php
App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
]);
```

7. 启动开发服务器：

```bash
php artisan serve
```

访问 http://localhost:8000 查看前台，http://localhost:8000/admin 进入后台。

## 提交规范

本项目采用 [Conventional Commits](https://www.conventionalcommits.org/zh-hans/v1.0.0/) 规范，提交信息格式如下：

```
<type>(<scope>): <subject>

[可选的详细描述]

[可选的 Footer]
```

### Type 说明

| 类型 | 含义 |
|------|------|
| `feat` | 新功能 |
| `fix` | 修复 Bug |
| `docs` | 仅文档变更 |
| `style` | 代码风格调整（不影响代码逻辑） |
| `refactor` | 重构（既不是新功能也不是修复 Bug） |
| `perf` | 性能优化 |
| `test` | 增加或修改测试 |
| `chore` | 构建过程或辅助工具的变动 |
| `ci` | CI 配置变更 |

### 示例

```
feat: 增加文章定时发布功能

fix(comment): 修复评论邮件通知失败的问题
docs: 更新安装教程
test: 增加 FrontendTest 覆盖搜索接口
```

## 分支策略

- `master`：主分支，始终保持可部署状态
- `feature/*`：功能分支，从 `master` 检出，开发完成后合并回 `master`
- `fix/*`：修复分支，从 `master` 检出，修复完成后合并回 `master`

### 工作流程

1. 从最新 `master` 创建分支：

```bash
git checkout master
git pull origin master
git checkout -b feature/你的功能名
```

2. 开发并提交代码

3. 确保代码风格正确：

```bash
vendor/bin/pint
```

4. 确保测试通过：

```bash
php artisan test
```

5. 推送到你的 Fork 并提交 Pull Request

## Pull Request 规范

- PR 标题遵循提交规范格式
- 描述中说明改动内容和测试方式
- 确保 CI 检查通过
- 需要至少一个 Reviewer 批准后才能合并

## 代码风格

本项目使用 [Laravel Pint](https://laravel.com/docs/pint) 进行代码风格检查，提交前请运行：

```bash
vendor/bin/pint
```

## 问题反馈

如果你发现了 Bug 或有功能建议，请先搜索 [Issues](https://github.com/SnowmanNunu/SnowmanBlog/issues) 确认是否已存在。如果不存在，欢迎创建新的 Issue。

## 许可证

通过向本项目提交代码，你同意将你的贡献置于 [MIT License](LICENSE) 之下。
