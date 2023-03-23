<script setup>
import 'maplibre-gl/dist/maplibre-gl.css';
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot} from '@headlessui/vue'
import {XMarkIcon} from '@heroicons/vue/24/outline'

const props = defineProps({
    garden: Object,
    plants: Array,
    selectedPlant: Object,
});
</script>

<template>
    <TransitionRoot as="template" :show="garden !== null">
        <Dialog as="div" class="relative z-10" @close="$emit('close')">
            <div class="fixed inset-0"/>

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                        <TransitionChild as="template"
                                         enter="transform transition ease-in-out duration-500 sm:duration-700"
                                         enter-from="translate-x-full" enter-to="translate-x-0"
                                         leave="transform transition ease-in-out duration-500 sm:duration-700"
                                         leave-from="translate-x-0" leave-to="translate-x-full">
                            <DialogPanel class="pointer-events-auto w-screen max-w-2xl">
                                <div class="flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl">
                                    <div class="px-4 sm:px-6">
                                        <div class="flex items-start justify-between">
                                            <DialogTitle class="text-base font-semibold leading-6 text-gray-900">
                                                Select plants
                                            </DialogTitle>
                                            <div class="ml-3 flex h-7 items-center">
                                                <button type="button"
                                                        class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                        @click="$emit('close')">
                                                    <span class="sr-only">Close panel</span>
                                                    <XMarkIcon class="h-6 w-6" aria-hidden="true"/>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="relative mt-6 flex-1 px-4 sm:px-6">
                                        <div role="list"
                                             class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                                            <button v-for="plant in plants" :key="plant.id"
                                                    :class="[selectedPlant && selectedPlant.id === plant.id ? 'scale-110 rotate-6' : 'hover:scale-110 hover:rotate-6', 'col-span-1 flex flex-col divide-y divide-gray-200 rounded-lg bg-white text-center shadow transition ease-in-out duration-300']"
                                                    @click="$emit('plantSelected', selectedPlant && selectedPlant.id === plant.id ? null : plant)">
                                                <div class="flex flex-1 flex-col p-2">
                                                    <img class="mx-auto flex-shrink-0 rounded-full"
                                                         :src="plant.seed.public_image_url" alt=""/>
                                                    <h3 class="mt-6 text-sm font-medium text-gray-900">{{
                                                            plant.seed.variety
                                                        }}</h3>
                                                    <dl class="mt-1 flex flex-grow flex-col justify-between">
                                                        <dt class="sr-only">Title</dt>
                                                        <dd class="text-sm text-gray-500">{{
                                                                plant.seed.name
                                                            }}
                                                        </dd>
                                                        <dt class="sr-only">Role</dt>
                                                        <dd class="mt-3">
                                                                <span
                                                                    class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">{{
                                                                        plant.quantity
                                                                    }}</span>
                                                        </dd>
                                                    </dl>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
