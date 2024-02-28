<script setup>
import dayjs from 'dayjs';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';

const props = defineProps({
    seed: Object,
});

const form = useForm({
    name: props.seed.name,
    variety: props.seed.variety,
    category: props.seed.category,
    link: props.seed.link,
    green_house: props.seed.green_house,
    sprouting_time_days_min: props.seed.sprouting_time_days_min,
    sprouting_time_days_max: props.seed.sprouting_time_days_max,
    sun: props.seed.sun,
    height: props.seed.height,
    seed_distance_min: props.seed.seed_distance_min,
    seed_distance_max: props.seed.seed_distance_max,
    seed_depth: props.seed.seed_depth,
    seeding_start: props.seed.seeding_start && dayjs(props.seed.seeding_start).format('YYYY-MM-DD'),
    seeding_end: props.seed.seeding_end && dayjs(props.seed.seeding_end).format('YYYY-MM-DD'),
    planting_start: props.seed.planting_start && dayjs(props.seed.planting_start).format('YYYY-MM-DD'),
    planting_end: props.seed.planting_end && dayjs(props.seed.planting_end).format('YYYY-MM-DD'),
    harvest_start: props.seed.harvest_start && dayjs(props.seed.harvest_start).format('YYYY-MM-DD'),
    harvest_end: props.seed.harvest_end && dayjs(props.seed.harvest_end).format('YYYY-MM-DD'),
    inventory_last_checked: props.seed.inventory_last_checked && dayjs(props.seed.inventory_last_checked).format('YYYY-MM-DD'),
    notes: props.seed.notes,
});
</script>

<template>
    <Head :title="'Edit ' + seed.name"/>

    <AuthenticatedLayout>
        <template #header>
            Edit {{ seed.name }}
        </template>

        <section>
            <form @submit.prevent="form.patch(route('seeds.update', seed.id))" class="mt-6 space-y-6">
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

                <div>
                    <InputLabel for="link" value="Link"/>

                    <TextInput
                        id="link"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.link"
                    />

                    <InputError class="mt-2" :message="form.errors.link"/>
                </div>

                <div>
                    <PrimaryButton :disabled="form.processing">Populate seed details from URL</PrimaryButton>
                </div>

                <div>
                    <div class="flex items-center">
                        <Checkbox id="green_house" name="green_house" class="mr-2" v-model:checked="form.green_house"/>
                        <InputLabel for="green_house" value="Should be planted in green house"/>
                    </div>

                    <InputError class="mt-2" :message="form.errors.green_house"/>
                </div>

                <div>
                    <InputLabel for="sprouting_time_days_min" value="Sprouting time (days)"/>

                    <div class="flex items-center">
                        <TextInput
                            id="sprouting_time_days_min"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.sprouting_time_days_min"
                        />
                        <div class="mx-2">-</div>
                        <TextInput
                            id="sprouting_time_days_max"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.sprouting_time_days_max"
                        />
                    </div>

                    <InputError class="mt-2" :message="form.errors.sprouting_time_days_min"/>
                    <InputError class="mt-2" :message="form.errors.sprouting_time_days_max"/>
                </div>

                <div>
                    <InputLabel for="sun" value="Sun"/>

                    <TextInput
                        id="sun"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.sun"
                    />

                    <InputError class="mt-2" :message="form.errors.sun"/>
                </div>

                <div>
                    <InputLabel for="height" value="Expected Height (cm)"/>

                    <TextInput
                        id="height"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.height"
                    />

                    <InputError class="mt-2" :message="form.errors.height"/>
                </div>

                <div>
                    <InputLabel for="seed_distance_min" value="Seeding distance (cm)"/>

                    <div class="flex items-center">
                        <TextInput
                            id="seed_distance_min"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.seed_distance_min"
                        />
                        <div class="mx-2">-</div>
                        <TextInput
                            id="seed_distance_max"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.seed_distance_max"
                        />
                    </div>

                    <InputError class="mt-2" :message="form.errors.seed_distance_min"/>
                    <InputError class="mt-2" :message="form.errors.seed_distance_max"/>
                </div>

                <div>
                    <InputLabel for="seed_depth" value="Seed depth (cm)"/>

                    <TextInput
                        id="seed_depth"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.seed_depth"
                    />

                    <InputError class="mt-2" :message="form.errors.seed_depth"/>
                </div>

                <div>
                    <InputLabel for="seeding_start" value="Seeding period"/>

                    <div class="flex items-center">
                        <TextInput
                            id="seeding_start"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.seeding_start"
                        />
                        <div class="mx-2">-</div>
                        <TextInput
                            id="seeding_end"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.seeding_end"
                        />
                    </div>

                    <InputError class="mt-2" :message="form.errors.seeding_start"/>
                    <InputError class="mt-2" :message="form.errors.seeding_end"/>
                </div>

                <div>
                    <InputLabel for="planting_start" value="Planting period"/>

                    <div class="flex items-center">
                        <TextInput
                            id="planting_start"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.planting_start"
                        />
                        <div class="mx-2">-</div>
                        <TextInput
                            id="planting_end"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.planting_end"
                        />
                    </div>

                    <InputError class="mt-2" :message="form.errors.planting_start"/>
                    <InputError class="mt-2" :message="form.errors.planting_end"/>
                </div>

                <div>
                    <InputLabel for="harvest_start" value="Harvest period"/>

                    <div class="flex items-center">
                        <TextInput
                            id="harvest_start"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.harvest_start"
                        />
                        <div class="mx-2">-</div>
                        <TextInput
                            id="harvest_end"
                            type="date"
                            class="mt-1 block w-full"
                            v-model="form.harvest_end"
                        />
                    </div>

                    <InputError class="mt-2" :message="form.errors.harvest_start"/>
                    <InputError class="mt-2" :message="form.errors.harvest_end"/>
                </div>

                <div>
                    <InputLabel for="inventory_last_checked" value="Inventory last checked date"/>

                    <TextInput
                        id="inventory_last_checked"
                        type="date"
                        class="mt-1 block w-full"
                        v-model="form.inventory_last_checked"
                    />

                    <InputError class="mt-2" :message="form.errors.inventory_last_checked"/>
                </div>

                <div>
                    <InputLabel for="notes" value="Notes"/>

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
