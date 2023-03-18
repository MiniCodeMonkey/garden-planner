<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import {Link} from '@inertiajs/vue3';
import {Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue'
import {MagnifyingGlassIcon} from '@heroicons/vue/20/solid'
import {Bars3Icon, BellIcon, XMarkIcon} from '@heroicons/vue/24/outline'

const navigation = [
    {name: 'Dashboard', href: '/dashboard', current: route().current('dashboard')},
    {name: 'Seeds', href: '/seeds', current: route().current('seeds.*')},
    {name: 'Gardens', href: '/gardens', current: route().current('garden')},
    {name: 'Calendar', href: '/calendar', current: route().current('calendar')},
    {name: 'Reports', href: '/reports', current: route().current('reports')},
]
const userNavigation = [
    {name: 'Your Profile', href: route('profile.edit')},
    {name: 'Sign out', href: route('logout'), method: 'post'},
]

defineProps({
    mainAsCard: {
        type: Boolean,
        default: true,
    },
});
</script>

<template>
    <div class="min-h-full">
        <div class="bg-green-600 pb-32">
            <Disclosure as="nav" class="border-b border-green-300 border-opacity-25 bg-green-600 lg:border-none"
                        v-slot="{ open }">
                <div class="mx-auto max-w-7xl px-2 sm:px-4 lg:px-8">
                    <div
                        class="relative flex h-16 items-center justify-between lg:border-b lg:border-green-400 lg:border-opacity-25">
                        <div class="flex items-center px-2 lg:px-0">
                            <div class="flex-shrink-0">
                                <ApplicationLogo class="block h-8 w-8"></ApplicationLogo>
                            </div>
                            <div class="hidden lg:ml-10 lg:block">
                                <div class="flex space-x-4">
                                    <Link v-for="item in navigation" :key="item.name" :href="item.href"
                                          :class="[item.current ? 'bg-green-700 text-white' : 'text-white hover:bg-green-500 hover:bg-opacity-75', 'rounded-md py-2 px-3 text-sm font-medium']"
                                          :aria-current="item.current ? 'page' : undefined">{{ item.name }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-1 justify-center px-2 lg:ml-6 lg:justify-end">
                            <div class="w-full max-w-lg lg:max-w-xs">
                                <label for="search" class="sr-only">Search</label>
                                <div class="relative text-gray-400 focus-within:text-gray-600">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <MagnifyingGlassIcon class="h-5 w-5" aria-hidden="true"/>
                                    </div>
                                    <input id="search"
                                           class="block w-full rounded-md border-0 bg-white py-1.5 pl-10 pr-3 text-gray-900 focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-green-600 sm:text-sm sm:leading-6"
                                           placeholder="Search" type="search" name="search"/>
                                </div>
                            </div>
                        </div>
                        <div class="flex lg:hidden">
                            <!-- Mobile menu button -->
                            <DisclosureButton
                                class="inline-flex items-center justify-center rounded-md bg-green-600 p-2 text-green-200 hover:bg-green-500 hover:bg-opacity-75 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-green-600">
                                <span class="sr-only">Open main menu</span>
                                <Bars3Icon v-if="!open" class="block h-6 w-6" aria-hidden="true"/>
                                <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true"/>
                            </DisclosureButton>
                        </div>
                        <div class="hidden lg:ml-4 lg:block">
                            <div class="flex items-center">
                                <button type="button"
                                        class="flex-shrink-0 rounded-full bg-green-600 p-1 text-green-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-green-600">
                                    <span class="sr-only">View notifications</span>
                                    <BellIcon class="h-6 w-6" aria-hidden="true"/>
                                </button>

                                <!-- Profile dropdown -->
                                <Menu as="div" class="relative ml-3 flex-shrink-0">
                                    <div>
                                        <MenuButton
                                            class="flex rounded-full bg-green-600 text-sm text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-green-600">
                                            <span class="sr-only">Open user menu</span>
                                            <img class="h-8 w-8 rounded-full" :src="$page.props.auth.user.photo_url"
                                                 alt=""/>
                                        </MenuButton>
                                    </div>
                                    <transition enter-active-class="transition ease-out duration-100"
                                                enter-from-class="transform opacity-0 scale-95"
                                                enter-to-class="transform opacity-100 scale-100"
                                                leave-active-class="transition ease-in duration-75"
                                                leave-from-class="transform opacity-100 scale-100"
                                                leave-to-class="transform opacity-0 scale-95">
                                        <MenuItems
                                            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                            <MenuItem v-for="item in userNavigation" :key="item.name"
                                                      v-slot="{ active }">
                                                <Link :href="item.href" :method="item.method"
                                                      :as="item.method === 'post' ? 'button' : 'a'"
                                                      :class="[active ? 'bg-gray-100' : '', 'block py-2 px-4 text-sm text-gray-700']">
                                                    {{ item.name }}
                                                </Link>
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
                        <DisclosureButton v-for="item in navigation" :key="item.name" as="a" :href="item.href"
                                          :class="[item.current ? 'bg-green-700 text-white' : 'text-white hover:bg-green-500 hover:bg-opacity-75', 'block rounded-md py-2 px-3 text-base font-medium']"
                                          :aria-current="item.current ? 'page' : undefined">{{ item.name }}
                        </DisclosureButton>
                    </div>
                    <div class="border-t border-green-700 pt-4 pb-3">
                        <div class="flex items-center px-5">
                            <div class="flex-shrink-0">
                                <img class="h-10 w-10 rounded-full" :src="$page.props.auth.user.photo_url" alt=""/>
                            </div>
                            <div class="ml-3">
                                <div class="text-base font-medium text-white">{{ $page.props.auth.user.name }}</div>
                                <div class="text-sm font-medium text-green-300">{{ $page.props.auth.user.email }}</div>
                            </div>
                            <button type="button"
                                    class="ml-auto flex-shrink-0 rounded-full bg-green-600 p-1 text-green-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-green-600">
                                <span class="sr-only">View notifications</span>
                                <BellIcon class="h-6 w-6" aria-hidden="true"/>
                            </button>
                        </div>
                        <div class="mt-3 space-y-1 px-2">
                            <DisclosureButton v-for="item in userNavigation" :key="item.name" as="a" :href="item.href"
                                              class="block rounded-md py-2 px-3 text-base font-medium text-white hover:bg-green-500 hover:bg-opacity-75">
                                {{ item.name }}
                            </DisclosureButton>
                        </div>
                    </div>
                </DisclosurePanel>
            </Disclosure>

            <header class="py-10" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex">
                    <h1 class="text-3xl font-bold tracking-tight text-white">
                        <slot name="header"/>
                    </h1>

                    <div class="ml-auto" v-if="$slots.headeraction">
                        <slot name="headeraction"/>
                    </div>
                </div>
            </header>
        </div>

        <main class="-mt-32">
            <div class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
                <div v-if="mainAsCard" class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-5 sm:p-6">
                        <slot/>
                    </div>
                </div>
                <div v-else>
                    <slot/>
                </div>
            </div>
        </main>
    </div>
</template>
