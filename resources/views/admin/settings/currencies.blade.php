@extends('layouts.admin.app')

@section('title', 'Currencies')

@section('content')
{{-- Page Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Currencies</h1>
            <p class="text-gray-500 text-sm">Manage platform currencies and conversion rates</p>
        </div>

        <button onclick="openAddModal()"
   class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow">
    Add Currency
</button>
    </div>

    {{-- Currency Table --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">

                <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b">
                    <tr>
                        <th class="px-6 py-4">Code</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Symbol</th>
                        <th class="px-6 py-4">Country</th>
                        <th class="px-6 py-4">CF Code</th>
                        <th class="px-6 py-4">Conversion Rate</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @forelse($currencies as $currency)

                    <tr class="hover:bg-gray-50">

                        <td class="px-6 py-4 font-semibold">
                            {{ $currency->code }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $currency->name }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $currency->symbol }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $currency->country ?? '-' }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $currency->cf_code }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $currency->conversion_rate }}
                        </td>

                        <td class="px-6 py-4">
                            @if($currency->is_active)
                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                    Active
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right space-x-2">

                           <button
    onclick='openEditModal(@json($currency))'
    class="text-blue-600 hover:underline">
    Edit
</button>

<button
    onclick="openDeleteModal({{ $currency->id }})"
    class="text-red-600 hover:underline">
    Delete
</button>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            No currencies found
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>
        </div>
    </div>

{{-- Pagination --}}
        <div class="p-6 border-t">
@include('components.pager', ['paginator' => $currencies, '__pager' => $currencies])  
        </div>
{{-- ADD MODAL --}}
<div id="addModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">

<div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">

<h2 class="text-lg font-semibold mb-4">Add Currency</h2>

<form method="POST" action="{{ route('admin.settings.currencies.store') }}">
@csrf

<div class="grid grid-cols-2 gap-4">

<input name="code" placeholder="Code (USD)" class="border p-2 rounded">

<input name="name" placeholder="Name" class="border p-2 rounded">

<input name="symbol" placeholder="Symbol" class="border p-2 rounded">

<input name="country" placeholder="Country" class="border p-2 rounded">

<input name="cf_code" placeholder="CF Code" class="border p-2 rounded">

<input name="conversion_rate" placeholder="Conversion Rate" class="border p-2 rounded">

</div>

<label class="flex items-center gap-2 mt-4">
<input type="checkbox" name="is_active" value="1">
Active
</label>

<div class="flex justify-end gap-3 mt-6">

<button type="button" onclick="closeAddModal()"
class="px-4 py-2 bg-gray-200 rounded">
Cancel
</button>

<button class="px-4 py-2 bg-purple-600 text-white rounded">
Save
</button>

</div>

</form>

</div>
</div>


{{-- EDIT MODAL --}}
<div id="editModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">

<div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">

<h2 class="text-lg font-semibold mb-4">Edit Currency</h2>

<form id="editForm" method="POST">
@csrf
@method('PUT')

<div class="grid grid-cols-2 gap-4">

<input id="edit_code" name="code" class="border p-2 rounded">

<input id="edit_name" name="name" class="border p-2 rounded">

<input id="edit_symbol" name="symbol" class="border p-2 rounded">

<input id="edit_country" name="country" class="border p-2 rounded">

<input id="edit_cf_code" name="cf_code" class="border p-2 rounded">

<input id="edit_rate" name="conversion_rate" class="border p-2 rounded">

</div>

<label class="flex items-center gap-2 mt-4">
<input id="edit_active" type="checkbox" name="is_active" value="1">
Active
</label>

<div class="flex justify-end gap-3 mt-6">

<button type="button" onclick="closeEditModal()"
class="px-4 py-2 bg-gray-200 rounded">
Cancel
</button>

<button class="px-4 py-2 bg-blue-600 text-white rounded">
Update
</button>

</div>

</form>

</div>
</div>


{{-- DELETE MODAL --}}
<div id="deleteModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">

<div class="bg-white rounded-xl shadow-lg w-full max-w-sm p-6">

<h2 class="text-lg font-semibold mb-3">Delete Currency</h2>

<p class="text-gray-500 mb-6">
Are you sure you want to delete this currency?
</p>

<form id="deleteForm" method="POST">
@csrf
@method('DELETE')

<div class="flex justify-end gap-3">

<button type="button" onclick="closeDeleteModal()"
class="px-4 py-2 bg-gray-200 rounded">
Cancel
</button>

<button class="px-4 py-2 bg-red-600 text-white rounded">
Delete
</button>

</div>

</form>

</div>
</div>
@endsection

@push('scripts')
<script>

function openAddModal(){
document.getElementById('addModal').classList.remove('hidden')
}

function closeAddModal(){
document.getElementById('addModal').classList.add('hidden')
}

function openEditModal(currency){

document.getElementById('editModal').classList.remove('hidden')

document.getElementById('editForm').action =
"/admin/settings/currencies/"+currency.id

document.getElementById('edit_code').value = currency.code
document.getElementById('edit_name').value = currency.name
document.getElementById('edit_symbol').value = currency.symbol
document.getElementById('edit_country').value = currency.country
document.getElementById('edit_cf_code').value = currency.cf_code
document.getElementById('edit_rate').value = currency.conversion_rate

document.getElementById('edit_active').checked =
currency.is_active == 1
}

function closeEditModal(){
document.getElementById('editModal').classList.add('hidden')
}

function openDeleteModal(id){

document.getElementById('deleteModal').classList.remove('hidden')

document.getElementById('deleteForm').action =
"/admin/settings/currencies/"+id
}

function closeDeleteModal(){
document.getElementById('deleteModal').classList.add('hidden')
}

</script>
@endpush