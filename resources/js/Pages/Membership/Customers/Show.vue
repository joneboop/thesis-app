<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link, router, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  customer: Object,
  tiers: Array,
})

const page = usePage()
const orgName = computed(() => page.props?.auth?.organization?.name ?? '—')

const fullName = computed(() =>
  [props.customer.first_name, props.customer.last_name].filter(Boolean).join(' ')
)

const formatMoney = (n) => {
  const num = Number(n ?? 0)
  return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const membership = computed(() => props.customer.membership ?? null)

const overrideForm = useForm({
  override_tier_id: membership.value?.override_tier_id ?? null,
  override_until: membership.value?.override_until ?? null,
  override_reason: membership.value?.override_reason ?? null,
})

const setOverride = () => {
  overrideForm.post(route('membership.customers.override', props.customer.id), {
    preserveScroll: true,
  })
}

const clearOverride = () => {
  router.delete(route('membership.customers.override.clear', props.customer.id), {
    preserveScroll: true,
  })
}

const recalc = () => {
  router.post(route('membership.customers.recalculate', props.customer.id), {}, { preserveScroll: true })
}
</script>

<template>
  <AppLayout>
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold">Customer Membership</h1>
        <p class="mt-1 text-sm text-gray-600">Org: {{ orgName }}</p>
      </div>

      <div class="flex items-center gap-2">
        <Link
          :href="route('membership.customers.index')"
          class="rounded-md border px-3 py-2 text-sm hover:bg-gray-50"
        >
          Back
        </Link>
        <button
          @click="recalc"
          class="rounded-md bg-gray-900 px-3 py-2 text-sm text-white hover:bg-gray-800"
        >
          Recalculate
        </button>
      </div>
    </div>

    <div class="mt-6 rounded-lg border bg-white p-4">
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
          <div class="text-lg font-semibold">{{ fullName || '—' }}</div>
          <div class="text-sm text-gray-600">{{ customer.email || '—' }}</div>
        </div>

        <div class="text-right">
          <div class="text-xs uppercase tracking-wide text-gray-500">Current tier</div>
          <div class="mt-1 inline-flex items-center rounded-full border px-3 py-1 text-sm">
            {{ membership?.current_tier?.name ?? '—' }}
          </div>
        </div>
      </div>

      <div class="mt-4 grid gap-3 sm:grid-cols-3">
        <div class="rounded-md border p-3">
          <div class="text-xs text-gray-500">Spend (window)</div>
          <div class="mt-1 text-base font-semibold">
            {{ formatMoney(membership?.window_spend ?? 0) }}
          </div>
        </div>
        <div class="rounded-md border p-3">
          <div class="text-xs text-gray-500">Evaluated at</div>
          <div class="mt-1 text-sm text-gray-700">
            {{ membership?.evaluated_at ?? '—' }}
          </div>
        </div>
        <div class="rounded-md border p-3">
          <div class="text-xs text-gray-500">Downgrade eligible</div>
          <div class="mt-1 text-sm" :class="membership?.downgrade_eligible_at ? 'text-rose-700' : 'text-gray-700'">
            {{ membership?.downgrade_eligible_at ?? '—' }}
          </div>
        </div>
      </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
      <!-- Override -->
      <div class="rounded-lg border bg-white p-4">
        <h2 class="text-lg font-semibold">Manual override</h2>
        <p class="mt-1 text-sm text-gray-600">
          When override is set, automatic tier changes should be blocked (in evaluator logic).
        </p>

        <div class="mt-4 grid gap-3">
          <div>
            <label class="text-sm font-medium">Override tier</label>
            <select v-model="overrideForm.override_tier_id" class="mt-1 w-full rounded-md border px-3 py-2 text-sm">
              <option :value="null">— None —</option>
              <option v-for="t in tiers" :key="t.id" :value="t.id">
                {{ t.name }} (rank {{ t.rank }})
              </option>
            </select>
          </div>

          <div>
            <label class="text-sm font-medium">Override until (optional)</label>
            <input
              v-model="overrideForm.override_until"
              type="date"
              class="mt-1 w-full rounded-md border px-3 py-2 text-sm"
            />
          </div>

          <div>
            <label class="text-sm font-medium">Reason (optional)</label>
            <textarea
              v-model="overrideForm.override_reason"
              rows="3"
              class="mt-1 w-full rounded-md border px-3 py-2 text-sm"
              placeholder="Why is this customer overridden?"
            />
          </div>

          <div class="flex flex-wrap gap-2">
            <button
              @click="setOverride"
              :disabled="overrideForm.processing"
              class="rounded-md bg-gray-900 px-3 py-2 text-sm text-white hover:bg-gray-800 disabled:opacity-60"
            >
              Save override
            </button>

            <button
              @click="clearOverride"
              class="rounded-md border px-3 py-2 text-sm hover:bg-gray-50"
              type="button"
            >
              Clear override
            </button>
          </div>

          <div v-if="overrideForm.errors?.override_tier_id" class="text-sm text-rose-700">
            {{ overrideForm.errors.override_tier_id }}
          </div>
        </div>
      </div>

      <!-- History -->
      <div class="rounded-lg border bg-white p-4">
        <h2 class="text-lg font-semibold">Membership history</h2>
        <p class="mt-1 text-sm text-gray-600">Latest events (max 50).</p>

        <div class="mt-4 space-y-3">
          <div
            v-for="e in customer.membership_events"
            :key="e.id"
            class="rounded-md border p-3"
          >
            <div class="flex items-start justify-between gap-4">
              <div class="font-medium">{{ e.type }}</div>
              <div class="text-xs text-gray-500">{{ e.created_at }}</div>
            </div>

            <div class="mt-1 text-sm text-gray-700">
              <span v-if="e.from_tier?.name || e.to_tier?.name">
                {{ e.from_tier?.name ?? '—' }} → {{ e.to_tier?.name ?? '—' }}
              </span>
              <span v-else>—</span>
            </div>

            <div v-if="e.window_spend !== null" class="mt-1 text-xs text-gray-600">
              Spend: {{ formatMoney(e.window_spend) }}
            </div>
          </div>

          <div v-if="!customer.membership_events?.length" class="text-sm text-gray-600">
            No events yet.
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>