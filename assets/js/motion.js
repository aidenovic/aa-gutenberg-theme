/* AA adaptation modified 2026-10-19; derived from ZW Gutenberg Theme by ZEITWERK. GPL-2.0-or-later; see NOTICE.md and LICENSE.txt. */
/* Optional fade-up for individual content blocks. No hidden CSS, external services or persistent storage. */
(function () {
    'use strict';
    const seen = new WeakSet();
    function hasFocus(node) {
        const active = document.activeElement;
        return active && active !== document.body && active !== document.documentElement &&
            (node.contains(active) || active.contains?.(node));
    }
    function init() {
        if (typeof window.matchMedia !== 'function' || !window.IntersectionObserver) return;
        const preference = window.matchMedia('(prefers-reduced-motion: reduce)');
        if (preference.matches) return;
        const candidates = Array.from(document.querySelectorAll(
            '.aagt-section :is(.wp-block-heading,.wp-block-paragraph,.wp-block-image)'
        )).filter(node => typeof node.animate === 'function' && !seen.has(node) &&
            !hasFocus(node) && node.getBoundingClientRect().top >= window.innerHeight);
        const nodes = candidates.filter(node => !candidates.some(parent => parent !== node && parent.contains(node)));
        if (!nodes.length) return;
        const effects = new Map();
        const reveal = node => {
            const effect = effects.get(node);
            if (!effect) return;
            effect.animation.cancel();
            effects.delete(node);
            seen.add(node);
            observer.unobserve(node);
        };
        const observer = new window.IntersectionObserver(entries => {
            entries.forEach(entry => {
                const effect = effects.get(entry.target);
                if (!entry.isIntersecting || !effect || effect.started) return;
                if (preference.matches || hasFocus(entry.target) || entry.target.getBoundingClientRect().top < 0) {
                    reveal(entry.target);
                    return;
                }
                effect.started = true;
                seen.add(entry.target);
                observer.unobserve(entry.target);
                effect.animation.play();
            });
        }, { threshold: 0.1, rootMargin: '0px' });
        nodes.forEach(node => {
            // Prepare below the screen, so visible content never flashes back to transparent.
            const opacity = window.getComputedStyle?.(node).opacity || 1;
            const animation = node.animate([{ opacity: 0, transform: 'translateY(20px)' }, { opacity, transform: 'none' }], {
                duration: 450, easing: 'ease-out'
            });
            animation.pause();
            animation.currentTime = 0;
            effects.set(node, { animation, started: false });
            animation.finished.then(() => reveal(node), () => effects.delete(node));
            observer.observe(node);
        });
        const focus = event => {
            effects.forEach((effect, node) => {
                if (node.contains(event.target) || event.target.contains?.(node)) reveal(node);
            });
        };
        document.addEventListener('focusin', focus);
        const showAll = () => {
            observer.disconnect();
            effects.forEach(effect => effect.animation.cancel());
            effects.clear();
        };
        const reduce = () => { if (preference.matches) showAll(); };
        if (preference.addEventListener) preference.addEventListener('change', reduce);
        else preference.addListener?.(reduce);
        window.addEventListener?.('pagehide', () => {
            showAll();
            document.removeEventListener?.('focusin', focus);
            if (preference.removeEventListener) preference.removeEventListener('change', reduce);
            else preference.removeListener?.(reduce);
        }, { once: true });
    }
    window.addEventListener?.('pageshow', event => { if (event.persisted) init(); });
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
    else init();
})();
