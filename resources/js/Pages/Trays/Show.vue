<script setup>
import {ref} from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import dayjs from 'dayjs'
import {ArchiveBoxIcon, PencilSquareIcon, SparklesIcon} from '@heroicons/vue/24/outline'

defineProps({
    tray: Object,
    plants: Array,
});

const tabs = [
    'Details',
    'Planting',
    'Calendar',
]

const currentTab = ref('Details')
</script>

<template>
    <Head :title="tray.name"/>

    <AuthenticatedLayout>
        <div>
            <div>
                <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                    <div class="sm:flex sm:items-end sm:space-x-5">
                        <div class="flex">
                            <img class="h-24 w-24 rounded-full ring-4 ring-white sm:h-32 sm:w-32"
                                 :src="tray.public_image_url" alt=""/>
                        </div>
                        <div
                            class="mt-6 sm:flex sm:min-w-0 sm:flex-1 sm:items-center sm:justify-end sm:space-x-6 sm:pb-1">
                            <div class="mt-6 min-w-0 flex-1 sm:hidden 2xl:block">
                                <h1 class="truncate text-2xl font-bold text-gray-900">{{ tray.name }}</h1>
                            </div>
                            <div
                                class="justify-stretch mt-6 flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-4">
                                <Link :href="route('plants.create', { tray: tray.id })"
                                      class="inline-flex justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    <SparklesIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true"/>
                                    Germinate
                                </Link>

                                <Link :href="route('trays.edit', tray.id)"
                                      class="inline-flex justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    <PencilSquareIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true"/>
                                    Edit
                                </Link>

                                <Link
                                    :href="route('trays.destroy', tray.id)"
                                    method="delete"
                                    as="button"
                                    class="inline-flex justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                >
                                    <ArchiveBoxIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true"/>
                                    Hide for the season
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 hidden min-w-0 flex-1 sm:block 2xl:hidden">
                        <h1 class="truncate text-2xl font-bold text-gray-900">{{ tray.name }}</h1>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mt-6 sm:mt-2 2xl:mt-5">
                <div class="border-b border-gray-200">
                    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button v-for="tab in tabs" :key="tab" @click="currentTab = tab"
                                    :class="[currentTab === tab ? 'border-pink-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700', 'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium']"
                                    :aria-current="currentTab === tab ? 'page' : undefined">{{ tab }}
                            </button>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Description list -->
            <div class="mx-auto mt-6 max-w-5xl px-4 sm:px-6 lg:px-8" v-if="currentTab === 'Details'">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Variety</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ tray.variety || 'Unknown' }}</dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ tray.category }}</dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Inventory</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ tray.quantity }} trays</dd>
                    </div>

                    <div class="sm:col-span-1" v-if="tray.link && tray.link.length > 0">
                        <dt class="text-sm font-medium text-gray-500">Reference</dt>
                        <dd class="mt-1 text-sm text-gray-900"><a :href="tray.link" target="_blank">{{ tray.link }}</a>
                        </dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Green house?</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ tray.green_house ? 'Yes' : 'No' }}</dd>
                    </div>

                    <div class="sm:col-span-1" v-if="tray.inventory_last_checked">
                        <dt class="text-sm font-medium text-gray-500">Inventory last checked</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{
                                dayjs(tray.inventory_last_checked).format('D/M/YYYY')
                            }}
                        </dd>
                    </div>

                    <div class="sm:col-span-2" v-if="tray.notes && tray.notes.length > 0">
                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                        <dd class="mt-1 max-w-prose space-y-5 text-sm text-gray-900" v-html="tray.notes"/>
                    </div>
                </dl>
            </div>

            <div class="mx-auto mt-6 max-w-5xl px-4 sm:px-6 lg:px-8" v-if="currentTab === 'Planting'">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1" v-if="tray.sprouting_time_days_min">
                        <dt class="text-sm font-medium text-gray-500">Sprouting time</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{
                                tray.sprouting_time_days_max.length > 0 ? tray.sprouting_time_days_min + ' ' + tray.sprouting_time_days_max : tray.sprouting_time_days_min
                            }} days
                        </dd>
                    </div>

                    <div class="sm:col-span-1" v-if="tray.sun && tray.sun.length > 0">
                        <dt class="text-sm font-medium text-gray-500">Sun</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ tray.sun }}</dd>
                    </div>

                    <div class="sm:col-span-1" v-if="tray.height && tray.height > 0">
                        <dt class="text-sm font-medium text-gray-500">Height</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ tray.height }}cm</dd>
                    </div>

                    <div class="sm:col-span-1" v-if="tray.tray_distance_min">
                        <dt class="text-sm font-medium text-gray-500">Seed distance</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{
                                tray.tray_distance_max.length > 0 ? tray.tray_distance_min + ' ' + tray.tray_distance_max : tray.tray_distance_min
                            }} cm
                        </dd>
                    </div>

                    <div class="sm:col-span-1" v-if="tray.tray_depth && tray.tray_depth > 0">
                        <dt class="text-sm font-medium text-gray-500">Seed depth</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ tray.tray_depth }}cm</dd>
                    </div>

                    <div v-if="plants.length > 0" class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Plants</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <ul>
                                <li v-for="plant in plants" :key="plant.id">
                                    <Link :href="route('plants.show', plant.id)">
                                        {{ plant.quantity }}
                                        on
                                        {{ dayjs(plant.created_at).format('D/M') }}
                                        in
                                        {{ plant.location }}
                                    </Link>
                                </li>
                            </ul>
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="mx-auto mt-6 max-w-5xl px-4 sm:px-6 lg:px-8" v-if="currentTab === 'Calendar'">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-3">
                    <div v-if="tray.traying_start">
                        <dt class="text-sm font-medium text-gray-500">Seed</dt>
                        <dd class="mt-1 max-w-prose space-y-5 text-sm text-gray-900">
                            {{ dayjs(tray.traying_start).format('D/M') }}
                            to
                            {{ dayjs(tray.traying_end).format('D/M') }}
                        </dd>
                    </div>

                    <div v-if="tray.planting_start">
                        <dt class="text-sm font-medium text-gray-500">Plant</dt>
                        <dd class="mt-1 max-w-prose space-y-5 text-sm text-gray-900">
                            {{ dayjs(tray.planting_start).format('D/M') }}
                            to
                            {{ dayjs(tray.planting_end).format('D/M') }}
                        </dd>
                    </div>

                    <div v-if="tray.harvest_start">
                        <dt class="text-sm font-medium text-gray-500">Harvest</dt>
                        <dd class="mt-1 max-w-prose space-y-5 text-sm text-gray-900">
                            {{ dayjs(tray.harvest_start).format('D/M') }}
                            to
                            {{ dayjs(tray.harvest_end).format('D/M') }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
