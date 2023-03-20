<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import {DocumentMagnifyingGlassIcon, SparklesIcon} from '@heroicons/vue/24/outline'

defineProps({
    seeds: Array,
});
</script>

<template>
    <Head title="Seeds"/>

    <AuthenticatedLayout>
        <template #header>
            Seeds
        </template>

        <template #headeraction>
            <Link href="seeds/create"
                  class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                Add new seed
            </Link>
        </template>

        <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <li v-for="seed in seeds" class="col-span-1 divide-y divide-gray-200 rounded-lg bg-white shadow">
                <div class="flex w-full items-center justify-between space-x-6 p-6">
                    <div class="flex-1 truncate">
                        <div class="flex items-center space-x-3">
                            <h3 class="truncate text-sm font-medium text-gray-900">{{ seed.name }}</h3>
                            <span
                                class="inline-block flex-shrink-0 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">{{
                                    seed.category
                                }}</span>
                        </div>
                        <p class="mt-1 truncate text-sm text-gray-500">{{ seed.variety }}</p>
                    </div>
                    <img class="h-10 w-10 flex-shrink-0 rounded-full bg-gray-300" :src="seed.public_image_url"
                         :alt="seed.name">
                </div>
                <div>
                    <div class="-mt-px flex divide-x divide-gray-200">
                        <div class="flex w-0 flex-1">
                            <Link :href="route('germinations.create', { seed: seed.id })"
                                  class="relative inline-flex w-0 flex-1 items-center justify-center gap-x-3 rounded-br-lg border border-transparent py-4 text-sm font-semibold text-gray-900">
                                <SparklesIcon class="h-5 w-5 text-gray-400"/>
                                Germinate
                            </Link>
                        </div>
                        <div class="-ml-px flex w-0 flex-1">
                            <Link :href="route('seeds.show', { id: seed.id })"
                                  class="relative inline-flex w-0 flex-1 items-center justify-center gap-x-3 rounded-br-lg border border-transparent py-4 text-sm font-semibold text-gray-900">
                                <DocumentMagnifyingGlassIcon class="h-5 w-5 text-gray-400"/>
                                Details
                            </Link>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </AuthenticatedLayout>
</template>
