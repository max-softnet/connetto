@props(['icon' => null, 'title', 'subtitle' => null])

<div class="d-flex flex-wrap justify-content-between align-items-start align-items-md-center mb-4 gap-2">
    <div>
        <h1 class="h3 mb-0 d-flex align-items-center gap-2">
            @if ($icon)
                <i class="bi {{ $icon }}"></i>
            @endif
            {{ $title }}
        </h1>
        @if ($subtitle)
            <p class="text-muted small fst-italic mb-0">{{ $subtitle }}</p>
        @endif
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}"><i class="bi bi-house"></i></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
        </ol>
    </nav>
</div>
