<tr class="border-b border-gray">
    <!-- <td class="px-4 py-2"><input type="checkbox" /></td> -->
    <td class="text-blue">
        <a href="{{ asset('storage/' . $filePath )}}" target="_blank" class="flex items-center gap-4 py-3">
            <div class="bg-transparant-blue rounded-full w-10 h-10 flex justify-center items-center">
                <x-phosphor-file-bold class="h-4 text-dark-blue" />
            </div>
            <span class="text-black hover:text-blue">{{ $fileName }}</span>
        </a>
    </td>
    <td>
        {{ \Carbon\Carbon::parse($createdAt)->translatedFormat('d M Y') }}
    </td>
    <td>
        {{ \Carbon\Carbon::parse($updatedAt)->translatedFormat('d M Y') }}
    </td>
</tr>
