<template>
    <div class="bg-green-600 pb-32">
      <Disclosure as="nav" class="border-b border-green-300 border-opacity-25 bg-green-600 lg:border-none" v-slot="{ open }">
        <div class="mx-auto max-w-7xl px-2 sm:px-4 lg:px-8">
          <div class="relative flex h-16 items-center justify-between lg:border-b lg:border-green-400 lg:border-opacity-25">
            <div class="flex items-center px-2 lg:px-0">
              <div class="flex-shrink-0">
                <img class="block h-8 w-8" src="https://tailwindui.com/img/logos/mark.svg?color=green&shade=300" alt="Your Company" />
              </div>
              <div class="hidden lg:ml-10 lg:block">
                <div class="flex space-x-4">
                  <a v-for="item in navigation" :key="item.name" :href="item.href" :class="[item.current ? 'bg-green-700 text-white' : 'text-white hover:bg-green-500 hover:bg-opacity-75', 'rounded-md py-2 px-3 text-sm font-medium']" :aria-current="item.current ? 'page' : undefined">{{ item.name }}</a>
                </div>
              </div>
            </div>
            <div class="flex flex-1 justify-center px-2 lg:ml-6 lg:justify-end">
              <div class="w-full max-w-lg lg:max-w-xs">
                <label for="search" class="sr-only">Search</label>
                <div class="relative text-gray-400 focus-within:text-gray-600">
                  <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <MagnifyingGlassIcon class="h-5 w-5" aria-hidden="true" />
                  </div>
                  <input id="search" class="block w-full rounded-md border-0 bg-white py-1.5 pl-10 pr-3 text-gray-900 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-green-600 sm:text-sm sm:leading-6" placeholder="Search" type="search" name="search" />
                </div>
              </div>
            </div>
            <div class="flex lg:hidden">
              <!-- Mobile menu button -->
              <DisclosureButton class="inline-flex items-center justify-center rounded-md bg-green-600 p-2 text-green-200 hover:bg-green-500 hover:bg-opacity-75 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-green-600">
                <span class="sr-only">Open main menu</span>
                <Bars3Icon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
                <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
              </DisclosureButton>
            </div>
            <div class="hidden lg:ml-4 lg:block">
              <div class="flex items-center">
                <button type="button" class="flex-shrink-0 rounded-full bg-green-600 p-1 text-green-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-green-600">
                  <span class="sr-only">View notifications</span>
                  <BellIcon class="h-6 w-6" aria-hidden="true" />
                </button>

                <!-- Profile dropdown -->
                <Menu as="div" class="relative ml-3 flex-shrink-0">
                  <div>
                    <MenuButton class="flex rounded-full bg-green-600 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-green-600">
                      <span class="sr-only">Open user menu</span>
                      <img class="h-8 w-8 rounded-full" :src="user.imageUrl" alt="" />
                    </MenuButton>
                  </div>
                  <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                    <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                      <MenuItem v-for="item in userNavigation" :key="item.name" v-slot="{ active }">
                        <a :href="item.href" :class="[active ? 'bg-gray-100' : '', 'block py-2 px-4 text-sm text-gray-700']">{{ item.name }}</a>
                      </MenuItem>
                    </MenuItems>
                  </transition>
                </Menu>
              </div>
            </div>
          </div>
        </div>

        <DisclosurePanel class="lg:hidden">
          <div class="space-y-1 px-2 pt-2 pb-3">
            <DisclosureButton v-for="item in navigation" :key="item.name" as="a" :href="item.href" :class="[item.current ? 'bg-green-700 text-white' : 'text-white hover:bg-green-500 hover:bg-opacity-75', 'block rounded-md py-2 px-3 text-base font-medium']" :aria-current="item.current ? 'page' : undefined">{{ item.name }}</DisclosureButton>
          </div>
          <div class="border-t border-green-700 pt-4 pb-3">
            <div class="flex items-center px-5">
              <div class="flex-shrink-0">
                <img class="h-10 w-10 rounded-full" :src="user.imageUrl" alt="" />
              </div>
              <div class="ml-3">
                <div class="text-base font-medium text-white">{{ user.name }}</div>
                <div class="text-sm font-medium text-green-300">{{ user.email }}</div>
              </div>
              <button type="button" class="ml-auto flex-shrink-0 rounded-full bg-green-600 p-1 text-green-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-green-600">
                <span class="sr-only">View notifications</span>
                <BellIcon class="h-6 w-6" aria-hidden="true" />
              </button>
            </div>
            <div class="mt-3 space-y-1 px-2">
              <DisclosureButton v-for="item in userNavigation" :key="item.name" as="a" :href="item.href" class="block rounded-md py-2 px-3 text-base font-medium text-white hover:bg-green-500 hover:bg-opacity-75">{{ item.name }}</DisclosureButton>
            </div>
          </div>
        </DisclosurePanel>
      </Disclosure>
      <header class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <h1 class="text-3xl font-bold tracking-tight text-white">Garden Planner</h1>
        </div>
      </header>
    </div>
</template>

<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'
import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const user = {
  name: 'Tom Cook',
  email: 'tom@example.com',
  imageUrl:
    'https://www.gravatar.com/avatar/045dfdf10ef09f0eeaf9c4f0a27f5c6e?s=256',
}
const navigation = [
  { name: 'Dashboard', href: '/', current: window.location.pathname === '/' },
  { name: 'Seeds', href: '/seeds', current: window.location.pathname.indexOf('/seeds') === 0 },
  { name: 'Garden', href: '/garden', current: window.location.pathname.indexOf('/garden') === 0 },
  { name: 'Calendar', href: '/calendar', current: window.location.pathname.indexOf('/calendar') === 0 },
  { name: 'Reports', href: '/reports', current: window.location.pathname.indexOf('/reports') === 0 },
]
const userNavigation = [
  { name: 'Your Profile', href: '#' },
  { name: 'Settings', href: '#' },
  { name: 'Sign out', href: '#' },
]
</script>
