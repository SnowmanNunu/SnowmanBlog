# SnowmanBlog

一个基于 Laravel + Filament 构建的个人博客系统，包含完整的前端展示与后台管理功能。

## 在线预览

- 博客首页：https://blog.snowmannunu.top
- 后台管理：https://blog.snowmannunu.top/admin

## 技术栈

### 后端
- **Laravel 12.x** — PHP 全栈框架，提供路由、ORM、认证等基础能力
- **PHP 8.2** — 运行环境
- **MySQL 5.7** — 关系型数据库，存储文章、分类、标签、留言等数据
- **Nginx + PHP-FPM** — Web 服务器与 PHP 进程管理

### 前端
- **Blade** — Laravel 内置模板引擎
- **Tailwind CSS (CDN)** — 实用优先的 CSS 框架，快速构建响应式界面
- **Alpine.js** — 轻量级 JavaScript 框架，实现搜索弹窗、下拉菜单等交互

### 后台管理
- **Filament v3** — 基于 Laravel 的后台管理面板，提供 CRUD、表单验证、文件上传、图表统计等能力

### 部署与运维
- **Gitee** — 代码托管与版本控制
- **Linux 云服务器** — 生产环境部署
- **自动推送脚本** — 定时提交并推送代码变更到远程仓库

## 功能特性

### 前端展示
- 文章列表与分页浏览
- 文章详情页（支持封面图、分类、标签展示）
- 文章分类与标签筛选
- **全局实时搜索**（`Ctrl + K` 唤起，支持标题/内容/摘要搜索，关键词红色高亮）
- 留言板（访客可发表留言，博主可在后台回复）
- 文章评论系统（支持嵌套回复）
- 响应式设计，适配桌面端与移动端

### 后台管理
- **文章管理** — 发布、编辑、草稿、封面图上传
- **分类管理** — 文章分类的增删改查
- **标签管理** — 文章标签的增删改查
- **留言管理** — 审核留言、博主回复
- **评论管理** — 审核评论、回复评论
- **站点设置** — 博客标题、描述、ICP 备案号等基础配置

## 项目结构

```
snowmanblog/
├── app/
│   ├── Filament/Resources/      # Filament 后台资源（Post、Category、Tag、Guestbook、Comment、Setting）
│   ├── Http/Controllers/        # 前端控制器（BlogController、GuestbookController、CommentController）
│   ├── Models/                  # Eloquent 模型
│   └── Providers/               # 服务提供者（视图合成器注入站点配置）
├── resources/views/             # Blade 模板（layouts/blog.blade.php、blog/index.blade.php 等）
├── routes/web.php               # Web 路由定义
└── auto-push.sh                 # 自动推送脚本
```

## 开发说明

本项目是个人博客的练手与实用项目，采用简洁的架构设计，便于后续扩展与维护。

## License

MIT License
