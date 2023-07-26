<script setup>
import { useStore } from 'vuex'
import { useRouter, useRoute } from 'vue-router'
import { reactive } from 'vue'
import { roles } from '@/constants/roles'
import axiosIns from '@/plugins/axios'


const userList = ref([])
const store = useStore()
const router = useRouter()
const route = useRoute()
const page = ref()
const length = ref()
const cancel = ref(false)
const show = ref(true)
const dialog = ref(false)
const warehouseList = ref([])

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

const getAllWarehouse = computed(()=> store.getters.getAllWarehouse)

onMounted(async () => {
  await store.dispatch('getAllWarehouse')
  warehouseList.value.push(...getAllWarehouse.value)
  console.log(getAllWarehouse.value)
})

const getId = warehouse => warehouse.id

const formatName = warehouse=>{
  return  warehouse.id + ' - ' + warehouse.name
}

const getEmployeesByPage = computed(()=>{
  return store.getters.getemployeesByPage
})

const userInfor = reactive({
  id: null,
  image: null,
  firstname: '',
  lastname: '',
  username: '',
  email: '',
  warehouse_id: null,
  user_id: '',
  phone: '',
  date_of_birth: '',
  role: null,
  salary: '',
  city: null,
  address: '',
}) 

const updateForm = async id=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/employee/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    userInfor.id = res.data.id
    userInfor.image = res.data.image
    userInfor.firstname = res.data.firstname
    userInfor.lastname = res.data.lastname
    userInfor.city = res.data.city
    userInfor.warehouse_id = res.data.warehouse_id
    userInfor.user_id =res.data.user_id
    userInfor.phone = res.data.phone
    userInfor.date_of_birth = res.data.date_of_birth
    userInfor.salary = res.data.salary
    userInfor.city = res.data.city
    userInfor.address = res.data.address
    console.log(res.data)
  }).catch(err=>{
    console.log(err)
  })
}

const searchQuery = ref('')

const filteredItems = computed(() => {
  return userList.value.filter(item => {
    return item.firstname.toLowerCase().includes(searchQuery.value.toLowerCase()) 
  })
})

const update = async id=>{
  const accessToken = localStorage.getItem('accessToken')

  console.log(userInfor)
  await axiosIns.post('/api/update/employee/' + id, userInfor, {
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
    console.log(userInfor)
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

const deleteEmp = id=>{
  const accessToken = localStorage.getItem('accessToken')

  axiosIns.get('/api/delete/employee/' + id, {
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

// Call the action to retrieve the warehouse data and set the initial value of currentuserList to the result.

store.dispatch('getEmployeesByPage', page.value).then(() => {
  userList.value.push(...getEmployeesByPage.value.data)
  console.log(userList.value)
  length.value = Math.ceil(getEmployeesByPage.value.total / getEmployeesByPage.value.data.length)
})

// Call this function whenever the "page" value changes.
function updateuserList() {
  store.dispatch('getEmployeesByPage', page.value).then(() => {
    userList.value = getEmployeesByPage.value.data
  })
}

watch(page, updateuserList)

const viewEmployee = id =>{
  show.value = false
  router.push({ name: 'employee', params: { id: id } })
  console.log(id)
}

watchEffect(() => {
  const employeePath = /^\/view_employee\/employee\/\d+$/

  show.value = !employeePath.test(route.path)
})

watchEffect( async ()=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/user/' + userInfor.user_id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    userInfor.role = res.data.role
    userInfor.username = res.data.name
    userInfor.email = res.data.email
    console.log(res.data)
  }).catch(err=>{
    console.log(err)
  })
}, { immediate: true })
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
        title="Tất cả nhân viên"
        prepend-icon="mdi-store-plus-outline"
      >
        <template #append>
          <div class="me-n3 tool">
            <VCol
              cols="auto"
              class="d-flex"
            >
              <VTextField
                v-model="searchQuery"
                title="Search"
                class="mx-3"
                prepend-inner-icon="mdi-magnify"
                placeholder="Search"
              />
            </VCol>
          </div>
        </template> 
        <VDivider />
        <VTable>
          <thead>
            <tr>
              <th class="text-uppercase">
                ID
              </th>
              <th class="text-uppercase text-center">
                Tên đầy đủ
              </th>
              <th class="text-uppercase text-center">
                Ngày tạo tài khoản
              </th>
              <th class="text-uppercase text-center">
                Ảnh
              </th>
              <th class="text-uppercase text-center">
                Cài đặt
              </th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="user in filteredItems"
              :key="user.userList"
            >
              <td>
                {{ user.id }}
              </td>
              <td class="text-center">
                {{ user.lastname + ' ' + user.firstname }}
              </td>
              <td class="text-center">
                {{ user.created_at }}
              </td>
              <td class="text-center">
                <div class="image">
                  <VImg
                    width="20"
                    class="item-image"
                    content-class="text-center"
                    cover
                    :src="user.image"
                  />
                </div>
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
                          @click="viewEmployee(user.id)"
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
                              @click="updateForm(user.id)"
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
                            title=" Cập nhật nhân viên"
                          >
                            <VCardText>
                              <!-- 👉 Form -->
                              <VForm class="mt-6">
                                <VRow>
                                  <!-- 👉 Warehouse -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VSelect
                                      v-model="userInfor.warehouse_id"
                                      label="Kho"
                                      :items="warehouseList"
                                      :item-title="formatName"
                                      :item-value="getId"
                                    />
                                  </VCol>

                                  <!-- 👉 Role -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VSelect
                                      v-model="userInfor.role"
                                      label="Chức vụ"
                                      :items="roles"
                                    />
                                  </VCol>

                                  <!-- 👉 Salary -->
                                  <VCol cols="12">
                                    <VTextField
                                      v-model="userInfor.salary"
                                      label="Lương"
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
                                Hủy bỏ
                              </VBtn>
                              <VBtn
                                color="primary"
                                variant="elevated"
                                prepend-icon="mdi-pencil-outline"
                                @click="update(user.id)"
                              >
                                Cập nhật 
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
                              @click="deleteForm(user.id)"
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
                            title="Xóa nhân viên"
                          >
                            <VCardText>
                              Bạn có chắc chắn là bạn muốn xóa thông tin này không ?
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
                                @click="deleteEmp(user.id)"
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
.image{
  display: flex;
}
.item-image{
  margin: 10px;
}
.tool{
  width: 400px;
}
</style>