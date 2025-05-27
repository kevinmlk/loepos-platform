
    <section class="flex flex-col gap-8">

        <div class="container mt-4">
            <h2 class="mb-4">Medewerkers in mijn organisatie</h2>

            <div class="row">
                @foreach ($users as $user)
                    <div class="col-md-3 mb-4">
                        @include('admin.partials.employee-card', ['user' => $user])
                    </div>
                @endforeach
            </div>
        </div>
    </section>
