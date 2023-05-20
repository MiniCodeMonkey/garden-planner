<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import dayjs from 'dayjs';

defineProps({
    plants: Array,
});
</script>

<template>
    <Head title="Plants"/>

    <AuthenticatedLayout>
        <template #header>
            Plants
        </template>

        <div class="px-4 sm:px-6 lg:px-8">
            <div class="mt-8 flow-root">
                <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead>
                            <tr>
                                <th scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                    Name
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Quantity
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Date
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Location
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="plant in plants" :key="plant.id">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-0">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <img class="h-10 w-10 rounded-full" :src="plant.seed.public_image_url"
                                                 alt=""/>
                                        </div>
                                        <div class="ml-4">
                                            <Link :href="route('plants.show', { id: plant.id })">
                                                <div class="font-medium text-gray-900">{{ plant.seed.name }}</div>
                                                <div class="text-gray-500">{{ plant.seed.variety }}</div>
                                            </Link>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ plant.quantity }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ dayjs(plant.created_at).format('D/M') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <Link v-if="plant.tray" :href="route('trays.show', plant.seed_tray_id)">
                                        {{ plant.tray.type }} {{ plant.tray.name }}
                                    </Link>
                                    <span v-else>{{ plant.location }}</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
