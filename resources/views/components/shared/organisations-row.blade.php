<tr class="border-b border-light-gray hover:bg-gray-100 cursor-pointer transition duration-150" onclick="window.location='{{ route('organisations.show', $organization) }}'">
    <td class="text-blue py-4">
        <div class="flex items-center gap-4">
            <div class="bg-transparant-blue rounded-full w-10 h-10 flex justify-center items-center">
                {{ substr($organization->name, 0, 1)}}
            </div>
            <span class="text-black hover:text-blue">{{ $organization->name }}</span>
        </div>
    </td>
    <td class="text-caption px-6 py-4">
        <x-shared.badge :status="$organization->status" />
    </td>
    <td class="text-dark-gray text-body-small px-6 py-4">
        {{ $organization->phone }}
    </td>
    <td class="text-dark-gray text-body-small px-6 py-4">
        {{ $organization->email }}
    </td>
</tr>
