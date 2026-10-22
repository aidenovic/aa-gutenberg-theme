/* AA adaptation modified 2026-10-19; derived from ZW Gutenberg Theme by ZEITWERK. GPL-2.0-or-later; see NOTICE.md and LICENSE.txt. */
(function (wp, config) {
    'use strict';
    if (!wp || !wp.data || !config) { return; }

    // Preview unsaved Global Styles in both the Site Editor and its editor canvas.
    // The server uses the same token roles after saving; no frontend script is required.
    function cssValue(value, fallback) {
        if (typeof value !== 'string' || !value || /[{};<>]/.test(value)) { return fallback; }
        return value.replace(/var:preset\|([a-z0-9-]+)\|([a-z0-9-]+)/gi, 'var(--wp--preset--$1--$2)');
    }

    function typography(source, element) {
        return source && source.elements && source.elements[element] ? source.elements[element].typography || {} : {};
    }

    function blockTypography(source, name) {
        return source && source.blocks && source.blocks[name] ? source.blocks[name].typography || {} : {};
    }

    function buttonDesignVariables(defaults, user) {
        function property(group, key) {
            var value;
            [defaults.elements && defaults.elements.button, defaults.blocks && defaults.blocks['core/button'], user.elements && user.elements.button, user.blocks && user.blocks['core/button']].forEach(function (style) {
                if (style && style[group] && style[group][key] !== undefined) {
                    var next = style[group][key];
                    value = next && typeof next === 'object' ? Object.assign({}, typeof value === 'object' ? value : {}, next) : next;
                }
            });
            return value;
        }
        var radius = property('border', 'radius');
        if (radius && typeof radius === 'object') {
            radius = ['topLeft', 'topRight', 'bottomRight', 'bottomLeft'].map(function (corner) { return cssValue(radius[corner], '0'); }).join(' ');
        }
        var vars = {
            '--aa-button-background': cssValue(property('color', 'background'), 'var(--aa-primary)'),
            '--aa-button-color': cssValue(property('color', 'text'), 'var(--aa-white)'),
            '--aa-button-radius': cssValue(radius, 'var(--wp--custom--aa--button-radius, 0)'),
            '--aa-button-weight': cssValue(property('typography', 'fontWeight'), '400'),
            '--aa-button-line-height': cssValue(property('typography', 'lineHeight'), '1.2'),
            '--aa-button-letter-spacing': cssValue(property('typography', 'letterSpacing'), 'var(--aa-button-tracking)'),
            '--aa-button-text-transform': cssValue(property('typography', 'textTransform'), 'uppercase')
        };
        var padding = property('spacing', 'padding');
        var fallback = { top: '15px', right: '25px', bottom: '15px', left: '25px' };
        Object.keys(fallback).forEach(function (side) {
            vars['--aa-button-padding-' + side] = cssValue(typeof padding === 'string' ? padding : padding && padding[side], fallback[side]);
        });
        return vars;
    }

    function createCSS(user) {
        var defaults = config.defaults || {};
        var theme = config.theme || {};
        user = user || {};
        var body = Object.assign({}, defaults.typography, user.typography);
        var heading = Object.assign({}, typography(defaults, 'heading'), blockTypography(defaults, 'core/heading'), typography(user, 'heading'), blockTypography(user, 'core/heading'));
        var userHeading = Object.assign({}, typography(user, 'heading'), blockTypography(user, 'core/heading'));
        var h1 = Object.assign({}, typography(theme, 'h1'), typography(user, 'h1'));
        var h2 = Object.assign({}, typography(theme, 'h2'), typography(user, 'h2'));
        var button = Object.assign({}, typography(defaults, 'button'), blockTypography(defaults, 'core/button'), typography(user, 'button'), blockTypography(user, 'core/button'));
        var variables = {
            '--aa-font-family': cssValue(body.fontFamily, 'var(--wp--preset--font-family--system)'),
            '--aa-heading-font-family': cssValue(h2.fontFamily || heading.fontFamily || body.fontFamily, 'var(--aa-font-family)'),
            '--aa-display-font-family': cssValue(h1.fontFamily || heading.fontFamily, 'var(--aa-heading-font-family)'),
            '--aa-button-font-family': cssValue(button.fontFamily || body.fontFamily, 'var(--aa-font-family)'),
            '--aa-button-size': cssValue(button.fontSize, 'var(--aa-small-size)'),
            '--aa-navigation-size': cssValue(blockTypography(user, 'core/navigation').fontSize || blockTypography(defaults, 'core/navigation').fontSize, 'var(--aa-small-size)'),
            '--aa-body-size': cssValue(body.fontSize, 'var(--wp--preset--font-size--body)'),
            '--aa-heading-size': cssValue(h2.fontSize || userHeading.fontSize, 'var(--wp--preset--font-size--heading)'),
            '--aa-display-size': cssValue(h1.fontSize || userHeading.fontSize, 'var(--wp--preset--font-size--display)'),
            '--aa-feature-size': cssValue(h2.fontSize || userHeading.fontSize, 'var(--wp--preset--font-size--feature)'),
            '--aa-large-heading-size': cssValue(h2.fontSize || userHeading.fontSize, 'var(--wp--preset--font-size--display)'),
            '--aa-body-line-height': cssValue(body.lineHeight, '1.3158'),
            '--aa-heading-line-height': cssValue(heading.lineHeight, '1.14286'),
            '--aa-display-line-height': cssValue(h1.lineHeight || userHeading.lineHeight, '1'),
            '--aa-heading-weight': cssValue(heading.fontWeight, '700')
        };
        // Match PHP: ignore generic core button colors, honor project/user styles.
        Object.assign(variables, buttonDesignVariables(theme, user));
        return ':root:root,.editor-styles-wrapper.editor-styles-wrapper{' + Object.keys(variables).map(function (name) { return name + ':' + variables[name] + ';'; }).join('') + '}';
    }

    function preview() {
        var documents = [document];
        document.querySelectorAll('iframe[name="editor-canvas"]').forEach(function (frame) {
            try { if (frame.contentDocument) { documents.push(frame.contentDocument); } } catch (error) { /* Skip an unrelated cross-origin frame. */ }
        });
        // Core's editor iframe may omit lang. CSS hyphenation needs the content
        // language even before the Global Styles record has finished loading.
        documents.forEach(function (target) {
            if (target !== document && target.documentElement && !target.documentElement.lang && config.language) {
                target.documentElement.lang = config.language;
            }
        });
        var core = wp.data.select('core');
        if (!core) { return; }
        var id = config.id;
        if (!id && typeof core.__experimentalGetCurrentGlobalStylesId === 'function') { id = core.__experimentalGetCurrentGlobalStylesId(); }
        if (!id) { return; }
        var record = core.getEditedEntityRecord('root', 'globalStyles', id);
        if (!record) { return; }
        var css = createCSS(record.styles);
        documents.forEach(function (target) {
            if (!target.head) { return; }
            var style = target.getElementById('aagt-live-design');
            if (!style) {
                style = target.createElement('style');
                style.id = 'aagt-live-design';
                target.head.appendChild(style);
            }
            if (style.textContent !== css) { style.textContent = css; }
        });
    }

    var scheduled = false;
    function schedule() {
        if (scheduled) { return; }
        scheduled = true;
        requestAnimationFrame(function () { scheduled = false; preview(); });
    }
    wp.data.subscribe(schedule);
    new MutationObserver(schedule).observe(document.documentElement, {childList: true, subtree: true});
    document.addEventListener('load', schedule, true);
    schedule();
})(window.wp, window.aagtDesign);
