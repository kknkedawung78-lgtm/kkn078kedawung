@extends('layouts.app')

@section('title', $member['name'] ?? 'Detail Anggota')

@section('content')
<section class="member-detail-page">
    <div class="container">
        <div class="member-detail-intro text-center">
            <p class="eyebrow">Profil anggota KKN</p>
            <h1>Kenalan lebih dekat!</h1>
            <p>Gerakkan mouse atau sentuh ID card untuk melihat efek kartu yang menggantung.</p>
        </div>

        <div class="member-detail-layout">
        <div class="member-id-column">
        <div class="member-id-scene" data-id-scene>
            <div class="member-id-rig">
                <div class="lanyard" aria-hidden="true">
                    <div class="lanyard-strap"></div>
                    <div class="lanyard-ring"></div>
                    <div class="lanyard-clip"></div>
                </div>

                <div class="member-id-stack">
                    <div class="id-layer id-layer-blue" data-id-layer aria-hidden="true"></div>
                    <div class="id-layer id-layer-pink" data-id-layer aria-hidden="true"></div>

                    <article class="member-id-card" data-id-card>
                        <div class="id-card-header">
                            <span class="id-card-brand">KKN<span>.</span>KARYA</span>
                            <span class="id-card-year">2026</span>
                        </div>

                        <div class="id-photo-wrap">
                            @if($member['photo_url'] ?? false)
                                <img src="{{ $member['photo_url'] }}" alt="Foto {{ $member['name'] ?? 'anggota' }}" decoding="async">
                            @else
                                <div class="id-photo-placeholder"><i class="fa-solid fa-user"></i></div>
                            @endif
                            <span class="id-position">{{ $member['position'] ?? 'Anggota' }}</span>
                        </div>

                        <div class="id-card-body">
                            <p class="id-label">Nama anggota</p>
                            <h2>{{ $member['name'] ?? 'Anggota KKN' }}</h2>

                            <div class="id-info-grid">
                                <div>
                                    <span>NIM</span>
                                    <strong>{{ $member['nim'] ?? '-' }}</strong>
                                </div>
                                <div>
                                    <span>Program Studi</span>
                                    <strong>{{ $member['prodi'] ?? '-' }}</strong>
                                </div>
                            </div>

                            <div class="id-socials">
                                @if($member['social_media']['instagram'] ?? false)
                                    <a href="{{ $member['social_media']['instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                @endif
                                @if($member['social_media']['whatsapp'] ?? false)
                                    <a href="{{ $member['social_media']['whatsapp'] }}" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                @endif
                                @if($member['social_media']['email'] ?? false)
                                    <a href="mailto:{{ $member['social_media']['email'] }}" aria-label="Email"><i class="fas fa-envelope"></i></a>
                                @endif
                            </div>
                        </div>

                        <div class="id-card-footer">
                            <span>Official Member</span>
                            <div class="barcode" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
        </div>

        <aside class="member-info-panel">
            <span class="member-info-number">ID / {{ $member['nim'] ?? 'KKN-2026' }}</span>
            <p class="eyebrow">Data lengkap anggota</p>
            <h2>{{ $member['name'] ?? 'Anggota KKN' }}</h2>
            <span class="member-info-role">{{ $member['position'] ?? 'Anggota' }}</span>

            <div class="member-info-list">
                <div class="member-info-item">
                    <span class="member-info-icon bg-warning"><i class="fa-solid fa-id-badge"></i></span>
                    <div><small>NIM</small><strong>{{ $member['nim'] ?? '-' }}</strong></div>
                </div>
                <div class="member-info-item">
                    <span class="member-info-icon bg-info"><i class="fa-solid fa-graduation-cap"></i></span>
                    <div><small>Program Studi</small><strong>{{ $member['prodi'] ?? '-' }}</strong></div>
                </div>
                <div class="member-info-item">
                    <span class="member-info-icon bg-secondary"><i class="fa-solid fa-people-group"></i></span>
                    <div><small>Jabatan Kelompok</small><strong>{{ $member['position'] ?? '-' }}</strong></div>
                </div>
                <div class="member-info-item">
                    <span class="member-info-icon bg-primary"><i class="fa-solid fa-envelope"></i></span>
                    <div>
                        <small>Email</small>
                        @if($member['social_media']['email'] ?? false)
                            <a href="mailto:{{ $member['social_media']['email'] }}">{{ $member['social_media']['email'] }}</a>
                        @else
                            <strong>-</strong>
                        @endif
                    </div>
                </div>
                <div class="member-info-item">
                    <span class="member-info-icon bg-danger"><i class="fa-brands fa-instagram"></i></span>
                    <div>
                        <small>Instagram</small>
                        @if($member['social_media']['instagram'] ?? false)
                            <a href="{{ $member['social_media']['instagram'] }}" target="_blank" rel="noopener">Buka Instagram <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                        @else
                            <strong>-</strong>
                        @endif
                    </div>
                </div>
                <div class="member-info-item">
                    <span class="member-info-icon bg-success"><i class="fa-brands fa-whatsapp"></i></span>
                    <div>
                        <small>WhatsApp</small>
                        @if($member['social_media']['whatsapp'] ?? false)
                            <strong>{{ $member['social_media']['whatsapp'] }}</strong>
                        @else
                            <strong>-</strong>
                        @endif
                    </div>
                </div>
            </div>

            <p class="member-info-note"><i class="fa-solid fa-star"></i> Bagian dari tim KKN yang bergerak, belajar, dan berkarya bersama masyarakat.</p>
        </aside>
        </div>

        <div class="text-center member-detail-actions">
            <a href="{{ route('profil-kelompok') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Semua Anggota</a>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
(() => {
    const scene = document.querySelector('[data-id-scene]');
    const card = scene?.querySelector('[data-id-card]');
    const rig = scene?.querySelector('.member-id-rig');
    const layers = scene ? [...scene.querySelectorAll('[data-id-layer]')] : [];
    const canTilt = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!scene || !card || !rig || !canTilt) return;

    let frame = null, dragging = false, pointerId = null;
    let x = 0, y = 0, angle = 0, vx = 0, vy = 0, angularVelocity = 0;
    let tiltX = 0, tiltY = 0, lastX = 0, lastY = 0, lastTime = 0, previousFrame = 0;

    rig.classList.add('is-physics-ready');

    const paint = () => {
        rig.style.transform = `translate3d(${x}px, ${y}px, 0) rotate(${angle}deg)`;
        card.style.transform = `rotateX(${-tiltY * 13}deg) rotateY(${tiltX * 17}deg) translateZ(32px)`;
        layers[0]?.style.setProperty('transform', `translate3d(${-14 - tiltX * 18}px, ${9 - tiltY * 12}px, -35px) rotate(${-6 - angle * .08}deg)`);
        layers[1]?.style.setProperty('transform', `translate3d(${15 + tiltX * 15}px, ${8 + tiltY * 10}px, -18px) rotate(${5 + angle * .08}deg)`);
    };

    scene.addEventListener('pointerdown', (event) => {
        if (event.target.closest('a')) return;
        dragging = true;
        pointerId = event.pointerId;
        lastX = event.clientX;
        lastY = event.clientY;
        lastTime = performance.now();
        vx = vy = angularVelocity = 0;
        rig.classList.add('is-dragging');
        scene.setPointerCapture(event.pointerId);
        event.preventDefault();
    });

    scene.addEventListener('pointermove', (event) => {
        const bounds = scene.getBoundingClientRect();
        tiltX = Math.max(-1, Math.min(1, ((event.clientX - bounds.left) / bounds.width - .5) * 2));
        tiltY = Math.max(-1, Math.min(1, ((event.clientY - bounds.top) / bounds.height - .5) * 2));

        if (dragging && event.pointerId === pointerId) {
            const now = performance.now();
            const dt = Math.max(8, now - lastTime) / 1000;
            const dx = event.clientX - lastX;
            const dy = event.clientY - lastY;
            x = Math.max(-210, Math.min(210, x + dx * 1.18));
            y = Math.max(-145, Math.min(145, y + dy * 1.12));
            angle = Math.max(-34, Math.min(34, x * .105 + dx * .42));
            vx = dx / dt * 1.35;
            vy = dy / dt * 1.25;
            angularVelocity = (vx * .055) + (dx / dt * .018);
            lastX = event.clientX;
            lastY = event.clientY;
            lastTime = now;
        }
        paint();
    });

    const animateSpring = (time) => {
        const dt = Math.min(.032, (time - (previousFrame || time)) / 1000);
        previousFrame = time;

        if (!dragging) {
            const spring = 28;
            const damping = 5.2;
            vx += (-spring * x - damping * vx) * dt;
            vy += (-spring * y - damping * vy) * dt;
            angularVelocity += (-35 * angle - 5 * angularVelocity + vx * .045) * dt;
            x += vx * dt;
            y += vy * dt;
            angle += angularVelocity * dt;
            tiltX *= Math.pow(.025, dt);
            tiltY *= Math.pow(.025, dt);
            paint();
        }

        const moving = dragging || Math.abs(x) > .08 || Math.abs(y) > .08 || Math.abs(angle) > .05 || Math.abs(vx) > .3 || Math.abs(vy) > .3;
        frame = moving ? requestAnimationFrame(animateSpring) : null;
    };

    const release = (event) => {
        if (!dragging || (event && event.pointerId !== pointerId)) return;
        dragging = false;
        pointerId = null;
        rig.classList.remove('is-dragging');
        previousFrame = performance.now();
        if (!frame) frame = requestAnimationFrame(animateSpring);
    };

    scene.addEventListener('pointerup', release);
    scene.addEventListener('pointercancel', release);
    scene.addEventListener('pointerleave', (event) => {
        if (event.pointerType === 'mouse' && dragging) release(event);
        if (!dragging) { tiltX = 0; tiltY = 0; paint(); }
    });
})();
</script>
@endsection
