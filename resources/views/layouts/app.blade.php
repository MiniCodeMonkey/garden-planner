<!DOCTYPE html>
<html class="h-full bg-gray-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Garden Planner</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full">
        <div class="min-h-full">
            <div id="navigation"></div>

            <main class="-mt-32">
              <div class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
                  <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-5 sm:p-6">
                      @yield('content')
                    </div>
                  </div>
              </div>
            </main>
        </div>
    </body>
</html>
