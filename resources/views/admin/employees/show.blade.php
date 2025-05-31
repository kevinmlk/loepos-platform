<section class="flex flex-col gap-8">
    <div class="container mt-4">
        <h2 class="mb-4">Details van medewerker</h2>

        {{-- This includes the partial for showing employee details --}}
        @include('admin.partials.showemployee', ['user' => $employee])
         
    </div>
</section>
