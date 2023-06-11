<script setup>
import { useRoute } from 'vue-router'
import AddWarehouse from '@/views/pages/warehouse/add_warehouse.vue'

const route = useRoute()
const activeTab = ref(route.params.tab)

// tabs
const tabs = [
  {
    title: 'Account',
    icon: 'mdi-account-outline',
    tab: 'account',
  },
  {
    title: 'Security',
    icon: 'mdi-lock-open-outline',
    tab: 'security',
  },
  {
    title: 'Notifications',
    icon: 'mdi-bell-outline',
    tab: 'notification',
  },
]
</script>

<template>
  <div>
    <VWindow
      v-model="activeTab"
      class=" disable-tab-transition"
    >
      <!-- Account -->
      <VWindowItem value="account">
        <AddWarehouse />
      </VWindowItem>
    </vwindow>
  </div>
</template>