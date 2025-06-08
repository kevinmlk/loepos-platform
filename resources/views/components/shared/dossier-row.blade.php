<tr class="border-b border-light-gray">
    <td class="text-blue py-4">
        <a href="/dossiers/{{ $dossierId }}"  class="flex items-center gap-4">
            <div class="hidden lg:flex bg-transparant-blue rounded-full w-10 h-10 flex justify-center items-center">
                {{ substr($firstName, 0, 1) . substr($lastName, 0, 1) }}
            </div>
            <span class="text-black hover:text-blue">{{ $firstName . " " . $lastName }}</span>
        </a>
    </td>
    <td class="text-caption md:px-6 py-4">
        <x-shared.badge :status="$status" />
    </td>
    <td class="text-dark-gray text-body-small md:px-6 py-4">
        {{ $phone }}
    </td>
    <td class="text-dark-gray text-body-small md:px-6 py-4">
        {{ $email }}
    </td>
</tr>
