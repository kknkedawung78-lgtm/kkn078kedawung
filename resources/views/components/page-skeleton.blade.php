@props(['admin' => false])

<div class="page-skeleton {{ $admin ? 'page-skeleton--admin' : '' }}"
     aria-hidden="true"
     data-page-skeleton>
    <div class="page-skeleton__topbar"></div>
    <div class="page-skeleton__nav">
        <div class="container page-skeleton__nav-inner">
            <span class="skeleton-block skeleton-logo"></span>
            <span class="skeleton-block skeleton-brand"></span>
            <div class="page-skeleton__links">
                <span class="skeleton-block"></span>
                <span class="skeleton-block"></span>
                <span class="skeleton-block"></span>
                <span class="skeleton-block"></span>
                <span class="skeleton-block skeleton-button"></span>
            </div>
        </div>
    </div>

    <div class="page-skeleton__content container">
        <section class="page-skeleton__hero">
            <div class="page-skeleton__copy">
                <span class="skeleton-block skeleton-kicker"></span>
                <span class="skeleton-block skeleton-title"></span>
                <span class="skeleton-block skeleton-title skeleton-title--short"></span>
                <span class="skeleton-block skeleton-text"></span>
                <span class="skeleton-block skeleton-text skeleton-text--short"></span>
            </div>
            <span class="skeleton-block page-skeleton__visual"></span>
        </section>

        <section class="page-skeleton__section">
            <span class="skeleton-block skeleton-heading"></span>
            <div class="page-skeleton__grid">
                @for ($i = 0; $i < 3; $i++)
                    <div class="page-skeleton__card">
                        <span class="skeleton-block skeleton-card-image"></span>
                        <div class="page-skeleton__card-body">
                            <span class="skeleton-block skeleton-card-title"></span>
                            <span class="skeleton-block skeleton-card-line"></span>
                            <span class="skeleton-block skeleton-card-line skeleton-card-line--short"></span>
                        </div>
                    </div>
                @endfor
            </div>
        </section>
    </div>
</div>
