<x-layout>
    {{-- Header --}}
    <x-header>
        Ondersteuning
        <x-slot:subText>
            Heeft u hulp nodig? Contacteer onze IT-afdeling.
        </x-slot:subText>
    </x-header>

    {{-- Success message --}}
    @if(session('success'))
        <div class="bg-green-50 border-2 border-green-500 text-green-800 p-4 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-600"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Main content grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Contact form section (2/3 width on desktop) --}}
        <section class="lg:col-span-2 border-2 border-light-gray rounded-lg">
            <div class="p-6">
                {{-- Introduction --}}
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-2">Contact opnemen</h2>
                    <p class="text-dark-gray">Gebruik onderstaand formulier om contact op te nemen met onze IT-afdeling. We streven ernaar om binnen 24 uur te reageren op uw vraag.</p>
                </div>

                {{-- Contact form --}}
                <form method="POST" action="{{ route('support.send') }}">
                    @csrf

                    <div class="space-y-4">
                        {{-- Organization info box --}}
                        <div class="bg-gradient-to-br from-blue-50 to-light-gray p-6 rounded-xl border border-blue-100">
                            <div class="flex items-center mb-4">
                                <div class="bg-blue text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <h3 class="font-semibold text-dark-gray">Uw gegevens</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Organization --}}
                                <div class="relative">
                                    <x-form.label for="organization" class="text-sm font-medium text-gray mb-1">Organisatie</x-form.label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <i class="fas fa-building text-gray text-sm"></i>
                                        </div>
                                        <input 
                                            type="text" 
                                            id="organization" 
                                            name="organization" 
                                            value="{{ auth()->user()->organization->name ?? '' }}" 
                                            readonly 
                                            class="w-full pl-10 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-dark-gray cursor-not-allowed focus:outline-none"
                                        />
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="relative">
                                    <x-form.label for="email" class="text-sm font-medium text-gray mb-1">E-mailadres</x-form.label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <i class="fas fa-envelope text-gray text-sm"></i>
                                        </div>
                                        <input 
                                            type="email" 
                                            id="email" 
                                            name="email" 
                                            value="{{ auth()->user()->email }}" 
                                            readonly 
                                            class="w-full pl-10 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-dark-gray cursor-not-allowed focus:outline-none"
                                        />
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3 flex items-center text-xs text-gray">
                                <i class="fas fa-lock mr-2"></i>
                                <span>Deze gegevens kunnen niet worden gewijzigd</span>
                            </div>
                        </div>

                        {{-- Message section --}}
                        <div class="border-2 border-light-gray rounded-xl p-6 bg-white">
                            <div class="flex items-center mb-4">
                                <div class="bg-blue text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">
                                    <i class="fas fa-message text-sm"></i>
                                </div>
                                <h3 class="font-semibold text-dark-gray">Uw bericht</h3>
                            </div>
                            
                            {{-- Subject (optional) --}}
                            <div class="mb-5">
                                <x-form.label for="subject" class="text-sm font-medium text-gray mb-2">Onderwerp</x-form.label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <i class="fas fa-tag text-gray text-sm"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        id="subject" 
                                        name="subject" 
                                        placeholder="Bijv: Probleem met uploaden documenten"
                                        class="w-full pl-10 pr-3 py-2 border border-light-gray rounded-lg focus:outline-none focus:border-blue focus:ring-1 focus:ring-blue transition-all"
                                    />
                                </div>
                            </div>

                            {{-- Message --}}
                            <div>
                                <x-form.label for="message" class="text-sm font-medium text-gray mb-2">
                                    Bericht
                                    <span class="text-red-500 ml-1">*</span>
                                </x-form.label>
                                <div class="relative">
                                    <textarea 
                                        id="message" 
                                        name="message" 
                                        rows="2" 
                                        required 
                                        class="w-full px-4 py-3 border border-light-gray rounded-lg focus:outline-none focus:border-blue focus:ring-1 focus:ring-blue resize-none transition-all"
                                        placeholder="Beschrijf uw vraag of probleem zo gedetailleerd mogelijk..."
                                    ></textarea>
                                    <div class="absolute bottom-3 right-3 text-xs text-gray">
                                        <i class="fas fa-pencil mr-1"></i>
                                    </div>
                                </div>
                                <p class="text-xs text-gray mt-2 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Verplicht veld
                                </p>
                            </div>
                        </div>

                        {{-- Action buttons --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                            <div class="text-sm text-gray">
                                <i class="fas fa-info-circle mr-1"></i>
                                <span class="block sm:inline">Antwoord wordt gestuurd naar:</span>
                                <span class="block sm:inline font-medium">{{ auth()->user()->email }}</span>
                            </div>
                            
                            <button type="submit" class="bg-blue text-white px-6 py-2 rounded-lg hover:bg-dark-blue transition-colors font-medium w-full sm:w-auto">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Bericht versturen
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        {{-- FAQ section (1/3 width on desktop) --}}
        <section class="lg:col-span-1 border-2 border-light-gray rounded-lg h-fit">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <i class="fas fa-question-circle mr-2 text-blue"></i>
                    Veelgestelde vragen
                </h2>
                
                <div class="space-y-4">
                    <div class="border-b border-light-gray pb-4">
                        <h3 class="font-semibold text-blue mb-2 text-sm">Hoe upload ik documenten?</h3>
                        <p class="text-dark-gray text-sm">Ga naar de Upload pagina via het hoofdmenu. U kunt documenten slepen en neerzetten of klikken om bestanden te selecteren.</p>
                    </div>
                    
                    <div class="border-b border-light-gray pb-4">
                        <h3 class="font-semibold text-blue mb-2 text-sm">Wat is de maximale bestandsgrootte?</h3>
                        <p class="text-dark-gray text-sm">De maximale bestandsgrootte is 50 MB per document. Ondersteunde formaten zijn PDF, JPG, en PNG.</p>
                    </div>
                    
                    <div class="border-b border-light-gray pb-4">
                        <h3 class="font-semibold text-blue mb-2 text-sm">Hoe snel krijg ik antwoord?</h3>
                        <p class="text-dark-gray text-sm">We streven ernaar om binnen 24 uur te reageren op werkdagen. Voor urgente zaken kunt u telefonisch contact opnemen.</p>
                    </div>

                    <div class="pb-4">
                        <h3 class="font-semibold text-blue mb-2 text-sm">Kan ik meerdere bestanden tegelijk uploaden?</h3>
                        <p class="text-dark-gray text-sm">Ja, u kunt meerdere bestanden selecteren of slepen naar het uploadvenster om ze tegelijk te uploaden.</p>
                    </div>
                </div>

                {{-- Contact info --}}
                <div class="mt-6 pt-4 border-t border-light-gray">
                    <h3 class="font-semibold text-sm mb-2">Direct contact</h3>
                    <p class="text-dark-gray text-sm">
                        <i class="fas fa-phone mr-2 text-blue"></i>
                        Voor urgente zaken: 
                        <a href="tel:+32478379695" class="text-blue hover:underline">+32 478 37 96 95</a>
                    </p>
                </div>
            </div>
        </section>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endpush
</x-layout>
