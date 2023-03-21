<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: '',
    variety: '',
    category: '',
});
</script>

<template>
    <Head title="Add new seed"/>

    <AuthenticatedLayout>
        <template #header>
            Add new seed
        </template>

        <section>
            <header>
                <h2 class="text-lg font-medium text-gray-900">Let's start with some basic information</h2>

                <p class="mt-1 text-sm text-gray-600">
                    You can add more information later.
                </p>
            </header>

            <form @submit.prevent="form.post(route('seeds.store'))" class="mt-6 space-y-6">
                <div>
                    <InputLabel for="name" value="Name"/>

                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="E.g. Green Bean"
                    />

                    <InputError class="mt-2" :message="form.errors.name"/>
                </div>

                <div>
                    <InputLabel for="variety" value="Variety"/>

                    <TextInput
                        id="variety"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.variety"
                        placeholder="E.g. Cobra"
                    />

                    <InputError class="mt-2" :message="form.errors.variety"/>
                </div>

                <div>
                    <InputLabel for="category" value="Category"/>

                    <TextInput
                        id="category"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.category"
                        placeholder="E.g. Herb"
                    />

                    <InputError class="mt-2" :message="form.errors.category"/>
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
