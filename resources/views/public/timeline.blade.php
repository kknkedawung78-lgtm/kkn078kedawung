@extends('layouts.app')

@section('title', 'Timeline Kegiatan')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Timeline Kegiatan</h1>
        <p class="lead">Alur kegiatan KKN dari awal hingga akhir</p>
    </div>
</section>

<!-- Timeline -->
<section class="py-5 timeline-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="timeline">
                    @forelse($timelines as $timeline)
                    <div class="timeline-item {{ $loop->odd ? 'timeline-left' : 'timeline-right' }}">
                        <div class="timeline-marker">
                            <div class="marker" style="background-color: {{ $timeline['color'] ?? '#3498db' }};">
                                <i class="fas {{ $timeline['icon'] ?? 'fa-circle' }}"></i>
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="card">
                                <div class="card-header" style="background-color: {{ $timeline['color'] ?? '#3498db' }}20; border-left: 4px solid {{ $timeline['color'] ?? '#3498db' }};">
                                    <h5 class="mb-0">{{ $timeline['title'] ?? 'Kegiatan' }}</h5>
                                    <small class="text-muted">
                                        {{ isset($timeline['date']) ? \Carbon\Carbon::parse($timeline['date'])->format('d F Y') : '-' }}
                                    </small>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $timeline['description'] ?? 'Deskripsi tidak tersedia' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="alert alert-info text-center">
                        <p>Belum ada timeline terdaftar</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Back to Home -->
<section class="py-4 bg-light text-center">
    <div class="container">
        <a href="{{ route('home') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>
</section>
@endsection

@section('styles')
<style>
.timeline {
    position: relative;
    padding: 2rem 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    width: 4px;
    height: 100%;
    background: #e0e0e0;
}

.timeline-item {
    position: relative;
    display: grid;
    grid-template-columns: minmax(0, 1fr) 70px minmax(0, 1fr);
    align-items: start;
    margin-bottom: 3rem;
}

.timeline-item:last-child { margin-bottom: 0; }
.timeline-left { text-align: right; }
.timeline-right { text-align: left; }

.timeline-marker {
    position: relative;
    z-index: 10;
    grid-column: 2;
    grid-row: 1;
    justify-self: center;
}

.marker {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #3498db;
    border: 4px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.timeline-left .timeline-content {
    grid-column: 1;
    grid-row: 1;
}

.timeline-right .timeline-content {
    grid-column: 3;
    grid-row: 1;
}

.timeline-content,
.timeline-content .card {
    min-width: 0;
}

.timeline-content h5,
.timeline-content p,
.timeline-content small {
    overflow-wrap: anywhere;
}

@media (max-width: 767.98px) {
    .timeline {
        padding: .75rem 0;
    }

    .timeline::before {
        left: 22px;
        transform: translateX(-50%);
    }

    .timeline-item {
        grid-template-columns: 48px minmax(0, 1fr);
        column-gap: .75rem;
        margin-bottom: 2rem;
        text-align: left;
    }

    .timeline-left,
    .timeline-right {
        text-align: left;
    }

    .timeline-marker {
        grid-column: 1;
        justify-self: start;
    }

    .marker {
        width: 44px;
        height: 44px;
        border-width: 3px;
    }

    .timeline-left .timeline-content,
    .timeline-right .timeline-content {
        grid-column: 2;
        grid-row: 1;
    }

    .timeline-content .card-header,
    .timeline-content .card-body {
        padding: 1rem;
    }
}

@media (max-width: 359.98px) {
    .timeline-item {
        grid-template-columns: 40px minmax(0, 1fr);
        column-gap: .6rem;
    }

    .timeline::before { left: 18px; }
    .marker { width: 38px; height: 38px; font-size: .8rem; }

    .timeline-content .card-header,
    .timeline-content .card-body {
        padding: .8rem;
    }
}
</style>
@endsection
