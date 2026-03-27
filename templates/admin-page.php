<?php
if (!defined('ABSPATH')) exit;
?>
<div class="wrap fam-wrap">
    <h1>
        <span class="dashicons dashicons-megaphone" style="font-size:28px;margin-right:8px;"></span>
        <?php echo esc_html__('悬浮广告管理', 'floating-ad-manager'); ?>
    </h1>
    <p class="description"><?php echo esc_html__('配置网站悬浮广告，支持多个广告位、自定义位置和间距。', 'floating-ad-manager'); ?></p>

    <form method="post" action="options.php" id="fam-form">
        <?php settings_fields('fam_settings_group'); ?>

        <div id="fam-ads-container">
            <?php if (!empty($ads)) : ?>
                <?php foreach ($ads as $i => $ad) : ?>
                    <div class="fam-ad-block" data-index="<?php echo $i; ?>">
                        <div class="fam-ad-header">
                            <span class="fam-ad-title">
                                <span class="dashicons dashicons-menu"></span>
                                广告 #<?php echo $i + 1; ?>
                                <?php if (!empty($ad['name'])) : ?>
                                    - <?php echo esc_html($ad['name']); ?>
                                <?php endif; ?>
                            </span>
                            <div class="fam-ad-header-actions">
                                <label class="fam-switch-label">
                                    <input type="checkbox" name="fam_ads[<?php echo $i; ?>][enabled]" value="1"
                                        <?php checked(!empty($ad['enabled'])); ?>>
                                    <span class="fam-switch-slider"></span>
                                </label>
                                <button type="button" class="fam-toggle-btn" title="展开/折叠">
                                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                                </button>
                                <button type="button" class="fam-remove-btn" title="删除此广告">
                                    <span class="dashicons dashicons-trash"></span>
                                </button>
                            </div>
                        </div>
                        <div class="fam-ad-body">
                            <table class="form-table">
                                <tr>
                                    <th><label>广告名称</label></th>
                                    <td>
                                        <input type="text" class="regular-text"
                                               name="fam_ads[<?php echo $i; ?>][name]"
                                               value="<?php echo esc_attr($ad['name'] ?? ''); ?>"
                                               placeholder="便于识别的名称（选填）">
                                    </td>
                                </tr>
                                <tr>
                                    <th><label>悬浮位置</label></th>
                                    <td>
                                        <div class="fam-position-grid">
                                            <?php
                                            $positions = [
                                                'top-left'     => '↖ 左上角',
                                                'top-center'   => '↑ 正上方',
                                                'top-right'    => '↗ 右上角',
                                                'center-left'  => '← 居中左',
                                                'center-right' => '→ 居中右',
                                                'bottom-left'  => '↙ 左下角',
                                                'bottom-center'=> '↓ 正下方',
                                                'bottom-right' => '↘ 右下角',
                                            ];
                                            $current_pos = $ad['position'] ?? 'bottom-right';
                                            foreach ($positions as $val => $label) :
                                            ?>
                                                <label class="fam-position-option <?php echo $current_pos === $val ? 'active' : ''; ?>">
                                                    <input type="radio"
                                                           name="fam_ads[<?php echo $i; ?>][position]"
                                                           value="<?php echo esc_attr($val); ?>"
                                                           <?php checked($current_pos, $val); ?>>
                                                    <span><?php echo esc_html($label); ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label>边距设置 (px)</label></th>
                                    <td>
                                        <div class="fam-margin-grid">
                                            <div class="fam-margin-item">
                                                <label>上</label>
                                                <input type="number" name="fam_ads[<?php echo $i; ?>][margin_top]"
                                                       value="<?php echo esc_attr($ad['margin_top'] ?? 20); ?>" min="0" max="500">
                                            </div>
                                            <div class="fam-margin-item">
                                                <label>右</label>
                                                <input type="number" name="fam_ads[<?php echo $i; ?>][margin_right]"
                                                       value="<?php echo esc_attr($ad['margin_right'] ?? 20); ?>" min="0" max="500">
                                            </div>
                                            <div class="fam-margin-item">
                                                <label>下</label>
                                                <input type="number" name="fam_ads[<?php echo $i; ?>][margin_bottom]"
                                                       value="<?php echo esc_attr($ad['margin_bottom'] ?? 20); ?>" min="0" max="500">
                                            </div>
                                            <div class="fam-margin-item">
                                                <label>左</label>
                                                <input type="number" name="fam_ads[<?php echo $i; ?>][margin_left]"
                                                       value="<?php echo esc_attr($ad['margin_left'] ?? 20); ?>" min="0" max="500">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label>层级 (z-index)</label></th>
                                    <td>
                                        <input type="number" name="fam_ads[<?php echo $i; ?>][z_index]"
                                               value="<?php echo esc_attr($ad['z_index'] ?? 9999); ?>" min="1" max="999999"
                                               class="small-text">
                                    </td>
                                </tr>
                                <tr>
                                    <th><label>显示关闭按钮</label></th>
                                    <td>
                                        <label>
                                            <input type="checkbox" name="fam_ads[<?php echo $i; ?>][show_close]" value="1"
                                                <?php checked(!empty($ad['show_close'])); ?>>
                                            显示关闭按钮（允许用户手动关闭）
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label>显示页面</label></th>
                                    <td>
                                        <select name="fam_ads[<?php echo $i; ?>][show_on]">
                                            <?php
                                            $show_options = [
                                                'all'  => '所有页面',
                                                'home' => '仅首页',
                                                'post' => '仅文章页',
                                                'page' => '仅独立页面',
                                            ];
                                            $current_show = $ad['show_on'] ?? 'all';
                                            foreach ($show_options as $val => $label) :
                                            ?>
                                                <option value="<?php echo esc_attr($val); ?>"
                                                    <?php selected($current_show, $val); ?>>
                                                    <?php echo esc_html($label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label>广告代码</label></th>
                                    <td>
                                        <textarea name="fam_ads[<?php echo $i; ?>][code]"
                                                  class="fam-code-editor"
                                                  rows="10"
                                                  placeholder="在此输入 HTML / CSS / JavaScript 代码..."><?php echo esc_textarea($ad['code'] ?? ''); ?></textarea>
                                        <p class="description">支持 HTML、CSS、JavaScript 代码。输入后会自动刷新下方实时预览并展示错误日志。</p>
                                    </td>
                                </tr>
                            </table>
                            <div class="fam-preview-section">
                                <h4>预览位置示意</h4>
                                <div class="fam-preview-box">
                                    <div class="fam-preview-dot" data-pos="<?php echo esc_attr($current_pos); ?>"></div>
                                </div>
                                <h4>代码实时预览</h4>
                                <div class="fam-preview-toolbar">
                                    <label class="fam-auto-preview-label">
                                        <input type="checkbox" class="fam-auto-preview-toggle" checked>
                                        自动刷新预览
                                    </label>
                                    <button type="button" class="button button-secondary fam-refresh-preview">手动刷新预览</button>
                                    <button type="button" class="button fam-clear-errors">清空错误日志</button>
                                </div>
                                <div class="fam-live-preview" data-preview-root="1">
                                    <iframe class="fam-live-preview-frame" sandbox="allow-scripts allow-same-origin"></iframe>
                                    <div class="fam-live-preview-empty">在上方输入广告代码后，这里会显示实时效果</div>
                                </div>
                                <div class="fam-error-log" aria-live="polite">
                                    <div class="fam-error-log-title">错误日志</div>
                                    <div class="fam-error-log-content">暂无错误</div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="fam-actions">
            <button type="button" id="fam-add-ad" class="button button-secondary">
                <span class="dashicons dashicons-plus-alt" style="margin-top:4px;"></span>
                添加新广告位
            </button>
            <?php submit_button('保存所有设置', 'primary', 'submit', false); ?>
        </div>
    </form>
</div>

<!-- 新广告模板（JS用） -->
<script type="text/html" id="fam-ad-template">
    <div class="fam-ad-block" data-index="__INDEX__">
        <div class="fam-ad-header">
            <span class="fam-ad-title">
                <span class="dashicons dashicons-menu"></span>
                广告 #__NUM__
            </span>
            <div class="fam-ad-header-actions">
                <label class="fam-switch-label">
                    <input type="checkbox" name="fam_ads[__INDEX__][enabled]" value="1" checked>
                    <span class="fam-switch-slider"></span>
                </label>
                <button type="button" class="fam-toggle-btn" title="展开/折叠">
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                </button>
                <button type="button" class="fam-remove-btn" title="删除此广告">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
        </div>
        <div class="fam-ad-body">
            <table class="form-table">
                <tr>
                    <th><label>广告名称</label></th>
                    <td>
                        <input type="text" class="regular-text"
                               name="fam_ads[__INDEX__][name]"
                               placeholder="便于识别的名称（选填）">
                    </td>
                </tr>
                <tr>
                    <th><label>悬浮位置</label></th>
                    <td>
                        <div class="fam-position-grid">
                            <label class="fam-position-option">
                                <input type="radio" name="fam_ads[__INDEX__][position]" value="top-left">
                                <span>↖ 左上角</span>
                            </label>
                            <label class="fam-position-option">
                                <input type="radio" name="fam_ads[__INDEX__][position]" value="top-center">
                                <span>↑ 正上方</span>
                            </label>
                            <label class="fam-position-option">
                                <input type="radio" name="fam_ads[__INDEX__][position]" value="top-right">
                                <span>↗ 右上角</span>
                            </label>
                            <label class="fam-position-option">
                                <input type="radio" name="fam_ads[__INDEX__][position]" value="center-left">
                                <span>← 居中左</span>
                            </label>
                            <label class="fam-position-option">
                                <input type="radio" name="fam_ads[__INDEX__][position]" value="center-right">
                                <span>→ 居中右</span>
                            </label>
                            <label class="fam-position-option">
                                <input type="radio" name="fam_ads[__INDEX__][position]" value="bottom-left">
                                <span>↙ 左下角</span>
                            </label>
                            <label class="fam-position-option">
                                <input type="radio" name="fam_ads[__INDEX__][position]" value="bottom-center">
                                <span>↓ 正下方</span>
                            </label>
                            <label class="fam-position-option active">
                                <input type="radio" name="fam_ads[__INDEX__][position]" value="bottom-right" checked>
                                <span>↘ 右下角</span>
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label>边距设置 (px)</label></th>
                    <td>
                        <div class="fam-margin-grid">
                            <div class="fam-margin-item">
                                <label>上</label>
                                <input type="number" name="fam_ads[__INDEX__][margin_top]" value="20" min="0" max="500">
                            </div>
                            <div class="fam-margin-item">
                                <label>右</label>
                                <input type="number" name="fam_ads[__INDEX__][margin_right]" value="20" min="0" max="500">
                            </div>
                            <div class="fam-margin-item">
                                <label>下</label>
                                <input type="number" name="fam_ads[__INDEX__][margin_bottom]" value="20" min="0" max="500">
                            </div>
                            <div class="fam-margin-item">
                                <label>左</label>
                                <input type="number" name="fam_ads[__INDEX__][margin_left]" value="20" min="0" max="500">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label>层级 (z-index)</label></th>
                    <td>
                        <input type="number" name="fam_ads[__INDEX__][z_index]" value="9999" min="1" max="999999" class="small-text">
                    </td>
                </tr>
                <tr>
                    <th><label>显示关闭按钮</label></th>
                    <td>
                        <label>
                            <input type="checkbox" name="fam_ads[__INDEX__][show_close]" value="1" checked>
                            显示关闭按钮（允许用户手动关闭）
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><label>显示页面</label></th>
                    <td>
                        <select name="fam_ads[__INDEX__][show_on]">
                            <option value="all">所有页面</option>
                            <option value="home">仅首页</option>
                            <option value="post">仅文章页</option>
                            <option value="page">仅独立页面</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label>广告代码</label></th>
                    <td>
                        <textarea name="fam_ads[__INDEX__][code]"
                                  class="fam-code-editor"
                                  rows="10"
                                  placeholder="在此输入 HTML / CSS / JavaScript 代码..."></textarea>
                        <p class="description">支持 HTML、CSS、JavaScript 代码。输入后会自动刷新下方实时预览并展示错误日志。</p>
                    </td>
                </tr>
            </table>
            <div class="fam-preview-section">
                <h4>预览位置示意</h4>
                <div class="fam-preview-box">
                    <div class="fam-preview-dot" data-pos="bottom-right"></div>
                </div>
                <h4>代码实时预览</h4>
                <div class="fam-preview-toolbar">
                    <label class="fam-auto-preview-label">
                        <input type="checkbox" class="fam-auto-preview-toggle" checked>
                        自动刷新预览
                    </label>
                    <button type="button" class="button button-secondary fam-refresh-preview">手动刷新预览</button>
                    <button type="button" class="button fam-clear-errors">清空错误日志</button>
                </div>
                <div class="fam-live-preview" data-preview-root="1">
                    <iframe class="fam-live-preview-frame" sandbox="allow-scripts allow-same-origin"></iframe>
                    <div class="fam-live-preview-empty">在上方输入广告代码后，这里会显示实时效果</div>
                </div>
                <div class="fam-error-log" aria-live="polite">
                    <div class="fam-error-log-title">错误日志</div>
                    <div class="fam-error-log-content">暂无错误</div>
                </div>
            </div>
        </div>
    </div>
</script>
