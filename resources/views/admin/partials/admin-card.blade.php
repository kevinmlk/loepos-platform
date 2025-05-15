<a href="{{ $href }}" class="text-decoration-none text-dark flex-grow-1" style="min-width: 300px; max-width: 32%;">
    <div class="card admin-card h-80 rounded-xl bg-[var(--color-light-gray)]">
        <div class="position-relative p-4">

            <div class="flex">
                <div>
                    {{-- Header --}}
                    <h5 class="card-title font-semibold mb-1" style="font-size: var(--text-header-4);">
                        {{ $title }}
                    </h5>

                    {{-- Description --}}
                    <p class="card-text mb-3" style="font-size: var(--text-body-default);">
                        {{ $description }}
                    </p>
                </div>

                {{-- Top right Icon --}}
                <div class="position-absolute m-1 ms-4 px-4 py-2 h-14"
                    style="background-color: var(--color-blue); color: var(--color-white); border-radius: 0.5rem;">
                    <i class="bi bi-arrow-up-right" style="font-size: 1.6rem"></i>
                </div>
            </div>

            {{-- Bottom Icon --}}
            <div class="text-center mt-4 pt-5 rounded-xl bg-[var(--color-white)] h-32" style="">
                <i class="{{ $icon }}" style="color: var(--color-blue); font-size: 4rem; "></i>
            </div> 
        </div>
    </div>
</a>
