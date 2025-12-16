<x-layout>
{{--    'phone',--}}
{{--    'first_name',--}}
{{--    'last_name',--}}
{{--    'avatar',--}}
{{--    'id_card',--}}
{{--    'birth_date',--}}
{{--    'password',--}}
{{--    'user_verified_at'--}}
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <form action="{{ route('user.update', $user->id) }}" class="w-full max-w-2xl bg-white p-8 rounded-xl shadow-lg" method="POST" enctype="multipart/form-data">
            @method('PATCH')
            @csrf

            {{-- ================= Avatar ================= --}}
            <div class="flex flex-col items-center mb-8">
                <label for="avatar" class="cursor-pointer">
                    <img
                        src="{{ $user->avatar ? asset($user->avatar) : asset('images/default-avatar.png') }}"
                        class="w-32 h-32 rounded-full object-cover border-2 border-gray-300 hover:opacity-80"
                    >
                </label>

                <input type="file" name="avatar" id="avatar" class="hidden">

                <x-form-input
                    name="avatar_url"
                    type="text"
                    placeholder="Avatar URL from server"
                    class="mt-3"
                />
            </div>

            {{-- ================= Read-only info ================= --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <x-form-field>
                    <x-form-label>ID</x-form-label>
                    <x-form-input value="{{ $user->id }}" disabled />
                </x-form-field>

                <x-form-field>
                    <x-form-label>Admin</x-form-label>
                    <x-form-input value="{{ $user->is_admin ? 'Yes' : 'No' }}" disabled />
                </x-form-field>
            </div>

            {{-- ================= Editable fields ================= --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-field>
                    <x-form-label>Phone</x-form-label>
                    <x-form-input
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        placeholder="Phone number"
                    />
                </x-form-field>

                <x-form-field>
                    <x-form-label>Balance</x-form-label>
                    <x-form-input
                        name="balance"
                        type="number"
                        step="0.01"
                        value="{{ old('balance', $user->balance) }}"
                    />
                </x-form-field>

                <x-form-field>
                    <x-form-label>First Name</x-form-label>
                    <x-form-input
                        name="first_name"
                        value="{{ old('first_name', $user->first_name) }}"
                        placeholder="First name"
                    />
                </x-form-field>

                <x-form-field>
                    <x-form-label>Last Name</x-form-label>
                    <x-form-input
                        name="last_name"
                        value="{{ old('last_name', $user->last_name) }}"
                        placeholder="Last name"
                    />
                </x-form-field>

                <x-form-field>
                    <x-form-label>Birth Date</x-form-label>
                    <x-form-input
                        name="birth_date"
                        type="date"
                        value="{{ old('birth_date', $user->birth_date) }}"
                    />
                </x-form-field>
            </div>

            {{-- ================= Verification ================= --}}
            <div class="mt-6">
                <x-form-field>
                    <x-form-label>Verification Status</x-form-label>
                    <x-form-input
                        value='{{ $user->user_verified_at ? "Verified at {$user->user_verified_at}" : "Not Verified" }}'
                        class='{{$user->user_verified_at ? "text-green-600" : "text-red-600"}}'
                        disabled
                    />
                </x-form-field>
            </div>

            {{-- ================= Password ================= --}}
            <div class="mt-6">
                <x-form-field>
                    <x-form-label>Password</x-form-label>
                    <x-form-input
                        name="password"
                        type="password"
                        placeholder="New password"
                    />
                </x-form-field>
            </div>

            {{-- ================= ID Card (IMAGE – LAST) ================= --}}
            <div class="mt-10">
                <x-form-label>ID Card</x-form-label>

                <div class="flex flex-col items-center mt-3">
                    <label for="id_card" class="cursor-pointer">
                        <img
                            src="{{ $user->id_card ? asset($user->id_card) : asset('images/id-placeholder.png') }}"
                            class="w-64 h-40 object-cover border-2 border-dashed border-gray-300 rounded-lg hover:opacity-80"
                        >
                    </label>

                    <input type="file" name="id_card" id="id_card" class="hidden">

                    <x-form-input
                        name="id_card_url"
                        type="text"
                        placeholder="ID card image URL"
                        class="mt-4 w-full"
                    />
                </div>
            </div>
            {{-- ================== Errors ======================= --}}
            <x-form-error name="message" />
            {{-- ================= Submit ================= --}}
            <div class="mt-8 flex justify-end">
                <button
                    type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition"
                >
                    Save Changes
                </button>
            </div>
        </form>
    </div>

</x-layout>
{{--// Avatar--}}
{{--if ($request->hasFile('avatar')) {--}}
{{--$user->avatar = $request->file('avatar')->store('avatars', 'public');--}}
{{--} elseif ($request->filled('avatar_url')) {--}}
{{--$user->avatar = $request->avatar_url;--}}
{{--}--}}

{{--// ID Card--}}
{{--if ($request->hasFile('id_card')) {--}}
{{--$user->id_card = $request->file('id_card')->store('id_cards', 'public');--}}
{{--} elseif ($request->filled('id_card_url')) {--}}
{{--$user->id_card = $request->id_card_url;--}}
{{--}--}}
