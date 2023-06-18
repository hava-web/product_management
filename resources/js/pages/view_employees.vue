<script setup>
import { useStore } from 'vuex'
import { useRouter, useRoute } from 'vue-router'
import { reactive } from 'vue'
import axiosIns from '@/plugins/axios'


const warehouseList = ref([])
const store = useStore()
const router = useRouter()
const route = useRoute()
const page = ref()
const length = ref()
const cancel = ref(false)
const show = ref(true)
const dialog = ref(false)
const managerList = ref([])

const alert = reactive({
  status: false,
  title: '',
  text: '',
  color: '',
})

const error = reactive({
  status: false,
  title: '',
  text: '',
  color: '',
})

const getAllUser = computed(() => store.getters.getAllUser)

onMounted( async () => {
  await store.dispatch('getAllUser') 
  managerList.value.push(...getAllUser.value)
  console.log(managerList.value)
})

const getId = warehouse => warehouse.id

const formatName = manager=>{
  return  manager.id + ' - ' + manager.name
}



const getWarehouseByPage = computed(()=>{
  return store.state.warehousesByPage
})

const warehouseInfo = reactive({
  id: null,
  name: '',
  manager: null,
  city: '',
  status: '',
  address: '',
}) 

const city = [
  'Ho Chi Minh City',
  'Hanoi',
  'Da Nang',
  'Can Tho',
  'Hai Phong',
  'Nha Trang',
  'Hue',
  'Ba Ria',
  'Vung Tau',
  'Qui Nhon',
  'Rach Gia',
  'Sa Dec',
  'Vinh',
  'Ha Tinh',
  'Thai Nguyen',
  'Lang Son',
  'Dien Bien Phu',
  'Da Lat',
  'Pleiku',
  'Phan Thiet',
  'Ha Long',
  'Tam Ky',
]

const status = [
  'Open',
  'Closed',
  'Modifing',
]

const updateForm = async id=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/warehouse/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    warehouseInfo.id = res.data.id
    warehouseInfo.name = res.data.name
    warehouseInfo.manager = res.data.manager
    warehouseInfo.city = res.data.city
    warehouseInfo.status = res.data.status
    warehouseInfo.address = res.data.address
    console.log(res.data)
  }).catch(err=>{
    console.log(err)
  })
}

const update = async id=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.post('/api/update/warehouse/' + id, warehouseInfo, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res)
    dialog.value = false
    alert.status = true
    alert.title = 'Updated Successfully'
    alert.text = 'Warehouse Updated Successfully'
    alert.color = 'rgba(39, 217, 11, 0.8)'
  }).catch(err=>{
    console.log(err)
    error.status = true
    error.title = 'You have some errors'
    error.text = err.response.data.message
    error.color = 'rgba(222, 29, 29, 0.8)'
  })
} 

const deleteForm = id=>{
  console.log(id)
}

const deleteWarehouse = id=>{
  const accessToken = localStorage.getItem('accessToken')

  axiosIns.get('/api/delete/warehouse/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res)
    cancel.value = false
    alert.status = true
    alert.title = 'Deleted Successfully'
    alert.text = 'Warehouse deleted Successfully'
    alert.color = 'rgba(39, 217, 11, 0.8)'
  }).catch(err=>{
    console.log(err)
  })
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
  <Transition name="slide-fade">
    <VAlert 
      v-if="alert.status"
      :color="alert.color"
      icon="mdi-check-circle-outline"
      :title="alert.title"
      closable
      class="alert"
      max-width="400px"
      :text="alert.text"
      @click:close="alert.status = false"
    />
  </Transition>
  <VRow v-if="show">
    <VCol cols="12">
      <VCard 
        title="All Employees"
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
                          width="800px"
                        >
                          <template #activator="{ props }">
                            <VBtn
                              color="none"
                              v-bind="props"
                              icon="mdi-pencil"
                              @click="updateForm(warehouse.id)"
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
                          <Transition name="slide-fade">
                            <VAlert 
                              v-if="error.status"
                              :color="error.color"
                              icon="mdi-alert"
                              :title="error.title"
                              closable
                              class="alert"
                              max-width="400px"
                              :text="error.text"
                              @click:close="error.status = false"
                            />
                          </Transition>
                          <VCard
                            prepend-icon="mdi-store-edit"
                            title=" Update Warehouse "
                          >
                            <VDivider />

                            <VCardText>
                              <!-- 👉 Form -->
                              <VForm class="mt-6">
                                <VRow>
                                  <!-- 👉 Warehouse Name -->
                                  <VCol
                                    md="6"
                                    cols="12"
                                  >
                                    <VTextField 
                                      v-model="warehouseInfo.name"
                                      label="Warehouse Name"
                                    />
                                  </VCol>

                                  <!-- 👉 Manager -->
                                  <VCol
                                    md="6"
                                    cols="12"
                                  >
                                    <VSelect
                                      v-model="warehouseInfo.manager"
                                      :items="managerList"
                                      :item-value="getId"
                                      :item-title="formatName"
                                      label="Manager"
                                    />
                                  </VCol>

                                  <!-- 👉 City -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VSelect
                                      v-model="warehouseInfo.city"
                                      :items="city"
                                      label="City"
                                    />
                                  </VCol>

                                  <!-- 👉 Status -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VSelect
                                      v-model="warehouseInfo.status"
                                      :items="status"
                                      label="Status"
                                    />
                                  </VCol>

                                  <!-- 👉 Address -->
                                  <VCol cols="12">
                                    <VTextField 
                                      v-model="warehouseInfo.address"
                                      label="Address"
                                    />
                                  </VCol>
                                </VRow>
                              </VForm>
                            </VCardText>
                            <VCardActions>
                              <VSpacer />
                              <VBtn
                                color="red"
                                variant="elevated"
                                class="btn-cancel"
                                prepend-icon="mdi-close"
                                @click="dialog = false"
                              >
                                Cancel
                              </VBtn>
                              <VBtn
                                color="primary"
                                variant="elevated"
                                prepend-icon="mdi-pencil-outline"
                                @click="update(warehouseInfo.id)"
                              >
                                Update
                              </VBtn>
                            </VCardActions>
                          </VCard>
                        </VDialog>
                      </VListItem>
                      <VListItem>
                        <VDialog
                          v-model="cancel"
                          width="auto"
                        >
                          <template #activator="{ props }">
                            <VBtn 
                              icon="mdi-delete-empty"
                              v-bind="props"
                              color="none"
                              @click="deleteForm(warehouse.id)"
                            >
                              <VIcon icon="mdi-delete-empty" />
                              <VTooltip
                                activator="parent"
                                location="top"
                              >
                                Delete
                              </VTooltip>
                            </VBtn>
                          </template>
                          <VCard
                            prepend-icon="mdi-alert"
                            title="Do you want delete this warehouse ?"
                          >
                            <VCardText>
                              Once you delete this warehouse you can not get this warehouse information again. Are you sure you want delete this ?
                            </VCardText>
                            <VCardActions>
                              <VSpacer />
                              <VBtn
                                color="green-darken-1"
                                prepend-icon="mdi-close"
                                variant="elevated"
                                @click="cancel = false"
                              >
                                Cancel
                              </VBtn>
                              <VBtn
                                color="red"
                                prepend-icon="mdi-trash-can-outline"
                                variant="elevated"
                                @click="deleteWarehouse(warehouse.id)"
                              >
                                Delete
                              </VBtn>
                            </VCardActions>
                          </VCard>
                        </VDialog>
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

<style scoped>
.btn-cancel{
  background-color: #E53935;
  color: white;
}
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