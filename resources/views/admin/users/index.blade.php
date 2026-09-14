@extends('layouts.admin.app')

@section('title', 'All Users')

@section('content')
<div 
    x-data="{ open: false, openEdit: false, editingUser: {} }" 
    class="w-full max-w-full lg:max-w-7xl mx-auto px-4 py-6 space-y-8 overflow-x-hidden"

>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">All Users</h2>

        <button 
            @click="open = true"
class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow transition w-full md:w-auto max-w-full"
        >
            + Add New User
        </button>
    </div>

    <!-- Desktop Table -->
<div class="hidden md:block bg-white border border-gray-200 shadow-sm rounded-xl overflow-x-auto">
        <table class="min-w-full text-sm text-gray-700">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Name</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Email</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Role</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Balance</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Joined</th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4 capitalize">{{ $user->role }}</td>
                         <td class="px-6 py-4 capitalize">
                             
                              {{ formatCurrency($user->wallet_balance, 'USD') }}
                             </td>
                        <td class="px-6 py-4">
                            @if ($user->is_active)
                                <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Active</span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $user->created_at->format('M d, Y') }}</td>

                        <td class="px-6 py-4 space-x-3">
                            <button 
                                @click="
                                    openEdit = true;
                                    editingUser = {
                                        id: '{{ $user->id }}',
                                        name: '{{ $user->name }}',
                                        email: '{{ $user->email }}',
                                        role: '{{ $user->role }}',
                                        balance: '{{ $user->wallet_balance }}',
                                        status: '{{ $user->is_active ? 'active' : 'inactive' }}'
                                    };
                                "
                                class="text-indigo-600 hover:text-indigo-800 font-medium"
                            >
                                Edit
                            </button>

                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this user?')"
                                        class="text-red-600 hover:text-red-800 font-medium">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-6 text-gray-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="grid gap-4 md:hidden">
        @forelse ($users as $user)
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl p-4 space-y-2">
                <div class="flex justify-between">
                    <h3 class="font-semibold text-lg text-gray-900">{{ $user->name }}</h3>

                    @if ($user->is_active)
                        <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full self-start">Active</span>
                    @else
                        <span class="px-3 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full self-start">Inactive</span>
                    @endif
                </div>

                <p class="text-sm text-gray-600">{{ $user->email }}</p>

                <div class="flex justify-between text-sm">
                    <span class="capitalize text-gray-700">
                    Role: <span class="font-medium">{{ $user->role }}</span>
                    </span>
                    <span class="capitalize text-gray-700">
                    Balance: <span class="font-medium">{{ formatCurrency($user->wallet_balance, 'USD') }}</span>
                    </span>
                    <br><br>
                    <span class="text-gray-700">
                        {{ $user->created_at->format('M d, Y') }}</span>
                </div>

                <div class="flex gap-4 pt-3">
                    <button 
                        @click="
                            openEdit = true;
                            editingUser = {
                                id: '{{ $user->id }}',
                                name: '{{ $user->name }}',
                                email: '{{ $user->email }}',
                                role: '{{ $user->role }}',
                                balance: '{{ $user->wallet_balance }}',
                                status: '{{ $user->is_active ? 'active' : 'inactive' }}'
                            };
                        "
                        class="text-indigo-600 font-medium"
                    >
                        Edit
                    </button>

                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('Are you sure you want to delete this user?')"
                            class="text-red-600 font-medium">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 py-6">No users found.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pt-4 flex justify-center text-indigo-600">
@include('components.pager', ['paginator' => $users, '__pager' => $users])

    </div>

   <!-- Add User Modal -->
<div 
    x-show="open" 
    x-transition.opacity 
class="fixed inset-0 bg-black/50 z-50 flex items-end md:items-center justify-center"
>
    <!-- Modal Content -->
    <div 
        @click.away="open = false"
        x-transition.duration.300ms
        class="bg-white rounded-t-2xl md:rounded-xl w-full max-w-lg p-6 shadow-xl transform transition-all"
    >
        <h2 class="text-xl font-bold text-gray-900 mb-4">Add New User</h2>

        <form action="{{ route('admin.users.add') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block font-medium text-gray-800 mb-1">Name</label>
                <input type="text" name="name"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500"
                       required>
            </div>

            <div>
                <label class="block font-medium text-gray-800 mb-1">Email</label>
                <input type="email" name="email"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500"
                       required>
            </div>
            @if (!$allow_label_registration)
     <div>
                <label class="block font-medium text-gray-800 mb-1">Role</label>
                <select name="role"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="artist">Artist</option>
                </select>
            </div>
@else
    <div>
                <label class="block font-medium text-gray-800 mb-1">Role</label>
                <select name="role"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="artist">Artist</option>
                     <option value="label">Label</option>
                </select>
            </div>
@endif

           

            <div>
                <label class="block font-medium text-gray-800 mb-1">Password</label>
                <input type="password" name="password"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500"
                       required>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        @click="open = false"
                        class="px-4 py-2.5 rounded-lg border text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition">
                    Add User
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Edit User Modal -->
<div 
    x-show="openEdit" 
    x-transition.opacity 
class="fixed inset-0 bg-black/50 z-50 flex items-end md:items-center justify-center"
>
    <!-- Modal Content -->
    <div 
        @click.away="openEdit = false"
        x-transition.duration.300ms
        class="bg-white rounded-t-2xl md:rounded-xl w-full max-w-lg p-6 shadow-xl transform transition-all"
    >
        <h2 class="text-xl font-bold text-gray-900 mb-4">Edit User</h2>

        <form :action="'{{ url('admin/users') }}/' + editingUser.id" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium text-gray-800 mb-1">Name</label>
                <input type="text" name="name" x-model="editingUser.name"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500"
                       required>
            </div>

            <div>
                <label class="block font-medium text-gray-800 mb-1">Email</label>
                <input type="email" name="email" x-model="editingUser.email"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500"
                       required>
            </div>
 @if (!$allow_label_registration)
            <div>
                <label class="block font-medium text-gray-800 mb-1">Role</label>
                <select name="role" x-model="editingUser.role"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="artist">Artist</option>
                </select>
            </div>
    @else
    <div>
                <label class="block font-medium text-gray-800 mb-1">Role</label>
                <select name="role" x-model="editingUser.role"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="artist">Artist</option>
                    <option value="label">Label</option>
                </select>
            </div>
    @endif
            
           <div>
        <label class="block font-medium text-gray-800 mb-1">Balance ($)</label>
        <input type="text" name="wallet_balance" x-model="editingUser.balance" placeholder="$"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500"
                       required>
            </div>

            <div>
                <label class="block font-medium text-gray-800 mb-1">Status</label>
                <select name="status" x-model="editingUser.status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-2">
                <button type="button"
                        @click="openEdit = false"
                        class="px-4 py-2.5 rounded-lg border text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                    Cancel
                </button>

                <button type="submit"
                        class="px-4 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

</div>
@endsection
