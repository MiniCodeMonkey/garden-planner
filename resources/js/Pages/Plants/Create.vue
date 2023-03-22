<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import dayjs from 'dayjs'
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';

const props = defineProps({
    seed: Object,
});

const form = useForm({
    seed_id: props.seed.id,
    date: dayjs().format('YYYY-MM-DD'),
    quantity: 10,
    location: '',
    notes: '',
});
</script>

<template>
    <Head title="Start new plant"/>

    <AuthenticatedLayout>
        <template #header>
            Start new plant of
        </template>

        <section>
            <header>
                <h2 class="text-lg font-medium text-gray-900">
                    Record plant of {{ seed.variety }} {{ seed.name }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    This let's you track its progress from seed to plant.
                </p>
            </header>

            <form @submit.prevent="form.post(route('plants.store'))" class="mt-6 space-y-6">
                <input type="hidden" name="seed_id" :value="seed.id"/>
                <InputError class="mt-2" :message="form.errors.seed_id"/>

                <div>
                    <InputLabel for="date" value="Date"/>

                    <TextInput
                        id="date"
                        type="date"
                        class="mt-1 block w-full"
                        v-model="form.date"
                        required
                        autofocus
                        autocomplete="date"
                    />

                    <InputError class="mt-2" :message="form.errors.date"/>
                </div>

                <div>
                    <InputLabel for="quantity" value="Quantity"/>

                    <TextInput
                        id="quantity"
                        type="number"
                        class="mt-1 block w-full"
                        v-model="form.quantity"
                        required
                        autofocus
                    />

                    <InputError class="mt-2" :message="form.errors.quantity"/>
                </div>

                <div>
                    <InputLabel for="location" value="Location"/>

                    <TextInput
                        id="location"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.location"
                        required
                        autofocus
                        autocomplete="location"
                        placeholder="E.g. Cell Tray row 5 and 6"
                    />

                    <InputError class="mt-2" :message="form.errors.location"/>
                </div>

                <div>
                    <InputLabel for="text" value="Notes"/>

                    <TextArea
                        id="notes"
                        class="mt-1 block w-full"
                        v-model="form.notes"
                    />

                    <InputError class="mt-2" :message="form.errors.notes"/>
                </div>

                <div class="flex items-center gap-4">
                    <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                    <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                    </Transition>
                </div>
            </form>
        </section>

    </AuthenticatedLayout>
</template>
