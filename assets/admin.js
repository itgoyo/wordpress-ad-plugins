/**
 * Floating Ad Manager - Admin JavaScript
 */
(function($) {
    'use strict';

    var FAM = {
        init: function() {
            this.bindEvents();
            this.showEmptyState();
            this.bindPreviewMessage();
            this.initExistingBlocks();
        },

        bindEvents: function() {
            // 添加新广告
            $('#fam-add-ad').on('click', this.addAd.bind(this));

            // 删除广告
            $(document).on('click', '.fam-remove-btn', this.removeAd.bind(this));

            // 展开/折叠
            $(document).on('click', '.fam-toggle-btn', this.toggleAd);
            $(document).on('click', '.fam-ad-header', function(e) {
                if (!$(e.target).closest('.fam-switch-label, .fam-toggle-btn, .fam-remove-btn').length) {
                    $(this).find('.fam-toggle-btn').trigger('click');
                }
            });

            // 位置选择器
            $(document).on('change', '.fam-position-option input[type="radio"]', this.onPositionChange);

            // 表单提交前重新排序索引
            $('#fam-form').on('submit', this.reindexAds);

            // Tab键支持
            $(document).on('keydown', '.fam-code-editor', this.handleTab);

            // 输入代码时实时预览
            $(document).on('input', '.fam-code-editor', this.onCodeInput.bind(this));

            // 自动刷新开关
            $(document).on('change', '.fam-auto-preview-toggle', this.onAutoPreviewToggle.bind(this));

            // 手动刷新预览
            $(document).on('click', '.fam-refresh-preview', this.onRefreshPreview.bind(this));

            // 清空错误日志
            $(document).on('click', '.fam-clear-errors', this.onClearErrors.bind(this));
        },

        initExistingBlocks: function() {
            $('.fam-ad-block').each(function() {
                FAM.ensurePreviewId($(this));
                FAM.syncPositionPreview($(this));
                FAM.updatePreview($(this));
            });
        },

        bindPreviewMessage: function() {
            window.addEventListener('message', function(event) {
                var data = event.data;
                if (!data || data.source !== 'fam-preview' || !data.previewId) {
                    return;
                }

                var block = $('.fam-ad-block[data-preview-id="' + data.previewId + '"]');
                if (!block.length) {
                    return;
                }

                if (data.type === 'clear-errors') {
                    FAM.clearErrors(block);
                    return;
                }

                if (data.type === 'runtime-error') {
                    FAM.appendError(block, data.message || '未知错误', data.extra || '');
                }
            });
        },

        ensurePreviewId: function(block) {
            if (!block.attr('data-preview-id')) {
                block.attr('data-preview-id', 'fam-prev-' + Date.now() + '-' + Math.floor(Math.random() * 100000));
            }
            return block.attr('data-preview-id');
        },

        onCodeInput: function(e) {
            var block = $(e.target).closest('.fam-ad-block');
            var autoPreviewEnabled = block.find('.fam-auto-preview-toggle').is(':checked');

            if (!autoPreviewEnabled) {
                return;
            }

            clearTimeout(block.data('previewTimer'));
            var timer = setTimeout(function() {
                FAM.updatePreview(block);
            }, 280);
            block.data('previewTimer', timer);
        },

        onAutoPreviewToggle: function(e) {
            var block = $(e.target).closest('.fam-ad-block');
            if ($(e.target).is(':checked')) {
                this.updatePreview(block);
            }
        },

        addAd: function() {
            var template = $('#fam-ad-template').html();
            var index = $('.fam-ad-block').length;
            var html = template
                .replace(/__INDEX__/g, index)
                .replace(/__NUM__/g, index + 1);

            // 移除空状态
            $('.fam-empty-state').remove();

            $('#fam-ads-container').append(html);

            var newBlock = $('#fam-ads-container .fam-ad-block:last');
            this.ensurePreviewId(newBlock);
            this.syncPositionPreview(newBlock);
            this.updatePreview(newBlock);

            // 滚动到新广告
            $('html, body').animate({
                scrollTop: newBlock.offset().top - 50
            }, 300);
        },

        removeAd: function(e) {
            e.stopPropagation();
            var block = $(e.target).closest('.fam-ad-block');
            var name = block.find('input[name$="[name]"]').val();

            if (confirm('确定要删除' + (name ? ' "' + name + '" ' : '此') + '广告吗？')) {
                block.slideUp(200, function() {
                    $(this).remove();
                    FAM.reindexBlocks();
                    FAM.showEmptyState();
                });
            }
        },

        toggleAd: function(e) {
            e.stopPropagation();
            var block = $(this).closest('.fam-ad-block');
            block.toggleClass('collapsed');
        },

        onPositionChange: function() {
            var block = $(this).closest('.fam-ad-block');
            FAM.syncPositionPreview(block);
        },

        onRefreshPreview: function(e) {
            e.preventDefault();
            var block = $(e.target).closest('.fam-ad-block');
            this.updatePreview(block);
        },

        onClearErrors: function(e) {
            e.preventDefault();
            var block = $(e.target).closest('.fam-ad-block');
            this.clearErrors(block);
        },

        syncPositionPreview: function(block) {
            var selected = block.find('.fam-position-option input[type="radio"]:checked');
            var pos = selected.val() || 'bottom-right';

            block.find('.fam-position-option').removeClass('active');
            selected.closest('.fam-position-option').addClass('active');
            block.find('.fam-preview-dot').attr('data-pos', pos);
        },

        reindexAds: function() {
            $('.fam-ad-block').each(function(i) {
                $(this).attr('data-index', i);
                $(this).find('[name]').each(function() {
                    var name = $(this).attr('name');
                    $(this).attr('name', name.replace(/fam_ads\[\d+\]/, 'fam_ads[' + i + ']'));
                });
            });
        },

        reindexBlocks: function() {
            $('.fam-ad-block').each(function(i) {
                $(this).attr('data-index', i);
                $(this).find('.fam-ad-title').contents().filter(function() {
                    return this.nodeType === 3;
                }).each(function() {
                    this.textContent = this.textContent.replace(/广告 #\d+/, '广告 #' + (i + 1));
                });
                $(this).find('[name]').each(function() {
                    var name = $(this).attr('name');
                    $(this).attr('name', name.replace(/fam_ads\[\d+\]/, 'fam_ads[' + i + ']'));
                });

                FAM.updatePreview($(this));
            });
        },

        showEmptyState: function() {
            if ($('.fam-ad-block').length === 0 && $('.fam-empty-state').length === 0) {
                var html = '<div class="fam-empty-state">' +
                    '<span class="dashicons dashicons-megaphone"></span>' +
                    '<p>还没有添加任何广告，点击下方按钮添加第一个悬浮广告</p>' +
                    '</div>';
                $('#fam-ads-container').append(html);
            }
        },

        updatePreview: function(block) {
            var codeEditor = block.find('.fam-code-editor');
            var frame = block.find('.fam-live-preview-frame');
            var previewWrap = block.find('.fam-live-preview');
            var code = codeEditor.val() || '';
            var previewId = this.ensurePreviewId(block);

            if (!frame.length || !previewWrap.length) {
                return;
            }

            this.clearErrors(block);

            if (!code.trim()) {
                previewWrap.removeClass('has-content');
                this.writePreviewFrame(frame.get(0), this.buildPreviewHTML('<div style="padding:16px;color:#8c8f94;font-size:13px;">暂无预览内容</div>', previewId));
                return;
            }

            previewWrap.addClass('has-content');
            this.writePreviewFrame(frame.get(0), this.buildPreviewHTML(code, previewId));
        },

        writePreviewFrame: function(frameEl, html) {
            if (!frameEl) {
                return;
            }

            try {
                frameEl.srcdoc = html;
            } catch (e) {
                // Ignore and fallback to document.write below.
            }

            try {
                var doc = frameEl.contentDocument || (frameEl.contentWindow && frameEl.contentWindow.document);
                if (!doc) {
                    return;
                }
                doc.open();
                doc.write(html);
                doc.close();
            } catch (e) {
                // Some environments may block direct writes when srcdoc already succeeds.
            }
        },

        buildPreviewHTML: function(code, previewId) {
            var injectedScript = [
                '<script>',
                '(function(){',
                '  var send = function(type, message, extra) {',
                '    try {',
                '      window.parent.postMessage({source:"fam-preview", type:type, previewId:"' + previewId + '", message:message || "", extra:extra || ""}, "*");',
                '    } catch (e) {}',
                '  };',
                '  window.onerror = function(message, source, lineno, colno, error) {',
                '    var pos = source ? (" @" + source + ":" + lineno + ":" + colno) : "";',
                '    send("runtime-error", String(message || "Script Error"), pos);',
                '  };',
                '  window.onunhandledrejection = function(evt) {',
                '    var reason = evt && evt.reason ? String(evt.reason) : "Unhandled Promise Rejection";',
                '    send("runtime-error", reason, "");',
                '  };',
                '  var oldError = console.error;',
                '  console.error = function(){',
                '    var msg = Array.prototype.slice.call(arguments).map(function(v){',
                '      return typeof v === "string" ? v : JSON.stringify(v);',
                '    }).join(" ");',
                '    send("runtime-error", msg || "console.error", "");',
                '    if (oldError) { oldError.apply(console, arguments); }',
                '  };',
                '  send("clear-errors", "", "");',
                '})();',
                '</script>'
            ].join('');

            return '<!doctype html><html><head><meta charset="utf-8"><style>body{margin:0;padding:10px;box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,sans-serif;}*{box-sizing:border-box;}</style></head><body>' + code + injectedScript + '</body></html>';
        },

        clearErrors: function(block) {
            var log = block.find('.fam-error-log');
            var content = block.find('.fam-error-log-content');
            log.removeClass('has-error');
            content.html('暂无错误');
        },

        appendError: function(block, message, extra) {
            var log = block.find('.fam-error-log');
            var content = block.find('.fam-error-log-content');
            var time = new Date().toLocaleTimeString();
            var line = '<div class="fam-error-item">[' + this.escapeHtml(time) + '] ' + this.escapeHtml(message);

            if (extra) {
                line += '<br><span>' + this.escapeHtml(extra) + '</span>';
            }
            line += '</div>';

            if (content.text().trim() === '暂无错误') {
                content.html('');
            }

            log.addClass('has-error');
            content.prepend(line);
        },

        escapeHtml: function(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        },

        handleTab: function(e) {
            if (e.key === 'Tab') {
                e.preventDefault();
                var start = this.selectionStart;
                var end = this.selectionEnd;
                var value = $(this).val();
                $(this).val(value.substring(0, start) + '    ' + value.substring(end));
                this.selectionStart = this.selectionEnd = start + 4;
            }
        }
    };

    $(document).ready(function() {
        FAM.init();
    });

})(jQuery);
