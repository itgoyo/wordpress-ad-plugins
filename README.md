# Floating Ad Manager

一个面向 WordPress 的悬浮广告管理插件，支持在后台可视化配置多个悬浮广告位，并允许直接输入 HTML、CSS、JavaScript 代码在前端展示。

<img width="879" height="105" alt="image" src="https://github.com/user-attachments/assets/71367ebc-82ba-4673-907b-adcb3cbb4d7b" />


## 功能特性

- 多广告位管理：支持添加多个独立广告配置。
- 位置控制：支持 8 个悬浮位置。
  - 左上、正上、右上
  - 左中、右中
  - 左下、正下、右下
- 间距设置：支持上、右、下、左边距独立配置（px）。
- 层级设置：支持自定义 z-index。
- 页面范围：可按页面类型控制显示范围（全部页面、首页、文章页、独立页面）。
- 关闭按钮控制：可在后台控制是否允许用户关闭广告。
- 代码实时预览：后台输入代码后可查看预览。
- 自动刷新开关：可开启/关闭输入时自动刷新预览。
- 手动刷新预览：关闭自动刷新后可手动刷新。
- 错误日志面板：实时展示预览代码运行时错误，支持一键清空。

## 适用场景

- 固定悬浮咨询按钮
- 活动弹出入口
- 营销推广按钮
- 自定义脚本挂件

## 环境要求

- WordPress 5.8+
- PHP 7.4+

## 安装方式

### 方式一：源码目录安装

1. 将插件目录上传到 WordPress 插件目录：
   - wp-content/plugins/ad-plugins
2. 登录 WordPress 后台，进入 插件 页面。
3. 启用 Floating Ad Manager 插件。

### 方式二：Git 克隆

1. 进入插件目录：
   - wp-content/plugins
2. 克隆仓库：

   ```bash
   git clone https://github.com/itgoyo/ad-plugins.git
   ```

3. 进入 WordPress 后台启用插件。

## 使用说明

1. 后台进入 悬浮广告 菜单。
2. 点击 添加新广告位。
3. 配置广告信息：
   - 名称（可选）
   - 位置
   - 边距
   - z-index
   - 是否显示关闭按钮
   - 显示页面范围
4. 在 广告代码 输入框粘贴 HTML/CSS/JS。
5. 通过 代码实时预览 区域验证效果。
6. 如有报错，可在 错误日志 查看详情。
7. 点击 保存所有设置。

## 预览与调试能力

- 自动刷新预览：默认开启，输入代码自动渲染。
- 手动刷新预览：在自动刷新关闭时，手动触发渲染。
- 错误日志：捕获运行时错误、Promise 未处理异常和 console.error。
- 清空日志：一键重置当前广告位日志。

## 目录结构

```text
ad-plugins/
├── assets/
│   ├── admin.css
│   └── admin.js
├── templates/
│   └── admin-page.php
├── floating-ad.php
└── README.md
```

## 安全说明

- 插件允许管理员输入前端代码（含 JavaScript），仅建议可信管理员使用。
- 由于广告代码会直接输出到前端，请勿粘贴来源不明的脚本。
- 建议在生产环境使用前先在测试站验证。

## 当前版本

- Version: 1.0.3

## 更新记录

### 1.0.3

- 新增自动刷新预览开关
- 新增手动刷新预览按钮
- 新增清空错误日志按钮
- 优化预览与日志交互体验

### 1.0.2

- 新增手动刷新预览与日志管理能力
- 优化后台交互体验

### 1.0.1

- 修复位置示意与勾选不同步问题
- 提升实时预览渲染兼容性

### 1.0.0

- 首个版本发布
- 支持多广告位、位置控制、边距设置、页面显示范围、关闭按钮控制

<img width="960" height="839" alt="image" src="https://github.com/user-attachments/assets/e5f0c37e-00bc-49b9-8823-c8e36afcfc86" />


## License

GPL v2 or later
