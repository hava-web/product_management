<script setup>
import { useRoute } from 'vue-router'
import AddWarehouse from '@/views/pages/warehouse/add_warehouse.vue'

const route = useRoute()
const activeTab = ref(route.params.tab)
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

<style scoped>
  .alert{
  position: absolute;
  top: 20px;
  right: 10px;
  z-index: 100;
}

.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.8s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
</style>