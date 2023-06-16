<script setup>
import { useStore } from 'vuex'
import { useRouter, useRoute } from 'vue-router'


const warehouseList = ref([])
const store = useStore()
const router = useRouter()
const route = useRoute()
const page = ref()
const length = ref()
const show = ref(true)
const dialog = ref(false)

const getWarehouseByPage = computed(()=>{
  return store.state.warehousesByPage
})

const update = id=>{
 
}

// Call the action to retrieve the warehouse data and set the initial value of currentWarehouseList to the result.

store.dispatch('getWarehouseByPage', page.value).then(() => {
  warehouseList.value.push(...getWarehouseByPage.value.data)
  console.log(warehouseList.value)
  length.value = Math.ceil(getWarehouseByPage.value.total / getWarehouseByPage.value.data.length)
})

// Call this function whenever the "page" value changes.
function updateWarehouseList() {
  store.dispatch('getWarehouseByPage', page.value).then(() => {
    warehouseList.value = getWarehouseByPage.value.data
  })
}

watch(page, updateWarehouseList)

const viewwarehouse = id =>{
  show.value = false
  router.push({ name: 'warehouse', params: { id: id } })
}

watchEffect(() => {
  const warehousePath = /^\/view_warehouse\/warehouse\/\d+$/

  show.value = !warehousePath.test(route.path)
})
</script>

<template>
  <RouterView />
  <VRow v-if="show">
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
                Status
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
                {{ warehouse.status }}
              </td>
              <td class="text-center">
                <VBtn
                  class="me-2"
                  icon="mdi-bell-outline"
                  color="none"
                >
                  <VIcon icon="mdi-dots-vertical" />
                  <VMenu
                    activator="parent"
                    location="right"
                  >
                    <VList>
                      <VListItem>
                        <VBtn 
                          icon="mdi-eye-outline"
                          color="none"
                          @click="viewwarehouse(warehouse.id)"
                        >
                          <VIcon icon="mdi-eye-outline" />
                          <VTooltip
                            activator="parent"
                            location="top"
                          >
                            View
                          </VTooltip>
                        </VBtn>
                      </VListItem>
                      <VListItem>
                        <VDialog
                          v-model="dialog"
                          persistent
                          width="auto"
                        >
                          <template #activator="{ props }">
                            <VBtn
                              color="none"
                              v-bind="props"
                              icon="mdi-pencil"
                              @click="update(warehouse.id)"
                            >
                              <VIcon icon="mdi-pencil" />
                              <VTooltip
                                activator="parent"
                                location="top"
                              >
                                Update
                              </VTooltip>
                            </VBtn>
                          </template>
                          <VCard>
                            <VCardTitle class="text-h5">
                              Update Warehouse 
                            </VCardTitle>
                            <VCardText>
                              Let Google help apps determine location. This means sending anonymous
                              location data to Google, even when no apps are running.
                            </VCardText>
                            <VCardActions>
                              <VSpacer />
                              <VBtn
                                color="green-darken-1"
                                variant="text"
                                @click="dialog = false"
                              >
                                Cancel
                              </VBtn>
                              <VBtn
                                color="green-darken-1"
                                variant="text"
                                @click="dialog = false"
                              >
                                Update
                              </VBtn>
                            </VCardActions>
                          </VCard>
                        </VDialog>
                      </VListItem>
                      <VListItem>
                        <VBtn 
                          icon="mdi-delete-empty"
                          color="none"
                        >
                          <VIcon icon="mdi-delete-empty" />
                          <VTooltip
                            activator="parent"
                            location="top"
                          >
                            Delete
                          </VTooltip>
                        </VBtn>
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