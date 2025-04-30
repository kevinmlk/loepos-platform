<tr class="border-b hover:bg-gray-50">
    <!-- <td class="px-4 py-2"><input type="checkbox" /></td> -->
    <td class="px-4 py-2 text-blue-700 flex items-center gap-2">
        <div class="bg-blue-100 text-blue-700 rounded-full p-2">
            <svg class="w-4 h-4" fill="currentColor">
                <x-phosphor-file-bold />
            </svg>
        </div>
        <div>{{ $fileName }}</div>
    </td>
    <td>
        {{ $mimeType }}
    </td>
    <td>
        {{ $createdAt }}
    </td>
    <td>
        {{ $updatedAt }}
    </td>
</tr>
