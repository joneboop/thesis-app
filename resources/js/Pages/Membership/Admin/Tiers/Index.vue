<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  tiers: Array,
})

const destroyTier = (id) => {
  if (!confirm('Delete this tier?')) return
  router.delete(route('membership.tiers.destroy', id), { preserveScroll: true })
}
</script>

<template>
  <AppLayout>
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold">Membership Tiers</h1>
        <p class="mt-1 text-sm text-gray-600">Manage ranks and minimum spend.</p>
      </div>

      <div class="flex items-center gap-2">
        <Link :href="route('membership.settings')" class="rounded-md border px-3 py-2 text-sm hover:bg-gray-50">
          Settings
        </Link>
        <Link :href="route('membership.tiers.create')" class="rounded-md bg-gray-900 px-3 py-2 text-sm text-white hover:bg-gray-800">
          New tier
        </Link>
      </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border bg-white">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
          <tr>
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Rank</th>
            <th class="px-4 py-3">Min spend</th>
            <th class="px-4 py-3">Active</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="t in tiers" :key="t.id" class="border-t">
            <td class="px-4 py-3 font-medium">{{ t.name }}</td>
            <td class="px-4 py-3">{{ t.rank }}</td>
            <td class="px-4 py-3">{{ Number(t.min_spend).toFixed(2) }}</td>
            <td class="px-4 py-3">{{ t.is_active ? 'Yes' : 'No' }}</td>
            <td class="px-4 py-3 text-right">
              <Link :href="route('membership.tiers.edit', t.id)" class="rounded-md border px-3 py-2 text-xs hover:bg-gray-50">
                Edit
              </Link>
              <button @click="destroyTier(t.id)" class="ml-2 rounded-md border px-3 py-2 text-xs hover:bg-gray-50">
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="tiers.length === 0">
            <td colspan="5" class="px-4 py-8 text-center text-gray-600">No tiers yet.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>