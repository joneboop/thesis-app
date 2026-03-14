<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useForm, usePage, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const page = usePage()

defineProps({
  deals: Object,
})

const createForm = useForm({
  title: '',
  value: 0,      // euros
  status: 'open',
})

function createDeal() {
  createForm.post('/deals', {
    preserveScroll: true,
    onSuccess: () => createForm.reset('title', 'value'),
  })
}

// inline edit (minimal)
const editing = ref(null)
const editForm = useForm({
  title: '',
  value: 0,
  status: 'open',
})

function startEdit(d) {
  editing.value = d.id
  editForm.title = d.title
  editForm.value = d.value ?? 0
  editForm.status = d.status ?? 'open'
}

function cancelEdit() {
  editing.value = null
  editForm.reset()
}

function saveEdit(id) {
  editForm.patch(`/deals/${id}`, {
    preserveScroll: true,
    onSuccess: () => cancelEdit(),
  })
}

function removeDeal(id) {
  if (!confirm('Delete this deal?')) return
  router.delete(`/deals/${id}`, { preserveScroll: true })
}

function formatEUR(value) {
  const n = Number(value ?? 0)
  try {
    return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'EUR' }).format(n)
  } catch {
    return `${n.toFixed(2)} EUR`
  }
}
</script>

<template>
  <AppLayout>
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold">Deals</h1>
        <div class="text-sm text-gray-600">
          Org: {{ page.props?.auth?.organization?.name ?? '—' }}
        </div>
      </div>
    </div>

    <div v-if="page.props?.can?.orgWrite" class="mt-6 p-4 bg-white border rounded">
      <h2 class="font-semibold mb-3">New deal</h2>

      <form class="grid grid-cols-1 md:grid-cols-3 gap-3" @submit.prevent="createDeal">
        <div class="md:col-span-2">
          <label class="text-sm">Title</label>
          <input class="w-full border rounded px-2 py-1" v-model="createForm.title" />
          <div class="text-sm text-red-600" v-if="createForm.errors.title">{{ createForm.errors.title }}</div>
        </div>

        <div>
          <label class="text-sm">Value (€)</label>
          <input class="w-full border rounded px-2 py-1" type="number" min="0" step="0.01" v-model.number="createForm.value" />
          <div class="text-sm text-red-600" v-if="createForm.errors.value">{{ createForm.errors.value }}</div>
        </div>

        <div>
          <label class="text-sm">Status</label>
          <select class="w-full border rounded px-2 py-1" v-model="createForm.status">
            <option value="open">open</option>
            <option value="won">won</option>
            <option value="lost">lost</option>
          </select>
        </div>

        <div class="md:col-span-3">
          <button class="border rounded px-3 py-1" type="submit" :disabled="createForm.processing">
            Create
          </button>
        </div>
      </form>
    </div>

    <div v-else class="mt-6 p-4 bg-yellow-50 border rounded">
      You have read-only access. You can view deals but cannot create/edit/delete them.
    </div>

    <div class="mt-6 bg-white border rounded overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left p-2">Title</th>
            <th class="text-left p-2">Value</th>
            <th class="text-left p-2">Status</th>
            <th class="text-left p-2" v-if="page.props?.can?.orgWrite">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="d in deals.data" :key="d.id" class="border-t align-top">
            <td class="p-2">
              <template v-if="editing === d.id">
                <input class="border rounded px-2 py-1 w-full" v-model="editForm.title" />
              </template>
              <template v-else>
                {{ d.title }}
              </template>
            </td>

            <td class="p-2">
              <template v-if="editing === d.id">
                <input class="border rounded px-2 py-1 w-full" type="number" min="0" step="0.01" v-model.number="editForm.value" />
              </template>
              <template v-else>
                {{ formatEUR(d.value) }}
              </template>
            </td>

            <td class="p-2">
              <template v-if="editing === d.id">
                <select class="border rounded px-2 py-1" v-model="editForm.status">
                  <option value="open">open</option>
                  <option value="won">won</option>
                  <option value="lost">lost</option>
                </select>
              </template>
              <template v-else>
                {{ d.status }}
              </template>
            </td>

            <td class="p-2" v-if="page.props?.can?.orgWrite">
              <div class="flex gap-3">
                <button v-if="editing !== d.id" class="underline" @click="startEdit(d)">Edit</button>
                <template v-else>
                  <button class="underline" :disabled="editForm.processing" @click="saveEdit(d.id)">Save</button>
                  <button class="underline" @click="cancelEdit">Cancel</button>
                </template>
                <button class="underline text-red-600" @click="removeDeal(d.id)">Delete</button>
              </div>
            </td>
          </tr>

          <tr v-if="deals.data.length === 0">
            <td class="p-2 text-gray-500" :colspan="page.props?.can?.orgWrite ? 4 : 3">No deals yet.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>