<script setup>
import { useStore } from 'vuex'
import { useRoute } from 'vue-router'
import { reactive } from 'vue'
import axiosIns from '@/plugins/axios'


const store = useStore()
const route = useRoute()
const page = ref()
const length = ref()
const cancel = ref(false)
const show = ref(true)
const dialog = ref(false)
const sizeList = ref([])


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



const getSizeByPage = computed(()=>{
  return store.getters.getSizeByPage
})

const sizeInfor = reactive({
  name: '',
  status: null,
})

const searchQuery = ref('')

const filteredItems = computed(() => {
  return sizeList.value.filter(item => {
    return item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) 
  })
})

const format = size => size.status == 0 ? "Không hiển thị" : "Hiển thị"



const updateForm = async id=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/size/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    sizeInfor.name = res.data.name
    sizeInfor.status = res.data.status
    console.log(res.data)
  }).catch(err=>{
    console.log(err)
  })
}

const update = async id=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.post('/api/update/size/' + id, sizeInfor, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res)
    dialog.value = false
    alert.status = true
    alert.title = 'Cập nhật thành công'
    alert.text = 'Kích thước đã được Cập nhật thành công'
    alert.color = 'rgba(39, 217, 11, 0.8)'
  }).catch(err=>{
    console.log(sizeInfor)
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

const deleteSize = id=>{
  const accessToken = localStorage.getItem('accessToken')

  axiosIns.get('/api/delete/size/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res)
    cancel.value = false
    alert.status = true
    alert.title = 'Xóa thành công'
    alert.text = 'Kích thước đã được Xóa thành công'
    alert.color = 'rgba(39, 217, 11, 0.8)'

    const index = sizeList.value.findIndex(cat => cat.id === id)

    sizeList.value.splice(index, 1)
  }).catch(err=>{
    console.log(err)
  })
}

// Call the action to retrieve the warehouse data and set the initial value of currentsizeList to the result.

store.dispatch('getSizeByPage', page.value).then(() => {
  sizeList.value.push(...getSizeByPage.value.data)
  console.log(sizeList.value)
  length.value = Math.ceil(getSizeByPage.value.total / getSizeByPage.value.data.length)
})

// Call this function whenever the "page" value changes.
function updatesizeList() {
  store.dispatch('getSizeByPage', page.value).then(() => {
    sizeList.value = getSizeByPage.value.data
  })
}

watch(page, updatesizeList)


watchEffect(() => {
  const employeePath = /^\/view_employee\/employee\/\d+$/

  show.value = !employeePath.test(route.path)
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
        title="All Sizes"
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
                Kích thước
              </th>
              <th class="text-uppercase text-center">
                Ngày tạo
              </th>
              <th class="text-uppercase text-center">
                Trạng thái
              </th>
              <th class="text-uppercase text-center">
                Cài đặt
              </th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="size in filteredItems"
              :key="size.sizeList"
            >
              <td>
                {{ size.id }}
              </td>
              <td class="text-center">
                {{ size.name }}
              </td>
              <td class="text-center">
                {{ size.created_at }}
              </td>
              <td class="text-center">
                {{ format(size) }}
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
                              @click="updateForm(size.id)"
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
                            title="Cập nhật kích thước"
                          >
                            <VCardText>
                              <!-- 👉 Form -->
                              <VForm class="mt-6">
                                <VRow>
                                  <!-- 👉 Color Name -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VTextField
                                      v-model="sizeInfor.name"
                                      prepend-icon="mdi-rename"
                                      label="Tên kích thước"
                                    />
                                  </VCol>

                                  <!-- 👉 Color Code -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VSwitch
                                      v-model="sizeInfor.status"
                                      :true-value="1"
                                      :false-value="0"
                                      prepend-icon="mdi-list-status"
                                      label="Trạng thái"
                                      color="primary"
                                      :value="status"
                                      hide-details
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
                                @click="update(size.id)"
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
                              @click="deleteForm(color.id)"
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
                            title="Xóa kích thước"
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
                                Hủy bỏ
                              </VBtn>
                              <VBtn
                                color="red"
                                prepend-icon="mdi-trash-can-outline"
                                variant="elevated"
                                @click="deleteSize(size.id)"
                              >
                                Xóa
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

.color{
    margin-left: 10px;
    align-items: center;
}
.tool{
  width: 400px;
}
</style>