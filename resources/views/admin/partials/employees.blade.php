<section class="flex flex-col gap-8">
    <div class="container mt-4">
        <h2 class="mb-4">Medewerkers in mijn organisatie</h2>

        <!-- Scrollable container -->
        <div class="h-[600px] overflow-auto p-4 rounded shadow">
            <div class="flex flex-wrap gap-4">
                @foreach ($users as $user)
                    <div class="col-md-3 mb-4">
                        <a href="{{ route('admin.employees.show', $user->id) }}" class="block">
                            @include('admin.partials.employee-card', ['user' => $user])
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
