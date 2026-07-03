// Interaksi halaman spesifik dimuat oleh masing-masing Blade view.
const skeleton = document.querySelector('[data-page-skeleton]');

if (skeleton) {
    const showSkeleton = () => {
        document.body.classList.add('is-page-loading');
        skeleton.setAttribute('aria-hidden', 'false');
    };
    const hideSkeleton = () => {
        document.body.classList.remove('is-page-loading');
        skeleton.setAttribute('aria-hidden', 'true');
    };

    if (document.readyState === 'complete') requestAnimationFrame(hideSkeleton);
    else window.addEventListener('load', hideSkeleton, { once: true });

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0) return;
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        if (link.target === '_blank' || link.hasAttribute('download')) return;

        const destination = new URL(link.href, window.location.href);
        const samePageAnchor = destination.pathname === window.location.pathname
            && destination.search === window.location.search
            && destination.hash;
        if (destination.origin === window.location.origin && !samePageAnchor) showSkeleton();
    });

    document.addEventListener('submit', (event) => {
        if (!event.defaultPrevented) showSkeleton();
    });
    window.addEventListener('pageshow', hideSkeleton);
}
