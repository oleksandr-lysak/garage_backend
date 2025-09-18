<script setup lang="ts">
import MasterCard from '@/Components/MasterCard.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="container columns-6 flex-row text-right">
                <div class="mr-4">
                    <SecondaryButton>dsadasd</SecondaryButton>
                </div>
                <div class="mr-4">
                    <SecondaryButton>dsadasd</SecondaryButton>
                </div>
            </div>
        </template>

        <div class="mx-auto max-w-7xl py-0 sm:px-6 lg:px-8">
            <MasterCard
                v-for="(item, index) in 40"
                :key="index"
                :master-id="index"
                imageUrl="https://images.unsplash.com/photo-1612838320302-4b3b3b3b3b3b"
                :description="item.toString()"
                address="Testimonial"
                name="Name"
                phone="Position"
                :age="5"
                :rating="5"
                slug="test-slug"
                :available="true"
            />
        </div>
    </AuthenticatedLayout>
</template>
