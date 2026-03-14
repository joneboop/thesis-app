<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  settings: Object,
})

const form = useForm({
  window_days: props.settings.window_days,
  grace_days: props.settings.grace_days,
})

const save = () => {
  form.put(route('membership.settings.update'))
}
</script>

<template>
  <AppLayout>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Membership Settings</h1>
      <Link :href="route('membership.customers.index')" class="rounded-md border px-3 py-2 text-sm hover:bg-gray-50">
        Back
      </Link>
    </div>

    <div class="mt-6 max-w-xl rounded-lg border bg-white p-4">
      <div class="grid gap-4">
        <div>
          <label class="text-sm font-medium">Window days</label>
          <input v-model="form.window_days" type="number" class="mt-1 w-full rounded-md border px-3 py-2 text-sm" />
          <div v-if="form.errors.window_days" class="mt-1 text-sm text-rose-700">{{ form.errors.window_days }}</div>
        </div>

        <div>
          <label class="text-sm font-medium">Grace days</label>
          <input v-model="form.grace_days" type="number" class="mt-1 w-full rounded-md border px-3 py-2 text-sm" />
          <div v-if="form.errors.grace_days" class="mt-1 text-sm text-rose-700">{{ form.errors.grace_days }}</div>
        </div>

        <button
          @click="save"
          :disabled="form.processing"
          class="rounded-md bg-gray-900 px-4 py-2 text-sm text-white hover:bg-gray-800 disabled:opacity-60"
        >
          Save
        </button>
      </div>
    </div>
  </AppLayout>
</template>