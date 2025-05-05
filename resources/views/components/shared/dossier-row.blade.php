<tr class="border-b border-light-gray">
    <!-- <td class="px-4 py-2"><input type="checkbox" /></td> -->
    <td class="text-blue px-6 py-4">
        <a href="/dossiers/{{ $dossierId }}"  class="flex items-center gap-4">
            <div class="bg-transparant-blue rounded-full w-10 h-10 flex justify-center items-center">
                {{ substr($firstName, 0, 1) . substr($lastName, 0, 1) }}
            </div>
            <span class="text-black hover:text-blue">{{ $firstName . " " . $lastName }}</span>
        </a>
    </td>
    <td class="text-caption px-6 py-4">
        {{ $status }}
    </td>
    <td class="text-dark-gray text-body-small px-6 py-4">
        {{ $phone }}
    </td>
    <td class="text-dark-gray text-body-small px-6 py-4">
        {{ $email }}
    </td>
</tr>
