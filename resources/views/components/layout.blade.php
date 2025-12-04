<!DOCTYPE html>
<html class="h-full bg-gray-900">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite('resources/css/app.css')
  </head>
  <body class="h-full">
<div class="min-h-full">
  <nav class="bg-gray-800/50">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <div class="flex items-center">
          <div class="shrink-0">
            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company" class="size-8" />
          </div>
          <div class="hidden md:block">
            <div class="ml-10 flex items-baseline space-x-4">
              <!-- Current: "bg-gray-950/50 text-white", Default: "text-gray-300 hover:bg-white/5 hover:text-white" -->
              {{-- <x-nav-link href="/" :active="request()->is('/')">Home</x-nav-link>
              <x-nav-link href="/about" :active="request()->is('about')">About</x-nav-link>
              <x-nav-link href="/contact" :active="request()->is('contact')">Contact</x-nav-link> --}}
              <x-nav-link href="/">Home</x-nav-link>
              <x-nav-link href="/about">About</x-nav-link>
              <x-nav-link href="/contact">Contact</x-nav-link>
              <x-nav-link href="/jobs">Jobs</x-nav-link>
              {{-- <x-nav-link href="/" type='a'>Home</x-nav-link>
              <x-nav-link href="/about" type='a'>About</x-nav-link>
              <x-nav-link href="/contact" type='a'>Contact</x-nav-link>
              <x-nav-link href="/contact" type='button'>Contact</x-nav-link> --}}
            </div>
          </div>
        </div>
        <div class="hidden md:block">
          <div class="ml-4 flex items-center md:ml-6">
            @guest
              <x-nav-link href='/login'>Log In</x-nav-link>
              <x-nav-link href='/register'>Register</x-nav-link>

            @endguest
          </div>
        </div>
      </div>
    </div>
  </nav>

  <header class="relative bg-gray-800 after:pointer-events-none after:absolute after:inset-x-0 after:inset-y-0 after:border-y after:border-white/10">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-white">
        {{$heading}}
      </h1>
    </div>
  </header>
  <main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      {{$slot}}
    </div>
  </main>
</div>

  </body>
</html>