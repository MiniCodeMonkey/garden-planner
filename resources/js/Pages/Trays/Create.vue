<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    type: '',
    rows: 10,
    columns: 6,
});
</script>

<template>
    <Head title="Add new seed tray"/>

    <AuthenticatedLayout>
        <template #header>
            Add new seed tray
        </template>

        <section>
            <header>
                <h2 class="text-lg font-medium text-gray-900">Let's start with some basic information</h2>

                <p class="mt-1 text-sm text-gray-600">
                    You can add more information later.
                </p>
            </header>

            <form @submit.prevent="form.post(route('trays.store'))" class="mt-6 space-y-6">
                <div>
                    <InputLabel for="name" value="Name"/>

                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                        placeholder="E.g. A"
                    />

                    <InputError class="mt-2" :message="form.errors.name"/>
                </div>

                <div>
                    <InputLabel for="type" value="Type"/>

                    <TextInput
                        id="type"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.type"
                        placeholder="E.g. Charles Dowding CD60"
                    />

                    <InputError class="mt-2" :message="form.errors.type"/>
                </div>

                <div>
                    <InputLabel for="rows" value="Rows"/>

                    <TextInput
                        id="rows"
                        type="number"
                        class="mt-1 block w-full"
                        v-model="form.rows"
                        placeholder="E.g. 10"
                    />

                    <InputError class="mt-2" :message="form.errors.rows"/>
                </div>

                <div>
                    <InputLabel for="columns" value="Columns"/>

                    <TextInput
                        id="columns"
                        type="number"
                        class="mt-1 block w-full"
                        v-model="form.columns"
                        placeholder="E.g. 10"
                    />

                    <InputError class="mt-2" :message="form.errors.columns"/>
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
