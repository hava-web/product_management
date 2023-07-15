<script setup>
import { useStore } from 'vuex'
import { useRoute, useRouter } from 'vue-router'
import { reactive } from 'vue'
import AnalyticsNumberBuy from '../views/customer/AnalyticsNumberBuy.vue'
import AnalyticsByMonth from '../views/customer/AnalyticsByMonth.vue'


const store = useStore()
const route = useRoute()
const page = ref()
const length = ref()
const router = useRouter()
const show = ref(true)
const inventoryList = ref([])


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


const getInventoryByPage = computed(()=>{
  return store.getters.getInventoryByPage
})

const viewProduct = id=>{
  show.value = false
  router.push({ name: 'product', params: { id: id } })
}

// Call the action to retrieve the warehouse data and set the initial value of currentinventoryList to the result.

store.dispatch('getInventoryByPage', page.value).then(() => {
  inventoryList.value.push(...getInventoryByPage.value.data)
  console.log(inventoryList.value)
  length.value = Math.ceil(getInventoryByPage.value.total / getInventoryByPage.value.data.length)
})

// Call this function whenever the "page" value changes.
function updateinventoryList() {
  store.dispatch('getInventoryByPage', page.value).then(() => {
    inventoryList.value = getInventoryByPage.value.data
  })
}

watch(page, updateinventoryList)


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
        title="Inventories"
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
                Product
              </th>
              <th class="text-uppercase text-center">
                Brand
              </th>
              <th class="text-uppercase text-center">
                Size
              </th>
              <th class="text-uppercase text-center">
                Color
              </th>
              <th class="text-uppercase text-center">
                Quantity
              </th>
              <th class="text-uppercase text-center">
                Action
              </th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="product in inventoryList"
              :key="product.inventoryList"
            >
              <td>
                {{ product.id }}
              </td>
              <td class="text-center">
                {{ product.product }}
              </td>
              <td class="text-center">
                {{ product.brand }}
              </td>
              <td class="text-center">
                {{ product.size }}
              </td>
              <td class="text-center ">
                <VSheet
                  :class="model"
                  class="color"
                  elevation="12"
                  rounded="circle"
                  :color="product.colors"
                  height="50"
                  width="50"
                />
              </td>
              <td class="text-center">
                {{ product.quantity }}
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
                          @click="viewProduct(product.id)"
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
      <AnalyticsNumberBuy />
      <AnalyticsByMonth />
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
    align-items: center;
    justify-content: center
}
</style>