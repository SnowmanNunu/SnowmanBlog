# 更新日志

所有项目的显著变更都将记录在此文件中。

格式基于 [Keep a Changelog](https://keepachangelog.com/zh-CN/1.0.0/)，
并且本项目遵循 [语义化版本](https://semver.org/lang/zh-CN/)。

## [未发布]

### 新增

- 数据库自动备份功能
- 贡献者文档（CONTRIBUTING.md、Issue/PR 模板）

## [1.9.2] - 2026-04-29

### 修复

- 移除 CI 中 `--parallel` 参数，避免缺少 paratest 依赖导致失败

## [1.9.1] - 2026-04-29

### 修复

- Laravel Pint 代码风格修复
- 补充 `package-lock.json` 以支持 CI 中的 `npm ci`

## [1.9.0] - 2026-04-29

### 新增

- 自动化测试与 CI/CD 支持
- Feature 测试覆盖前端主要页面（首页、文章、分类、标签、搜索、留言板、评论、Sitemap、RSS、点赞）
- Admin 测试覆盖后台登录与权限访问
- GitHub Actions 工作流：自动运行 PHPUnit 测试与 Laravel Pint 代码风格检查
- 新增 Post、Category、Tag、Comment、Guestbook 的 Model Factory

### 修复

- 兼容 SQLite 测试环境：MySQL FULLTEXT 索引迁移跳过 SQLite
- `User` 模型补充 `FilamentUser` 接口，解决后台访问 403 问题

## [1.8.0] - 2026-04-29

### 新增

- Docker 一键部署支持
- 提供完整的 Docker Compose 配置（PHP-FPM、Nginx、MySQL、Redis、定时任务）
- 新增 Docker 部署说明到 README

## [1.7.1] - 2026-04-28

### 新增

- 留言板支持博主回复
- 后台评论与留言管理增加批量审核功能
- 仪表盘增加今日 PV/UV 统计

### 修复

- 优化仪表盘数据库查询性能（减少 N+1 查询）
- 修复仪表盘待审内容模态框确认按钮不显示的问题

## [1.7.0] - 2026-04-27

### 新增

- 后台仪表盘优化：缓存管理、操作日志、最新待审内容快速审核
- 文章增加 views 浏览量统计
- 文章增加 meta_title、meta_description、meta_keywords SEO 字段
- 数据库性能优化索引

## [1.6.0] - 2026-04-25

### 新增

- 全局搜索支持（Ctrl+K 唤起，标题/内容/摘要搜索，关键词高亮）
- 友情链接管理
- 留言板功能
- Sitemap 与 RSS 订阅

## [1.5.0] - 2026-04-24

### 新增

- 文章评论系统（支持嵌套回复、邮件通知）
- 文章点赞功能
- 站点设置管理（标题、描述、ICP、管理员邮箱等）

## [1.4.0] - 2026-04-23

### 新增

- 基于 Filament v3 的后台管理面板
- 文章管理（发布、编辑、草稿、封面图、SEO、定时发布）
- 分类与标签管理
- 响应式前端主题（Tailwind CSS + Alpine.js）

## [1.0.0] - 2026-04-20

### 新增

- 项目初始版本
- Laravel 12 + Filament v3 基础架构
- 文章发布与展示功能
