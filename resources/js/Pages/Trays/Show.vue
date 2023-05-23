<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';

defineProps({
    tray: Object,
    plants: Object,
});
</script>

<template>
    <Head :title="tray.type + ' ' + tray.name"/>

    <AuthenticatedLayout>
        <div class="mb-4">
            <h1 class="truncate text-2xl font-bold text-gray-900">{{ tray.type }} <span
                class="text-gray-500 text-sm">{{ tray.name }}</span></h1>
        </div>

        <div>
            <div class="rounded-lg bg-gray-700 inline-block p-2">
                <table>
                    <tr v-for="row in tray.rows">
                        <td v-for="column in tray.columns" class="w-16 h-16">
                            <div class="rounded-lg bg-gray-900 w-full h-full flex items-center justify-center">
                                <Link v-if="plants[row + ',' + column]"
                                      :href="route('plants.show', { id: plants[row + ',' + column].id })">
                                    <img
                                        :src="plants[row + ',' + column].seed.public_image_url"
                                        :alt="plants[row + ',' + column].seed.name"
                                        class="h-10 w-10 rounded-full ring-4 ring-white"/>
                                </Link>
                                <span v-else class="text-white">
                                    {{ row + ',' + column }}
                                </span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
