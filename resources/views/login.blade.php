<x-layout>
  <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
  <div class="sm:mx-auto sm:w-full sm:max-w-sm">
    <x-logo src="./logo.jpg" alt="Staybay" />
    <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Sign in to your account</h2>
  </div>

  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
    <form action="/login" method="post" class="space-y-6">
        @csrf
      <x-form-field>
            <x-form-label for='phone'>Phone</x-form-label>
            <x-form-error name='phone'/>
            <x-form-phone-input/>
      </x-form-field>

      <x-form-field>
            <x-form-label for='password'>Password</x-form-label>
            <x-form-error name='password'/>
            <x-form-password-input/>
      </x-form-field>

    <x-form-button>Sign in</x-form-button>
    </form>
  </div>
</div>
</x-layout>