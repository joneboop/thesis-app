<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const page = usePage()

const props = defineProps({
  contacts: Object,
  filters: Object,
})

const search = ref(props.filters?.search ?? '')

// debounce-ish: update after a small delay
let t = null
watch(search, (val) => {
  clearTimeout(t)
  t = setTimeout(() => {
    router.get('/contacts', { ...props.filters, search: val }, { preserveState: true, replace: true })
  }, 250)
})

const createForm = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  company: '',
})

function create() {
  createForm.post('/contacts', { onSuccess: () => createForm.reset() })
}

// EDIT
const editing = ref(null)
const editForm = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  company: '',
})

function startEdit(c) {
  editing.value = c.id
  editForm.first_name = c.first_name
  editForm.last_name = c.last_name
  editForm.email = c.email
  editForm.phone = c.phone
  editForm.company = c.company
}

function cancelEdit() {
  editing.value = null
  editForm.reset()
}

function saveEdit(id) {
  editForm.patch(`/contacts/${id}`, {
    preserveScroll: true,
    onSuccess: () => cancelEdit(),
  })
}

function removeContact(id) {
  if (!confirm('Delete this contact?')) return
  router.delete(`/contacts/${id}`, { preserveScroll: true })
}

function sortBy(field) {
  const dir = (props.filters?.sort === field && props.filters?.dir === 'asc') ? 'desc' : 'asc'
  router.get('/contacts', { ...props.filters, sort: field, dir }, { preserveState: true, replace: true })
}
</script>

<template>
  <AppLayout>
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-semibold">Contacts</h1>
        <div class="text-sm text-gray-600">
          Org: {{ page.props?.auth?.organization?.name ?? '—' }}
        </div>
      </div>

      <input
        v-model="search"
        placeholder="Search contacts..."
        class="border rounded px-3 py-2 w-72"
      />
    </div>

    <!-- Create -->
    <div v-if="page.props?.can?.orgWrite" class="mt-6 p-4 bg-white border rounded">
      <h2 class="font-semibold mb-3">New contact</h2>

      <form class="grid grid-cols-1 md:grid-cols-2 gap-3" @submit.prevent="create">
        <div>
          <label class="text-sm">First name</label>
          <input class="w-full border rounded px-2 py-1" v-model="createForm.first_name" />
          <div class="text-sm text-red-600" v-if="createForm.errors.first_name">{{ createForm.errors.first_name }}</div>
        </div>

        <div>
          <label class="text-sm">Last name</label>
          <input class="w-full border rounded px-2 py-1" v-model="createForm.last_name" />
        </div>

        <div>
          <label class="text-sm">Email</label>
          <input class="w-full border rounded px-2 py-1" v-model="createForm.email" />
          <div class="text-sm text-red-600" v-if="createForm.errors.email">{{ createForm.errors.email }}</div>
        </div>

        <div>
          <label class="text-sm">Phone</label>
          <input class="w-full border rounded px-2 py-1" v-model="createForm.phone" />
        </div>

        <div>
          <label class="text-sm">Company</label>
          <input class="w-full border rounded px-2 py-1" v-model="createForm.company" />
        </div>

        <div class="md:col-span-2">
          <button class="border rounded px-3 py-1" type="submit" :disabled="createForm.processing">
            Create
          </button>
        </div>
      </form>
    </div>

    <div v-else class="mt-6 p-4 bg-yellow-50 border rounded">
      You have read-only access. You can view contacts but cannot create/edit/delete them.
    </div>

    <!-- Table -->
    <div class="mt-6 bg-white border rounded overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left p-2 cursor-pointer" @click="sortBy('first_name')">Name</th>
            <th class="text-left p-2 cursor-pointer" @click="sortBy('email')">Email</th>
            <th class="text-left p-2">Phone</th>
            <th class="text-left p-2 cursor-pointer" @click="sortBy('company')">Company</th>
            <th class="text-left p-2 cursor-pointer" @click="sortBy('created_at')">Created</th>
            <th class="text-left p-2" v-if="page.props?.can?.orgWrite">Actions</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="c in contacts.data" :key="c.id" class="border-t align-top">
            <td class="p-2">
              <template v-if="editing === c.id">
                <input class="border rounded px-2 py-1 w-full" v-model="editForm.first_name" placeholder="First name" />
                <input class="border rounded px-2 py-1 w-full mt-2" v-model="editForm.last_name" placeholder="Last name" />
              </template>
              <template v-else>
                {{ c.first_name }} {{ c.last_name }}
              </template>
            </td>

            <td class="p-2">
              <template v-if="editing === c.id">
                <input class="border rounded px-2 py-1 w-full" v-model="editForm.email" placeholder="Email" />
              </template>
              <template v-else>
                {{ c.email }}
              </template>
            </td>

            <td class="p-2">
              <template v-if="editing === c.id">
                <input class="border rounded px-2 py-1 w-full" v-model="editForm.phone" placeholder="Phone" />
              </template>
              <template v-else>
                {{ c.phone }}
              </template>
            </td>

            <td class="p-2">
              <template v-if="editing === c.id">
                <input class="border rounded px-2 py-1 w-full" v-model="editForm.company" placeholder="Company" />
              </template>
              <template v-else>
                {{ c.company }}
              </template>
            </td>

            <td class="p-2 text-gray-600">
              {{ new Date(c.created_at).toLocaleDateString() }}
            </td>

            <td class="p-2" v-if="page.props?.can?.orgWrite">
              <div class="flex gap-3">
                <button v-if="editing !== c.id" class="underline" @click="startEdit(c)">Edit</button>

                <template v-else>
                  <button class="underline" :disabled="editForm.processing" @click="saveEdit(c.id)">Save</button>
                  <button class="underline" @click="cancelEdit">Cancel</button>
                </template>

                <button class="underline text-red-600" @click="removeContact(c.id)">Delete</button>
              </div>

              <div class="text-sm text-red-600 mt-2" v-if="editing === c.id && Object.keys(editForm.errors).length">
                Please fix the highlighted fields.
              </div>
            </td>
          </tr>

          <tr v-if="contacts.data.length === 0">
            <td class="p-2 text-gray-500" :colspan="page.props?.can?.orgWrite ? 6 : 5">No contacts found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex gap-2 flex-wrap">
      <button
        v-for="link in contacts.links"
        :key="link.label"
        :disabled="!link.url"
        class="border rounded px-3 py-1"
        :class="{ 'font-semibold': link.active, 'opacity-50': !link.url }"
        v-html="link.label"
        @click="link.url && router.visit(link.url, { preserveState: true, preserveScroll: true })"
      />
    </div>
  </AppLayout>
</template>