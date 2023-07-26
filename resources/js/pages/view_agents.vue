<script setup>
import { useStore } from 'vuex'
import { useRoute } from 'vue-router'
import { reactive } from 'vue'
import { city, agentStatus } from '@/constants/cities'

import axiosIns from '@/plugins/axios'


const store = useStore()
const route = useRoute()
const page = ref()
const length = ref()
const cancel = ref(false)
const show = ref(true)
const dialog = ref(false)
const agentList = ref([])


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

const searchQuery = ref('')

const filteredItems = computed(() => {
  return agentList.value.filter(item => {
    return item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) 
  })
})



const getAgentByPage = computed(()=>{
  return store.getters.getAgentByPage
})

const agentInfor = reactive({
  name: '',
  city: null,
  address: '',
  status: null,
})

const agent = reactive({
  status: null,
  from: null,
  to: null,
})

const reset = () =>{
  agent.status = null
  agent.from = null
  agent.to = null
}

const isActive = ref(false)

const updateForm = async id=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/agent/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    agentInfor.name = res.data.name
    agentInfor.city = res.data.city
    agentInfor.address = res.data.address
    agentInfor.status = res.data.status
    console.log(res.data)
  }).catch(err=>{
    console.log(err)
  })
}

const update = async id=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.post('/api/update/agent/' + id, agentInfor, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res)
    dialog.value = false
    alert.status = true
    alert.title = 'Cập nhật thành công'
    alert.text = 'Chi nhánh đã được Cập nhật thành công'
    alert.color = 'rgba(39, 217, 11, 0.8)'
  }).catch(err=>{
    console.log(agentInfor)
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

const confirm = () =>{
  console.log(agent)

  const accessToken = localStorage.getItem('accessToken')

  axiosIns.post(`api/agents_filter?page=${page.value}`, agent, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    isActive.value = false
    agentList.value = []
    agentList.value = res.data.data
    console.log(res.data)
  }).catch(err=>{
    console.log(err.data)
    error.status = true
    error.title = 'You have some errors'
    error.text = err.response.data.message
    error.color = 'rgba(222, 29, 29, 0.8)'
  })
}

const deleteAgent = id=>{
  const accessToken = localStorage.getItem('accessToken')

  axiosIns.get('/api/delete/agent/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res)
    cancel.value = false
    alert.status = true
    alert.title = 'Xóa thành công'
    alert.text = 'Chi nhánh đã được xóa thành công'
    alert.color = 'rgba(39, 217, 11, 0.8)'

    const index = agentList.value.findIndex(cat => cat.id === id)

    agentList.value.splice(index, 1)
  }).catch(err=>{
    console.log(err)
  })
}

// Call the action to retrieve the warehouse data and set the initial value of currentagentList to the result.

store.dispatch('getAgentByPage', page.value).then(() => {
  agentList.value.push(...getAgentByPage.value.data)
  console.log(agentList.value)
  length.value = Math.ceil(getAgentByPage.value.total / getAgentByPage.value.data.length)
})

// Call this function whenever the "page" value changes.
function updateagentList() {
  store.dispatch('getAgentByPage', page.value).then(() => {
    agentList.value = getAgentByPage.value.data
  })
}

watch(page, updateagentList)

watch(page, confirm)


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
        title="Tất cả tri nhánh"
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
              <VDialog
                v-model="isActive"
                transition="dialog-bottom-transition"
              >
                <template #activator="{ props }">
                  <VBtn
                    color="none"
                    v-bind="props"
                    icon="mdi-clock-time-eight-outline"
                  >
                    <VIcon icon="mdi-filter" />
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
                <VCard>
                  <VToolbar
                    color="primary"
                    title="Lọc chi nhánh"
                  />
                  <VCardText>
                    <VForm class="mt-6">
                      <VRow>
                        <!-- 👉 Status -->
                        <VCol
                          cols="12"
                          md="6"
                        >
                          <VSelect
                            v-model="agent.status"
                            label="Trạng thái"
                            :items="agentStatus"
                          />
                        </VCol>

                        <!-- 👉 From -->
                        <VCol
                          cols="12"
                          md="6"
                        >
                          <VTextField
                            v-model="agent.from"
                            type="date"
                            label="Từ ngày"
                          />
                        </VCol>

                        <!-- 👉 To -->
                        <VCol
                          cols="12"
                          md="6"
                        >
                          <VTextField
                            v-model="agent.to"
                            type="date"
                            label="Đến ngày"
                          />
                        </VCol>
                      </VRow>
                    </VForm>
                  </VCardText>
                  <VCardActions class="justify-end">
                    <VBtn
                      variant="text"
                      @click="isActive = false"
                    >
                      Đóng
                    </VBtn>
                    <VBtn
                      variant="text"
                      @click="reset"
                    >
                      Reset
                    </VBtn>
                    <VBtn
                      variant="text"
                      @click="confirm"
                    >
                      Xác nhận
                    </VBtn>
                  </VCardActions>
                </VCard>
              </VDialog>
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
                Tên chi nhánh
              </th>
              <th class="text-uppercase text-center">
                Thời gian tạo
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
              v-for="agent in filteredItems"
              :key="agent.agentList"
            >
              <td>
                {{ agent.id }}
              </td>
              <td class="text-center">
                {{ agent.name }}
              </td>
              <td class="text-center">
                {{ agent.created_at }}
              </td>
              <td class="text-center">
                {{ agent.status }}
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
                              @click="updateForm(agent.id)"
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
                            title="Cập nhật chi nhánh"
                          >
                            <VCardText>
                              <!-- 👉 Form -->
                              <VForm class="mt-6">
                                <VRow>
                                  <!-- 👉 Agent Name -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VTextField
                                      v-model="agentInfor.name"
                                      label="Tên chi nhánh"
                                    />
                                  </VCol>

                                  <!-- 👉 City -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VSelect
                                      v-model="agentInfor.city"
                                      label="Thành phố"
                                      :items="city"
                                    />
                                  </VCol>

                                  <!-- 👉 Address -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VTextField
                                      v-model="agentInfor.address"
                                      label="Địa chỉ"
                                    />
                                  </VCol>

                                  <!-- 👉 Status -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VSelect
                                      v-model="agentInfor.status"
                                      label="Trạng thái"
                                      :items="agentStatus"
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
                                @click="update(agent.id)"
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
                              @click="deleteForm(agent.id)"
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
                            title="Xóa chi nhánh"
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
                                @click="deleteAgent(agent.id)"
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