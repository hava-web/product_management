<script setup>
import axiosIns from '@/plugins/axios'
import { useStore } from 'vuex'

const warehouseList = ref([])
const store = useStore()
const page = ref()
const length = ref(2)

const getWarehouseByPage = computed(()=>{
  return store.state.warehousesByPage
})

// Call the action to retrieve the warehouse data and set the initial value of currentWarehouseList to the result.
const currentWarehouseList = computed(()=>{
  return getWarehouseByPage.value
})

store.dispatch('getWarehouseByPage', page.value).then(() => {
  warehouseList.value.push(...getWarehouseByPage.value.data)
  console.log(warehouseList.value)
})

// Call this function whenever the "page" value changes.
function updateWarehouseList() {
  store.dispatch('getWarehouseByPage', page.value).then(() => {
    warehouseList.value = getWarehouseByPage.value.data
  })
}

watch(page, updateWarehouseList)
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard 
        title="All Warehouse"
        prepend-icon="mdi-store-plus-outline"
      >
        <VDivider />
        <VTable>
          <thead>
            <tr>
              <th class="text-uppercase">
                ID
              </th>
              <th class="text-uppercase text-center">
                Name
              </th>
              <th class="text-uppercase text-center">
                Setup Time
              </th>
              <th class="text-uppercase text-center">
                Action
              </th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="warehouse in warehouseList"
              :key="warehouse.warehouseList"
            >
              <td>
                {{ warehouse.id }}
              </td>
              <td class="text-center">
                {{ warehouse.name }}
              </td>
              <td class="text-center">
                {{ warehouse.created_at }}
              </td>
              <td class="text-center">
                <VBtn
                  class="me-2"
                  icon="mdi-bell-outline"
                  color="none"
                >
                  <VMenu
                    activator="parent"
                    location="right"
                  >
                    <VList>
                      <VListItem>
                        Hello
                      </VListItem>
                    </VList>
                  </VMenu>
                </VBtn>
              </td>
            </tr>
          </tbody>
        </VTable>
      </VCard>
      <VPagination
        v-model="page"
        :length="length"
        rounded="circle"
      />
    </VCol>
  </VRow>
</template>