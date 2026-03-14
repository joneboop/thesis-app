<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  customers: Object, // paginator
  filters: Object,
})

const page = usePage()
const orgName = computed(() => page.props?.auth?.organization?.name ?? '—')


const q = ref(props.filters?.q ?? '')


const createDemoCustomer = (tier) => {
  router.post(route('membership.demo.create', tier))
}

// small debounce so it doesn’t spam requests
let t = null
watch(q, (val) => {
  clearTimeout(t)
  t = setTimeout(() => {
    router.get(
      route('membership.customers.index'),
      { q: val || undefined },
      { preserveState: true, replace: true, preserveScroll: true }
    )
  }, 250)
})

const formatMoney = (n) => {
  const num = Number(n ?? 0)
  return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const fullName = (c) => [c.first_name, c.last_name].filter(Boolean).join(' ')
</script>

<template>
  <AppLayout>
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold">Membership</h1>
        <p class="mt-1 text-sm text-gray-600">Org: {{ orgName }}</p>
      </div>

      <div class="flex items-center gap-2">
        <Link
          :href="route('membership.settings')"
          class="rounded-md border px-3 py-2 text-sm hover:bg-gray-50"
        >
          Settings
        </Link>
        <Link
          :href="route('membership.tiers.index')"
          class="rounded-md border px-3 py-2 text-sm hover:bg-gray-50"
        >
          Tiers
        </Link>
      </div>
    </div>

    <div class="mt-6 flex items-center gap-3">
      <input
        v-model="q"
        type="text"
        placeholder="Search customers (name/email)…"
        class="w-full max-w-md rounded-md border px-3 py-2 text-sm"
      />
      <button
        @click="() => { q = '' }"
        class="rounded-md border px-3 py-2 text-sm hover:bg-gray-50"
      >
        Clear
      </button>
    </div>
    
    <div class="flex gap-2">

  <button
    @click="createDemoCustomer('bronze')"
    class="rounded-md bg-yellow-600 px-3 py-2 text-sm text-white hover:bg-yellow-700"
  >
    + Bronze Customer
  </button>

  <button
    @click="createDemoCustomer('silver')"
    class="rounded-md bg-gray-500 px-3 py-2 text-sm text-white hover:bg-gray-600"
  >
    + Silver Customer
  </button>

  <button
    @click="createDemoCustomer('gold')"
    class="rounded-md bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-700"
  >
    + Gold Customer
  </button>

</div>

    <div class="mt-4 overflow-hidden rounded-lg border bg-white">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">
          <tr>
            <th class="px-4 py-3">Customer</th>
            <th class="px-4 py-3">Tier</th>
            <th class="px-4 py-3">Spend (window)</th>
            <th class="px-4 py-3">Evaluated</th>
            <th class="px-4 py-3 text-right">Action</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="c in customers.data" :key="c.id" class="border-t">
            <td class="px-4 py-3">
              <div class="font-medium">{{ fullName(c) || '—' }}</div>
              <div class="text-gray-600">{{ c.email || '—' }}</div>
            </td>

            <td class="px-4 py-3">
              <span
                class="inline-flex items-center rounded-full border px-2 py-1 text-xs"
              >
                {{ c.membership?.current_tier?.name ?? '—' }}
              </span>

              <div v-if="c.membership?.override_tier_id" class="mt-1 text-xs text-amber-700">
                Override
              </div>
              <div v-if="c.membership?.downgrade_eligible_at" class="mt-1 text-xs text-rose-700">
                At risk
              </div>
            </td>

            <td class="px-4 py-3">
              {{ formatMoney(c.membership?.window_spend ?? 0) }}
            </td>

            <td class="px-4 py-3 text-gray-600">
              {{ c.membership?.evaluated_at ?? '—' }}
            </td>

            <td class="px-4 py-3 text-right">
              <Link
                :href="route('membership.customers.show', c.id)"
                class="rounded-md border px-3 py-2 text-xs hover:bg-gray-50"
              >
                View
              </Link>
            </td>
          </tr>

          <tr v-if="customers.data.length === 0">
            <td colspan="5" class="px-4 py-8 text-center text-gray-600">
              No customers found.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="customers.links?.length" class="mt-4 flex flex-wrap gap-2">
      <Link
        v-for="(link, idx) in customers.links"
        :key="idx"
        :href="link.url || ''"
        class="rounded-md border px-3 py-2 text-sm"
        :class="[
          link.active ? 'bg-gray-900 text-white border-gray-900' : 'hover:bg-gray-50',
          !link.url ? 'pointer-events-none opacity-50' : ''
        ]"
        v-html="link.label"
        preserve-scroll
      />
    </div>
  </AppLayout>
</template>