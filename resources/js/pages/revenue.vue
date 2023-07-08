<script setup>
import { useStore } from 'vuex'
import { useRoute, useRouter } from 'vue-router'
import { reactive } from 'vue'
import axiosIns from '@/plugins/axios'


const store = useStore()
const route = useRoute()
const page = ref()
const length = ref()
const cancel = ref(false)
const show = ref(true)
const dialog = ref(false)
const orderList = ref([])

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

const viewOrder = id =>{
  router.push({ name: 'order', params: { id: id } })
  console.log(id)
}

const getReceivedOrderByPage = computed(()=>{
  return store.getters.getReceivedOrderByPage
})

const colorInfor = reactive({
  name: '',
  code_color: '',
})


// Call the action to retrieve the warehouse data and set the initial value of currentorderList to the result.

store.dispatch('getReceivedOrderByPage', page.value).then(() => {
  orderList.value.push(...getReceivedOrderByPage.value.data)
  console.log(orderList.value)
  length.value = Math.ceil(getReceivedOrderByPage.value.total / getReceivedOrderByPage.value.data.length)
})

// Call this function whenever the "page" value changes.
function updateorderList() {
  store.dispatch('getReceivedOrderByPage', page.value).then(() => {
    orderList.value = getReceivedOrderByPage.value.data
  })
}

watch(page, updateorderList)


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
        title="All Received Orders"
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
                Total price
              </th>
              <th class="text-uppercase text-center">
                Created Time
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
              v-for="order in orderList"
              :key="order.orderList"
            >
              <td>
                {{ order.id }}
              </td>
              <td class="text-center">
                ${{ order.total_price }}
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