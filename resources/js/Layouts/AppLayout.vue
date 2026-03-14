<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
</script>

<template>
  <div class="min-h-screen bg-gray-50 text-gray-900">
    <header class="border-b bg-white">
      <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
        <Link href="/dashboard" class="font-semibold">
          CRM
        </Link>

        <div class="flex items-center gap-3 text-sm">
          <span class="text-gray-600">{{ page.props.auth?.user?.name }}</span>
          <Link href="/contacts" class="underline">Contacts</Link>
          <Link href="/logout" method="post" as="button" class="underline">
            Logout
          </Link>
          <Link v-if="page.props?.can?.orgManage" href="/settings/users" class="underline">
            Users
          </Link>
          <Link href="/deals" class="underline">Deals</Link>
                <Link
        :href="route('membership.customers.index')"
        class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm text-white hover:bg-gray-800"
      >
        Clients
      </Link>
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-6">
      <slot />
    </main>
  </div>
</template>