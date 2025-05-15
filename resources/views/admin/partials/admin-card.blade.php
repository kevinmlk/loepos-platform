<a href="{{ $href }}" class="text-decoration-none text-dark flex-grow-1" style="min-width: 300px; max-width: 32%;">
    <div class="card admin-card h-100 rounded-xl bg-[var(--color-light-gray)]">
        <div class="position-relative p-4">
            <div class="position-absolute top-0 end-0 m-2 btn btn-sm"
                style="background-color: var(--color-blue); color: var(--color-white); border-radius: 0.5rem;">
                <i class="bi bi-box-arrow-up-right"></i>
            </div>
            <h5 class="card-title font-semibold mb-1" style="font-size: var(--text-header-4);">
                {{ $title }}
            </h5>
            <p class="card-text mb-3" style="font-size: var(--text-body-default);">
                {{ $description }}
            </p>
            <div class="text-center mt-4">
                <i class="{{ $icon }} display-4" style="color: var(--color-blue); font-size: 2.5rem;"></i>
            </div>
        </div>
    </div>
</a>
