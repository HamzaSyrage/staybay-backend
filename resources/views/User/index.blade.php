<x-layout class="dark:bg-gray-800 dark:border-gray-700">
    <div class="p-4 bg-white block sm:flex items-center justify-between border-b border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
               <x-nav-bar>
                        <li class="inline-flex items-center">
                            <x-nav-link route="login" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                                Home
                            </x-nav-link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                                <a class="ml-1 text-gray-700 md:ml-2">Users</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/></svg>
                                <span class="ml-1 text-gray-400 md:ml-2">List</span>
                            </div>
                        </li>
                   <x-slot:logout>
                       <x-form route='logout' method="post">
                           @csrf
                           <x-form-button class="bg-red-600 hover:bg-red-900">
                               Logout
                           </x-form-button>
                       </x-form>
                   </x-slot:logout>
               </x-nav-bar>
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">All Users</h1>
            </div>

            <div class="sm:flex">
                <form class="lg:pr-3" method="GET" action="{{route('dashboard')}}">
                    <label for="users-search" class="sr-only">Search</label>
                    <div class="relative mt-1 lg:w-64 xl:w-96">
                        <input type="text" name="search" id="users-search"
                               class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg
                           focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5
                           dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="Search users...">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- TABLE -->
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-700">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="p-4"></th>
                            <x-Table.th>First Name</x-Table.th>
                            <x-Table.th>Last Name</x-Table.th>
                            <x-Table.th>Phone</x-Table.th>
                            <x-Table.th>Birth Date</x-Table.th>
                            <x-Table.th>Verified</x-Table.th>
                            <x-Table.th>Actions</x-Table.th>

                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y dark:bg-gray-800 dark:divide-gray-700">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                {{-- <td class="p-4">
                                    <input type="checkbox" class="w-4 h-4">
                                </td> --}}

                                <td class="flex items-center p-4 space-x-3">
                                    <img class="w-10 h-10 rounded-full"
                                         src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                                         alt="avatar">
                                </td>
                                <td>
                                        <div class="text-base font-semibold">{{ $user->first_name }}</div>
                                </td>
                                <td>
                                        <div class="text-base font-semibold">{{ $user->last_name }}</div>
                                </td>
                                <td class="p-4 text-gray-500">
                                    {{ $user->phone }}
                                </td>
                                <td>
                                    {{$user->birth_date  }}
                                </td>
                                <td class="p-4">
                                {{-- <span class="flex items-center">
                                    @if($user->user_verified_at != null)
                                        <span class="h-2.5 w-2.5 rounded-full bg-green-400 mr-2"></span>
                                    @else
                                        <span class="h-2.5 w-2.5 rounded-full bg-red-500 mr-2"></span>
                                    @endif
                                    {{ ucfirst($user->status) }}
                                </span> --}}

                                    @if($user->user_verified_at != null)
                                        <button class="px-3 py-2 text-sm  bg-gray-600 rounded-lg text-black">
                                            verified
                                        </button>
                                    @else
                                        <form action="{{ route('users.verify', $user) }}" method="POST" class="inline">
                                            @csrf
                                            <button
                                                class="px-3 py-2 text-sm text-white bg-green-500 rounded-lg hover:bg-green-700"
                                                onclick="return confirm('Verify this user?')">
                                                Verify
                                            </button>
                                        </form>
                                    @endif

                                </td>

                                <td class="p-4 space-x-2 whitespace-nowrap">
                                    <button data-modal-target="edit-user-modal"
                                            data-modal-toggle="edit-user-modal"
                                            class="px-3 py-2 text-sm text-white bg-primary-700 rounded-lg">
                                        Edit
                                    </button>
                                    {{-- {{ route('users.destroy', $user->id) }} --}}
                                    <form action="" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-800">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- PAGINATION -->
    <div class="sticky bottom-0 right-0 w-full p-4 bg-white border-t dark:bg-gray-800 dark:border-gray-700">
        {{ $users->links() }}
    </div>
</x-layout>
