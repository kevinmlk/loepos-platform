
    <section class="flex flex-col gap-8">

        <div class="container mt-4">
            <h2 class="mb-4">Clienten in mijn organisatie</h2>

             <div class="flex flex-wrap gap-4">
                @foreach ($clients as $client)
                    <div class="col-md-3 mb-4"> 
                        @include('admin.partials.client-card', ['client' => $client])
                    </div>    
                @endforeach
            </div>
        </div>
    </section>
