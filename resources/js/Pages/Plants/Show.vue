<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime';
import {DocumentIcon, PencilSquareIcon} from '@heroicons/vue/24/outline'

dayjs.extend(relativeTime)

defineProps({
    plant: Object,
});
</script>

<template>
    <Head :title="plant.seed.name"/>

    <AuthenticatedLayout>
        <div>
            <div>
                <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                    <div class="sm:flex sm:items-end sm:space-x-5">
                        <div class="flex">
                            <img class="h-24 w-24 rounded-full ring-4 ring-white sm:h-32 sm:w-32"
                                 :src="plant.seed.public_image_url" alt=""/>
                        </div>
                        <div
                            class="mt-6 sm:flex sm:min-w-0 sm:flex-1 sm:items-center sm:justify-end sm:space-x-6 sm:pb-1">
                            <div class="mt-6 min-w-0 flex-1 sm:hidden 2xl:block">
                                <h1 class="truncate text-2xl font-bold text-gray-900">{{ plant.seed.name }} <span
                                    class="text-gray-500 text-sm">{{ plant.seed.variety }}</span></h1>
                                <p>Started on {{ dayjs(plant.created_at).format('D/M') }}</p>
                            </div>
                            <div
                                class="justify-stretch mt-6 flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-4">
                                <Link :href="route('seeds.show', { id: plant.seed.id })"
                                      class="inline-flex justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    <DocumentIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true"/>
                                    Seed information
                                </Link>

                                <button type="button"
                                        class="inline-flex justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    <PencilSquareIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true"/>
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 hidden min-w-0 flex-1 sm:block 2xl:hidden">
                        <h1 class="truncate text-2xl font-bold text-gray-900">{{ plant.seed.name }}</h1>
                    </div>
                </div>
            </div>

            <div class="mx-auto mt-6 max-w-5xl px-4 sm:px-6 lg:px-8">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ plant.quantity }}</dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ plant.location }}</dd>
                    </div>

                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500">Days</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{
                                dayjs().diff(dayjs(plant.created_at), 'day')
                            }} out of {{
                                plant.seed.sprouting_time_days_max.length > 0 ? plant.seed.sprouting_time_days_min + ' ' + plant.seed.sprouting_time_days_max : plant.seed.sprouting_time_days_min
                            }}
                        </dd>
                    </div>

                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                        <dd class="mt-1 max-w-prose space-y-5 text-sm text-gray-900"
                            v-html="plant.notes || 'None yet'"/>
                    </div>
                </dl>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
