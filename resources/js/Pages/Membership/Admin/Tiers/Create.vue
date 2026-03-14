<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'

const form = useForm({
  name: '',
  rank: 1,
  min_spend: 0,
  is_active: true,
})

const save = () => form.post(route('membership.tiers.store'))
</script>

<template>
  <AppLayout>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Create Tier</h1>
      <Link :href="route('membership.tiers.index')" class="rounded-md border px-3 py-2 text-sm hover:bg-gray-50">Back</Link>
    </div>

    <div class="mt-6 max-w-xl rounded-lg border bg-white p-4">
      <div class="grid gap-4">
        <div>
          <label class="text-sm font-medium">Name</label>
          <input v-model="form.name" class="mt-1 w-full rounded-md border px-3 py-2 text-sm" />
          <div v-if="form.errors.name" class="mt-1 text-sm text-rose-700">{{ form.errors.name }}</div>
        </div>

        <div>
          <label class="text-sm font-medium">Rank</label>
          <input v-model="form.rank" type="number" class="mt-1 w-full rounded-md border px-3 py-2 text-sm" />
          <div v-if="form.errors.rank" class="mt-1 text-sm text-rose-700">{{ form.errors.rank }}</div>
        </div>

        <div>
          <label class="text-sm font-medium">Minimum spend</label>
          <input v-model="form.min_spend" type="number" step="0.01" class="mt-1 w-full rounded-md border px-3 py-2 text-sm" />
          <div v-if="form.errors.min_spend" class="mt-1 text-sm text-rose-700">{{ form.errors.min_spend }}</div>
        </div>

        <label class="inline-flex items-center gap-2 text-sm">
          <input type="checkbox" v-model="form.is_active" />
          Active
        </label>

        <button @click="save" :disabled="form.processing" class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white hover:bg-gray-800 disabled:opacity-60">
          Create
        </button>
      </div>
    </div>
  </AppLayout>
</template>