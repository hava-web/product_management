<script setup>
import { useStore } from 'vuex'
import { alert, city } from '@/constants/cities'
import { productStatus } from '../constants/roles'
import axiosIns from '@/plugins/axios'

const productData = {
  images: [],
  name: '',
  category: null,
  product_code: '',
  description: '',
  properties: [],
  imported_date: null,
}

const warehouseList = ref([])
const haveImg = ref(false)
const attachment = ref([])
const imagePreview = ref([])
const categoryList = ref([])
const colorList = ref([])
const brandList = ref([])
const sizeList = ref([])
const agentList = ref([])
const fields = ref([])

const store = useStore()
const productDataLocal = ref(structuredClone(productData))

const resetForm = () => {
  productDataLocal.value = structuredClone(productData)
  fields.value = []
}

const addField = () => {
  fields.value.push({
    quantity: null,
    discount: null,
    brand: null,
    warehouse_id: null,
    original_price: null,
    selling_price: null,
    color: null,
    status: null,
    agent: null,
    size: null,
    expired_date: null,
  })
}

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

const chosenColors = ref([])


const getId = warehouse => warehouse.id

const formatName = warehouse=>{
  return warehouse.id + ' - ' + warehouse.name
}


function fileChange(event){
  console.log(event)
  let selectedFiles = event.target.files
  if(!selectedFiles.length){
    return false
  }

  for(let i=0; i< selectedFiles.length; i++){
    attachment.value.push(selectedFiles[i])
  }
}

const submit = async ()=>{
  const properties = fields.value.map(field => ({
    agent: field.agent,
    quantity: field.quantity,
    discount: Number(field.discount),
    brand: field.brand,
    warehouse_id: field.warehouse_id,
    original_price: Number(field.original_price),
    selling_price: Number(field.selling_price),
    color: field.color,
    size: field.size,
    status: field.status,
    expired_date: field.expired_date,
  }))

  productDataLocal.value.properties.push(...properties)
  console.log(productDataLocal.value)
  console.log(chosenColors.value)

  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.post('/api/add_product', productDataLocal.value, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
      'Content-Type': 'multipart/form-data',
    },
  }).then(res=>{
    console.log(res)
    if(res.status === 201){
      alert.title = 'Thêm thành công'
      alert.status = true
      alert.text = 'Sản phẩm đã được thêm thành công'
      alert.color = 'rgba(39, 217, 11, 0.8)'
      productDataLocal.value = structuredClone(productData)
      chosenColors.value = []
      fields.value = []
    }
    else{
      alert.title = 'Cảnh báo'
      alert.status = true
      alert.text = 'Đang có lỗi gì đó'
      alert.color = 'rgba(234, 223, 30, 0.8)'
    }
  }).catch(err=>{
    console.log(err)
    alert.title = 'Lỗi'
    alert.status = true
    alert.text = err.response.data.message
    alert.color = 'rgba(222, 29, 29, 0.8)'
    productDataLocal.value = structuredClone(productData)
    fields.value = []
  })
}
</script>

<template>
  <Transition name="slide-fade">
    <VAlert 
      v-if="alert.status"
      :color="alert.color"
      icon=""
      :title="alert.title"
      closable
      class="alert"
      max-width="400px"
      :text="alert.text"
      @click:close="alert.status = false"
    />
  </Transition>
  <VRow>
    <VCol cols="12">
      <VCard
        title="Thêm sản phẩm"
        prepend-icon="mdi-package-variant-closed-plus"
      >
        <VCardText class="d-flex">
          <!-- 👉 Upload Photo -->
          <form class="d-flex flex-column justify-center gap-5">
            <!-- 👉 Image -->
            <VCol cols="20">
              <VFileInput
                v-model="productDataLocal.images"
                clearable
                multiple="true"
                chips
                prepend-icon="mdi-camera"
                label="Thêm ảnh"
                accept="image/*"
                @click:clear="productDataLocal.value.images = []"
                @change="fileChange"
              />
              <VCard
                :width="100"
                class="image"
              >
                <VImg
                  v-if="haveImg"
                  :width="100"
                  :height="100"
                  content-class="text-center"
                  alt="Image"
                  cover
                  :src="imagePreview"
                />
              </VCard>
            </VCol>


            <p class="text-body-1 mb-0">
             Bạn có thể thêm nhiều ảnh
            </p>
          </form>
        </VCardText>

        <VDivider />

        <VCardText>
          <!-- 👉 Form -->
          <VForm class="mt-6">
            <VRow>
              <!-- 👉 Product Name -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="productDataLocal.name"
                  label="Tên sản phẩm"
                />
              </VCol>

              <!-- 👉 Product code -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="productDataLocal.product_code"
                  label="Mã nhập"
                />
              </VCol>

              <!-- 👉 Category -->
              <VCol
                cols="12"
                md="6"
              >
                <VSelect
                  v-model="productDataLocal.category"
                  label="Danh mục"
                  :items="categoryList"
                  :item-title="formatName"
                  :item-value="getId"
                />
              </VCol>

              <!-- 👉 Import Date -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="productDataLocal.imported_date"
                  type="date"
                  label="Ngày nhập"
                />
              </VCol>

              <!-- 👉 Quantity -->
              <VCol cols="12">
                <VTextField
                  v-model="productDataLocal.quantity"
                  label="Số lượng"
                  @input="check"
                />
              </VCol>

              <!-- 👉 Description -->
              <VCol cols="12">
                <VTextarea
                  v-model="productDataLocal.description"
                  label="Mô tả"
                />
              </VCol>
              
              <VDivider />

              <!-- 👉 Properties -->
              <div class="">
                <div class="d-flex">
                  <VCardTitle
                    cols="12"
                    class="d-flex flex-wrap gap-4"
                  >
                    <VIcon icon="mdi-atom-variant" />
                    Các thuộc tính
                  </VCardTitle>
                  <VBtn
                    variant="tonal"
                    class="mt-2 add-props"
                    prepend-icon="mdi-plus"
                    @click="addField"
                  >
                    Thêm
                  </VBtn>
                </div>

                <!-- 👉 Form -->
                <div
                  v-for="field in fields"
                  :key="field.fields"
                  class="d-flex flex-wrap form"
                >
                  <!-- 👉 Quantity -->
                  <VCol
                    cols="12"
                    md="6"
                  >
                    <VTextField
                      v-model="field.quantity"
                      label="Số lượng"
                    />
                  </VCol>

                  <!-- 👉 Discount -->
                  <VCol
                    cols="12"
                    md="6"
                  >
                    <VTextField
                      v-model="field.discount"
                      label="Triết khấu"
                    />
                  </VCol>

                  <!-- 👉 Agent -->
                  <VCol
                    cols="12"
                    md="6"
                  >
                    <VSelect
                      v-model="field.agent"
                      label="Chi nhánh"
                      :items="agentList"
                      :item-title="formatName"
                      :item-value="getId"
                    />
                  </VCol>

                  <!-- 👉 Brand -->
                  <VCol
                    cols="12"
                    md="6"
                  >
                    <VSelect
                      v-model="field.brand"
                      label="Thương hiệu"
                      :items="brandList"
                      :item-title="formatName"
                      :item-value="getId"
                    />
                  </VCol>

                  <!-- 👉 Original Price -->
                  <VCol
                    md="6"
                    cols="12"
                  >
                    <VTextField
                      v-model="field.original_price"
                      label="Giá gốc"
                    />
                  </VCol>

                  <!-- 👉 Selling Price -->
                  <VCol
                    md="6"
                    cols="12"
                  >
                    <VTextField
                      v-model="field.selling_price"
                      label="Giá bán"
                    />
                  </VCol>

                  <!-- 👉 Warehouse -->
                  <VCol
                    cols="12"
                    md="6"
                  >
                    <VSelect
                      v-model="field.warehouse_id"
                      label="Kho hàng"
                      :items="warehouseList"
                      :item-title="formatName"
                      :item-value="getId"
                    />
                  </VCol>

                  <!-- 👉 Size -->
                  <VCol
                    cols="12"
                    md="6"
                  >
                    <VSelect
                      v-model="field.size"
                      label="Kích thước"
                      :items="sizeList"
                      :item-title="formatName"
                      :item-value="getId"
                    />
                  </VCol>

                  <!-- 👉 Status -->
                  <VCol
                    cols="12"
                    md="6"
                  >
                    <VSelect
                      v-model="field.status"
                      label="Trạng thái"
                      :items="productStatus"
                    />
                  </VCol>

                  <!-- 👉 Exprired Date -->
                  <VCol
                    cols="12"
                    md="6"
                  >
                    <VTextField
                      v-model="field.expired_date"
                      type="date"
                      label="Ngày hết hạn"
                    />
                  </VCol>
                  <div class="d-flex flex-wrap color ">
                    <VRadioGroup
                      v-model="field.color"
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
                            <!--
                              <VCheckboxBtn
                              :checked="isChosenColor(color)"
                              class="pe-2"
                              :value="color.id"
                              @click="toggleChosenColor(color)"
                              /> 
                            -->
                            <VRadio
                              class="pe-2"
                              :value="color.id"
                            />
                          </div>
                        <!--
                          <div class="field">
                          <VTextField
                          v-model="colorQuantity[color.id]"
                          :disabled="!isQuantityFieldEnabled(color)"
                          label="Quantity"
                          @input="updateColorQuantity(color.id, $event.target.value)"
                          />
                          </div> 
                        -->
                        </div>
                      </VCol>
                    </VRadioGroup>
                  </div>
                </div>
              </div>

              <!-- 👉 Form Actions -->
              <VCol
                cols="12"
                class="d-flex flex-wrap gap-4"
              >
                <VBtn @click="submit">
                  Thêm sản phẩm
                </VBtn>

                <VBtn
                  color="secondary"
                  variant="tonal"
                  type="reset"
                  @click.prevent="resetForm"
                >
                  Reset
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </vrow>
</template>

<style scoped>
  .alert{
  position: absolute;
  top: 20px;
  right: 10px;
  z-index: 100;
}

.title{
  display: block;
}
.lable{
  display: flex;
}
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.lable-title{
  max-width: 80px;
  width:80px;
 
}

.slide-fade-leave-active {
  transition: all 0.8s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
.field{
  width: 230px;
}
.add-props{
  position: absolute;
  right: 30px;

}
.color{
  margin-left: 10px;
}

.form{
  border: 2px solid #9155FD;
  border-radius: 10px;
  margin-top: 10px;
  margin-bottom: 10px;
}
.group{
  margin-left: 30px;
}
</style>
