<tr class="border-b border-light-gray hover:bg-gray-100 cursor-pointer transition duration-150">
    <td class="text-blue py-4">
        <div class="flex items-center gap-4">
            <div class="bg-transparant-blue rounded-full w-10 h-10 flex justify-center items-center">
               {{ substr($firstName, 0, 1) . substr($lastName, 0, 1) }}
            </div>
            <span class="text-black hover:text-blue">{{ $firstName . " " . $lastName }}</span>
        </div>
    </td>
   
    <td class="text-dark-gray text-body-small px-6 py-4">
        {{ $client->phone }}
    </td>
    <td class="text-dark-gray text-body-small px-6 py-4">
        {{ $client->email }}
    </td>
</tr>
    