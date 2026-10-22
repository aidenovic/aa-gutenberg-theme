/* AA adaptation modified 2026-10-19; derived from ZW Gutenberg Theme by ZEITWERK. GPL-2.0-or-later; see NOTICE.md and LICENSE.txt. */
(function (wp) {
    'use strict';
    const el = wp.element.createElement;
    const __ = wp.i18n.__;
    wp.blocks.registerBlockType('aagt/contact-form', {
        apiVersion: 3,
        title: __('Kontaktformular', 'aa-gutenberg-theme'),
        category: 'widgets', icon: 'email-alt',
        attributes: { provider: { type: 'string', default: 'none' } },
        supports: { html: false },
        edit: function (props) {
            const provider = props.attributes.provider || 'none';
            return el(wp.element.Fragment, null,
                el(wp.blockEditor.InspectorControls, null,
                    el(wp.components.PanelBody, { title: __('Kontaktformular', 'aa-gutenberg-theme'), initialOpen: true },
                        el(wp.components.SelectControl, {
                            label: __('Formularanbieter', 'aa-gutenberg-theme'), value: provider,
                            options: [
                                { value: 'none', label: __('Kein Formular', 'aa-gutenberg-theme') },
                                { value: 'native', label: __('WordPress-Formular ohne Plugin', 'aa-gutenberg-theme') }
                            ],
                            onChange: function (value) { props.setAttributes({ provider: value }); },
                            __nextHasNoMarginBottom: true, __next40pxDefaultSize: true
                        })
                    )
                ),
                el('div', wp.blockEditor.useBlockProps({ className: 'aagt-form-editor' }),
                    el('h3', null, __('Kontaktformular', 'aa-gutenberg-theme')),
                    el('p', null, provider === 'native'
                        ? __('WordPress-Formular: Name, E-Mail, Nachricht und Zustimmung. Empfänger unter AA Gutenberg Theme → Einrichten festlegen.', 'aa-gutenberg-theme')
                        : __('Auf der Website wird kein Formular ausgegeben.', 'aa-gutenberg-theme')),
                    el('p', null, __('Externe Formularplugins über ihren eigenen Block oder Shortcode einfügen.', 'aa-gutenberg-theme'))
                )
            );
        },
        save: function () { return null; }
    });
})(window.wp);
