<script setup>
import axiosIns from '@/plugins/axios'
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'


const store = useStore()
const productList = ref([])
const categoryList = ref([])
const colorList = ref([])
const warehouseList = ref([])
const brandList = ref([])
const sizeList = ref([])
const agentList = ref([])
const router = useRouter()
const searchQuery = ref('')

const error = reactive({
  status: false,
  title: '',
  text: '',
  color: '',
})


const isActive = ref(false)

const product = reactive({
  brand: null,
  size: null,
  color: null,
  agent: null,
  warehouse: null,
  category: null,
  from: null,
  to: null,
}) 

const reset = () =>{
  product.category = null
  product.brand = null
  product.size = null
  product.color = null
  product.agent = null
  product.warehouse = null
  product.from = null
  product.to = null
}

const filteredItems = computed(() => {
  return productList.value.filter(item => {
    const  productName = item.product.toLowerCase().includes(searchQuery.value.toLowerCase())
    const productCode = item.product_code && typeof item.product_code === 'string' && item.product_code.toString().includes(searchQuery.value)

    return productName || productCode
  })
})



const getAllWarehouse = computed(()=> store.getters.getAllWarehouse)
const getCategories = computed(()=> store.getters.getCategories)
const getBrands = computed(()=> store.getters.getBrands)
const getColors = computed(() => store.getters.getColors)
const getSizes = computed(() => store.getters.getSizes)
const getAgents = computed(() => store.getters.getAgents)

onMounted(async () => {
  await store.dispatch('getAllWarehouse')
  warehouseList.value.push(...getAllWarehouse.value)
  console.log(getAllWarehouse.value)
})

onMounted(async () => {
  await store.dispatch('getCategories')
  categoryList.value.push(...getCategories.value)
  console.log(getCategories.value)
})

onMounted(async () => {
  await store.dispatch('getBrands')
  brandList.value.push(...getBrands.value)
  console.log(getBrands.value)
})

onMounted(async () => {
  await store.dispatch('getColors')
  colorList.value.push(...getColors.value)
  console.log(getColors.value)
})

onMounted(async () => {
  await store.dispatch('getSizes')
  sizeList.value.push(...getSizes.value)
  console.log(getSizes.value)
})

onMounted(async () => {
  await store.dispatch('getAgents')
  agentList.value.push(...getAgents.value)
  console.log(getAgents.value)
})

const getId = warehouse => warehouse.id

const formatName = warehouse=>{
  return warehouse.id + ' - ' + warehouse.name
}

onMounted(async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/all_products_properties', {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    productList.value.push(...res.data)
    console.log(productList.value)
  }).catch(err=>{
    console.log(err)
  })
})

const confirm = () =>{
  console.log(product)

  const accessToken = localStorage.getItem('accessToken')

  axiosIns.post('api/filter_product', product, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    isActive.value = false
    productList.value = []
    productList.value.push(...res.data)
    console.log(res.data)
  }).catch(err=>{
    console.log(err.data)
    error.status = true
    error.title = 'Bạn có lỗi'
    error.text = err.response.data.message
    error.color = 'rgba(222, 29, 29, 0.8)'
  })
}

const productView = id=>{
  router.push({ name: 'product', params: { id: id } })
  console.log(id)
}


const headers = [
  'ID',
  'TÊN SẢN PHẨM',
  'CODE',
  'ẢNH',
  'THƯƠNG HIỆU',
  'KÍCH THƯỚC',
  'TÊN MÀU',
  'MÀU',
  'SỐ LƯỢNG',
]
</script>

<template>
  <VCard>
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
                title="Lọc sản phẩm"
              />
              <VCardText>
                <VForm class="mt-6">
                  <VRow>
                    <!-- 👉 Category -->
                    <VCol
                      cols="12"
                      md="6"
                    >
                      <VSelect
                        v-model="product.category"
                        label="Danh mục"
                        :items="categoryList"
                        :item-title="formatName"
                        :item-value="getId"
                      />
                    </VCol>

                    <!-- 👉 brand -->
                    <VCol
                      cols="12"
                      md="6"
                    >
                      <VSelect
                        v-model="product.brand"
                        label="Thương hiệu"
                        :items="brandList"
                        :item-title="formatName"
                        :item-value="getId"
                      />
                    </VCol>

                    <!-- 👉 size -->
                    <VCol
                      cols="12"
                      md="6"
                    >
                      <VSelect
                        v-model="product.size"
                        label="Kích thước"
                        :items="sizeList"
                        :item-title="formatName"
                        :item-value="getId"
                      />
                    </VCol>
                    <div class="d-flex flex-wrap color ">
                      <VRadioGroup
                        v-model="product.color"
                        inline
                        class="group"
                      >
                        <VCol
                          v-for="color in colorList"
                          :key="color.colorList"
                          md="3"
                          cols="2"
                        >
                          <div class="d-flex">
                            <VSheet
                              class="mt-2 mb-2 "
                              elevation="12"
                              rounded="circle"
                              :color="color.code_color"
                              height="50"
                              width="50"
                            />
                            <div class="lable pa-4">
                              <div class="lable-title">
                                {{ color.name }}
                              </div>
                              <VRadio
                                class="pe-2"
                                :value="color.id"
                              />
                            </div>
                          </div>
                        </VCol>
                      </VRadioGroup>
                    </div>
                    <!-- 👉 warehouse -->
                    <VCol
                      cols="12"
                      md="6"
                    >
                      <VSelect
                        v-model="product.warehouse"
                        label="Kho hàng"
                        :items="warehouseList"
                        :item-title="formatName"
                        :item-value="getId"
                      />
                    </VCol>
                    <!-- 👉 Agent -->
                    <VCol
                      cols="12"
                      md="6"
                    >
                      <VSelect
                        v-model="product.agent"
                        label="Chi nhánh"
                        :items="agentList"
                        :item-title="formatName"
                        :item-value="getId"
                      />
                    </VCol>
                    <!-- 👉 From -->
                    <VCol
                      cols="12"
                      md="6"
                    >
                      <VTextField
                        v-model="product.from"
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
                        v-model="product.to"
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
    <VTable
      :headers="headers"
      item-key="fullName"
      class="table-rounded"
      hide-default-footer
      disable-sort
    >
      <thead>
        <tr>
          <th
            v-for="header in headers"
            :key="header"
          >
            {{ header }}
          </th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="row in filteredItems"
          :key="row"
          class="user"
          @click="productView(row.id)"
        >
          <!-- name -->

          <td>
            <div class="d-flex flex-column">
              <h6 class="text-sm font-weight-medium">
                {{ row.id }}
              </h6>
            </div>
          </td>

          <td
            class="text-sm"
            v-text="row.product"
          />
          <td
            class="text-sm"
            v-text="row.product_code"
          />
          <td>
            <VImg
              :width="50"
              class="item-image"
              alt="Image"
              content-class="text-center"
              cover
              :src="'/storage/images/' + row.image"
            />
          </td>
          <td
            class="text-sm"
            v-text="`${row.brand_name}`"
          />
          <td
            class="text-sm"
            v-text="`${row.size_name}`"
          />
          <td
            class="text-sm"
            v-text="`${row.color_name}`"
          />
          <td>
            <VSheet
              :class="model"
              class="mt-2 mb-2 color"
              elevation="12"
              rounded="circle"
              :color="row.code_color"
              height="50"
              width="50"
            />
          </td>
          <td
            class="text-sm"
            v-text="`${row.quantity}`"
          />
        </tr>
      </tbody>
    </VTable>
  </VCard>
</template>

<style scoped>
.user{
  cursor: pointer;
}
.user:hover{
  background: #ECEFF1;
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
.tool{
  width: 400px;
}
</style>
