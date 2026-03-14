<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const orgName = computed(() => page.props?.auth?.organization?.name ?? '—')
</script>

<template>
  <AppLayout>
    <h1 class="text-2xl font-semibold">Dashboard</h1>
    <p class="mt-2">Org: {{ orgName }}</p>
    <p class="mt-2 text-gray-700">You’re in.</p>
  </AppLayout>
</template>