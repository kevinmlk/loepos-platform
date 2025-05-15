<a href="{{ $href }}" class="text-decoration-none text-dark flex-grow-1" style="min-width: 300px; max-width: 32%;">
    <div class="card admin-card h-100">
        <div class="position-relative p-3">
            <div class="position-absolute top-0 end-0 m-2 btn btn-sm btn-primary">
                <i class="bi bi-box-arrow-up-right"></i>
            </div>
            <h5 class="card-title">{{ $title }}</h5>
            <p class="card-text">{{ $description }}</p>
            <div class="text-center mt-4">
                <i class="{{ $icon }} display-4 text-primary"></i>
            </div>
        </div>
    </div>
</a>
