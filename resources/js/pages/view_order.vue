<script setup>
import { useStore } from 'vuex'
import { useRoute, useRouter } from 'vue-router'
import { reactive } from 'vue'
import { orderStatus } from '@/constants/cities'
import Barchart from '@/views/order/barchart.vue'
import Areachart from '@/views/order/areachart.vue'
import axiosIns from '@/plugins/axios'


const store = useStore()
const route = useRoute()
const page = ref()
const length = ref()
const show = ref(true)
const orderList = ref([])


const isActive = ref(false)

const router = useRouter()


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

const order = reactive({
  status: null,
  from: null,
  to: null,
})

const viewOrder = id =>{
  show.value = false
  router.push({ name: 'order', params: { id: id } })
  console.log(id)
}

const reset = () =>{
  order.status = null
  order.from = null
  order.to = null
}

const confirm = () =>{
  console.log(order)

  const accessToken = localStorage.getItem('accessToken')

  axiosIns.post(`api/orders_filter?page=${page.value}`, order, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    isActive.value = false
    orderList.value = []
    orderList.value = res.data.data
    console.log(res.data)
  }).catch(err=>{
    console.log(err.data)
    error.status = true
    error.title = 'You have some errors'
    error.text = err.response.data.message
    error.color = 'rgba(222, 29, 29, 0.8)'
  })
}

const getOrderByPage = computed(()=>{
  return store.getters.getOrderByPage
})

// Call the action to retrieve the warehouse data and set the initial value of currentorderList to the result.

store.dispatch('getOrderByPage', page.value).then(() => {
  orderList.value.push(...getOrderByPage.value.data)
  console.log(orderList.value)
  length.value = Math.ceil(getOrderByPage.value.total / getOrderByPage.value.data.length)
})

// Call this function whenever the "page" value changes.
function updateorderList() {
  store.dispatch('getOrderByPage', page.value).then(() => {
    orderList.value = getOrderByPage.value.data
  })
}

watch(page, updateorderList)

watch(page, confirm)


watchEffect(() => {
  const employeePath = /^\/view_order\/order\/\d+$/

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
        title="Tất cả đơn hàng"
        prepend-icon="mdi-store-plus-outline"
      >
        <template #append>
          <div class="me-n3 tool">
            <VCol
              cols="auto"
              class="d-flex"
            >
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
                    title="Lọc đơn hàng"
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
                            v-model="order.status"
                            label="Trạng thái"
                            :items="orderStatus"
                          />
                        </VCol>

                        <!-- 👉 From -->
                        <VCol
                          cols="12"
                          md="6"
                        >
                          <VTextField
                            v-model="order.from"
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
                            v-model="order.to"
                            type="date"
                            label="Đên ngày"
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
                Tổng giá trị
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
              v-for="order in orderList"
              :key="order.orderList"
            >
              <td>
                {{ order.id }}
              </td>
              <td class="text-center">
                {{ order.total_price }} VND
              </td>
              <td class="text-center">
                {{ order.created_at }}
              </td>
              <td class="text-center">
                {{ order.status }}
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
                          @click="viewOrder(order.id)"
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
    <VCol>
      <div class="d-flex">
        <Barchart class="w-50" />
        <Areachart class="w-50" />
      </div>
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
</style>