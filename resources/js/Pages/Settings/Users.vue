<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { router, usePage } from '@inertiajs/vue3'

const page = usePage()

defineProps({
  users: Array,
  roles: Array,
})

function changeRole(userId, role) {
  router.patch(route('org.users.update', userId), { role }, { preserveScroll: true })
}

function removeUser(userId) {
  if (!confirm('Remove this user from the organization?')) return
  router.delete(route('org.users.destroy', userId), { preserveScroll: true })
}
</script>

<template>
  <AppLayout>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Org Users</h1>
      <div class="text-sm text-gray-600">
        Org: {{ page.props?.auth?.organization?.name ?? '—' }}
      </div>
    </div>

    <div class="mt-6 bg-white border rounded overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left p-2">Name</th>
            <th class="text-left p-2">Email</th>
            <th class="text-left p-2">Role</th>
            <th class="text-left p-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="u in users" :key="u.id" class="border-t">
            <td class="p-2">{{ u.name }}</td>
            <td class="p-2">{{ u.email }}</td>

            <td class="p-2">
              <select class="border rounded px-2 py-1"
                      :value="u.role"
                      @change="changeRole(u.id, $event.target.value)">
                <option v-for="r in roles" :key="r" :value="r">
                  {{ r }}
                </option>
              </select>
            </td>

            <td class="p-2">
              <button class="underline" @click="removeUser(u.id)">
                Remove
              </button>
            </td>
          </tr>

          <tr v-if="users.length === 0">
            <td class="p-2 text-gray-500" colspan="4">No users.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>