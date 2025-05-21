

    <section class="flex flex-col gap-8">

        <h3 class="text-xl font-semibold">Organisatiegegevens</h3>

        <div class="flex flex-wrap">
            <div class="w-full md:w-1/2 lg:w-1/3 p-4">  
                <div><strong>Naam organisatie:</strong></div>
                <div>{{ $organization->name }}</div>
                <br>
                <div><strong>Telefoon:</strong></div>
                <div>{{ $organization->phone }}</div>
                <br>
                <div><strong>Website:</strong></div>
                <div>{{ $organization->website }}</div>
            </div>
        
            <div class="w-full md:w-1/2 lg:w-1/3 p-4">
                <div><strong>Adres:</strong></div>
                <div>{{ $organization->full_address }}</div>
                <br>
                <div><strong>E-mail:</strong></div>
                <div>{{ $organization->email }}</div>
                <br>
                <div><strong>Ondernemingsnummer:</strong></div>
                <div>{{ $organization->VAT }}</div>
            </div>
        </div>    

    </section>
