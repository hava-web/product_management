<script setup>
import { useStore } from 'vuex'
import { useRoute, useRouter } from 'vue-router'
import { reactive } from 'vue'
import AnalyticsNumberBuy from '../views/customer/AnalyticsNumberBuy.vue'
import AnalyticsByMonth from '../views/customer/AnalyticsByMonth.vue'
import Areachart from '@/views/customer/areachart.vue'

const store = useStore()
const route = useRoute()
const page = ref()
const length = ref()
const router = useRouter()
const cancel = ref(false)
const show = ref(true)
const dialog = ref(false)
const customerList = ref([])


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
  return customerList.value.filter(item => {
    const  firstname = item.firstname.toLowerCase().includes(searchQuery.value.toLowerCase())
    const  lastname = item.lastname.toLowerCase().includes(searchQuery.value.toLowerCase())
    
    return firstname || lastname
  })
})


const getCustomerByPage = computed(()=>{
  return store.getters.getCustomerByPage
})

const viewCustomer = id=>{
  show.value = false
  router.push({ name: 'customer', params: { id: id } })
}

// Call the action to retrieve the warehouse data and set the initial value of currentcustomerList to the result.

store.dispatch('getCustomerByPage', page.value).then(() => {
  customerList.value.push(...getCustomerByPage.value.data)
  console.log(customerList.value)
  length.value = Math.ceil(getCustomerByPage.value.total / getCustomerByPage.value.data.length)
})

// Call this function whenever the "page" value changes.
function updatecustomerList() {
  store.dispatch('getCustomerByPage', page.value).then(() => {
    customerList.value = getCustomerByPage.value.data
  })
}

watch(page, updatecustomerList)


watchEffect(() => {
  const employeePath = /^\/view_customer\/customer\/\d+$/

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
        title="Tất cả khách hàng"
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
                Họ
              </th>
              <th class="text-uppercase text-center">
                Tên
              </th>
              <th class="text-uppercase text-center">
                Số lần mua
              </th>
              <th class="text-uppercase text-center">
                Số điện thoại
              </th>
              <th class="text-uppercase text-center">
                Cài đặt
              </th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="customer in filteredItems"
              :key="customer.customerList"
            >
              <td>
                {{ customer.id }}
              </td>
              <td class="text-center">
                {{ customer.lastname }}
              </td>
              <td class="text-center">
                {{ customer.firstname }}
              </td>
              <td class="text-center">
                {{ customer.numbers_of_purchases }}
              </td>
              <td class="text-center">
                {{ customer.phone }}
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
                          @click="viewCustomer(customer.id)"
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
    <VCol
      cols="12"
      class="d-flex"
    >
      <AnalyticsNumberBuy :customers="customerList" />
      <AnalyticsByMonth />
    </VCol>
    <VCol cols="12">
      <Areachart />
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