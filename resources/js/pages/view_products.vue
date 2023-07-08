<script setup>
import { useStore } from 'vuex'
import { useRouter, useRoute } from 'vue-router'
import { reactive } from 'vue'
import { productStatus } from '@/constants/roles'
import axiosIns from '@/plugins/axios'


const store = useStore()
const route = useRoute()
const router = useRouter()
const page = ref()
const length = ref()
const cancel = ref(false)
const show = ref(true)
const dialog = ref(false)
const fields = ref([])
const productList = ref([])
const categoryList = ref([])
const warehouseList = ref([])
const brandList = ref([])
const colorList = ref([])
const sizeList = ref([])
const imageList = ref([])


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

const productInfor = reactive({
  id: null,
  images: [],
  name: '',
  category: null,
  properties: [],
  quantity: null,
  imported_date: null,
  delivered_from: '',
  description: '',
})

const getProductByPage = computed(()=>{
  return store.getters.getProductByPage
})

const getCategories = computed(()=> store.getters.getCategories)
const getAllWarehouse = computed(()=> store.getters.getAllWarehouse)
const getBrands = computed(()=> store.getters.getBrands)
const getColors = computed(() => store.getters.getColors)
const getSizes = computed(() => store.getters.getSizes)


onMounted(async () => {
  await store.dispatch('getAllWarehouse')
  warehouseList.value.push(...getAllWarehouse.value)
  console.log(getAllWarehouse.value)
})

onMounted( async() => {
  await store.dispatch('getCategories')
  categoryList.value.push(...getCategories.value)
  console.log(categoryList.value)
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

const getId = category => category.id

const formatName = category=>{
  return  category.id + ' - ' + category.name
}


const addField = () => {
  fields.value.push({
    quantity: null,
    sale_percentage: null,
    original_price: null,
    selling_price: null,
    brand_id: null,
    warehouse_id: null,
    color_id: null,
    status: null,
    size_id: null,
    expired_date: null,
  })
}

// function onFileChange(event){
//   haveImg.value = true
//   productInfor.image = event.target.files[0]
//   let reader = new FileReader()
//   reader.addEventListener("load", function(){
//     imagePreview.value = reader.result
//   }.bind(), false)

//   if(productInfor.image &&  /\.(jpe?g|png|gif)$/i.test( productInfor.image.name)){
//     reader.readAsDataURL(productInfor.image)
//   }

// }

const viewProduct = id =>{
  show.value = false
  router.push({ name: 'product', params: { id: id } })
  console.log(id)
}

const closeDialog = () =>{
  dialog.value = false
  productInfor.images = []
  imageList.value = []
  fields.value = []
  productInfor.properties = []
}

// const getImageById = async id => {
//   var image = ref()
//   const accessToken = localStorage.getItem('accessToken')

//   await axiosIns('/api/get_image/' + id, {
//     headers: {
//       'Authorization': `Bearer ${accessToken}`,
//     },
//   }).then(res=>{

//     image.value = res.data.image
    
//   }).catch(err=>{
//     console.log(err)
//   })
// }




const updateForm = async id=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/product/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    productInfor.id = res.data.id
    productInfor.name = res.data.name
    productInfor.category = res.data.category
    productInfor.original_price = Number(res.data.original_price)
    productInfor.selling_price = Number(res.data.selling_price)
    productInfor.quantity = res.data.quantity
    productInfor.imported_date = res.data.imported_date
    productInfor.delivered_from = res.data.delivered_from
    productInfor.description = res.data.description

    // imagePreview.value = 'storage/images/' + productInfor.image
    // haveImg.value = productInfor.image ? true : false
    console.log(productInfor)
  }).catch(err=>{
    console.log(err)
  })

  //Call Images
  await axiosIns('/api/get_images/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res => {

    imageList.value.push(...res.data)
    productInfor.images.push(...res.data)
    console.log(imageList.value)

  }).catch(err => {
    console.log(err)
  })

  //Call Properties
  await axiosIns('/api/product_properties/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res => {
    console.log(res.data)
    fields.value.push(...res.data)
  }).catch(err => {
    console.log(err)
  })

}

const update = async id=>{
  const properties = fields.value.map(field => ({
    quantity: Number(field.quantity),
    sale_percentage: Number(field.sale_percentage),
    brand_id: field.brand_id,
    original_price: field.original_price,
    selling_price: field.selling_price,
    warehouse_id: field.warehouse_id,
    color_id: field.color_id,
    size_id: field.size_id,
    status: field.status,
    expired_date: field.expired_date,
  }))

  productInfor.properties.push(...properties)

  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.post('/api/update/product/' + id, productInfor, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res)
    dialog.value = false
    alert.status = true
    alert.title = 'Updated Successfully'
    alert.text = 'Categoty Updated Successfully'
    alert.color = 'rgba(39, 217, 11, 0.8)'
    productInfor.images = []
    imageList.value = []
    fields.value = []
    productInfor.properties = []
  }).catch(err=>{
    console.log(productInfor)
    console.log(err)
    error.status = true
    error.title = 'You have some errors'
    error.text = err.response.data.message
    error.color = 'rgba(222, 29, 29, 0.8)'
  })
  console.log(productInfor)
} 

const deleteForm = id=>{
  console.log(id)
}

const deletePro = id=>{
  const accessToken = localStorage.getItem('accessToken')

  axiosIns.get('/api/delete/product/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res)  
    cancel.value = false
    alert.status = true
    alert.title = 'Deleted Successfully'
    alert.text = 'product deleted Successfully'
    alert.color = 'rgba(39, 217, 11, 0.8)'

    const index = productList.value.findIndex(cat => cat.id === id)

    productList.value.splice(index, 1)
  }).catch(err=>{
    console.log(err)
  })
}

// Call the action to retrieve the warehouse data and set the initial value of currentproductList to the result.

store.dispatch('getProductByPage', page.value).then(() => {
  productList.value.push(...getProductByPage.value.data)
  console.log(productList.value)
  length.value = Math.ceil(getProductByPage.value.total / getProductByPage.value.data.length)
})

// Call this function whenever the "page" value changes.
function updateproductList() {
  store.dispatch('getProductByPage', page.value).then(() => {
    productList.value = getProductByPage.value.data
  })
}

watch(page, updateproductList)


watchEffect(() => {
  const productPath = /^\/view_product\/product\/\d+$/

  show.value = !productPath.test(route.path)
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
        title="All Products"
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
                Quantity
              </th>
              <th class="text-uppercase text-center">
                Status
              </th>
              <th class="text-uppercase text-center">
                Imported Date
              </th>
              <th class="text-uppercase text-center">
                Action
              </th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="product in productList"
              :key="product.productList"
            >
              <td>
                {{ product.id }}
              </td>
              <td class="text-center">
                {{ product.name }}
              </td>
              <td class="text-center">
                {{ product.quantity }}
              </td>
              <td class="text-center">
                {{ product.status }}
              </td>
              <td class="text-center">
                <!--
                  <div class="image">
                  <VImg
                  :width="20"
                  class="item-image"
                  alt="Image"
                  content-class="text-center"
                  cover
                  :src="console.log(getImageById(product.id).then(image => {
                  return image // Access the resolved image value here
                  }))"
                  />
                  </div> 
                -->
                {{ product.imported_date }}
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
                              @click="updateForm(product.id)"
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
                            title="Add Product"
                            prepend-icon="mdi-package-variant-closed-plus"
                          >
                            <VCardText class="d-flex">
                              <!-- 👉 Avatar -->
                              <!--
                                <VAvatar
                                rounded="lg"
                                size="100"
                                class="me-6"
                                :image="productDataLocal.image"
                                /> 
                              -->

                              <!-- 👉 Upload Photo -->
                              <form class="d-flex flex-column justify-center gap-5">
                                <!--
                                  <div class="d-flex flex-wrap gap-2">
                                  <VBtn
                                  color="primary"
                                  @click="refInputEl?.click()"
                                  >
                                  <VIcon
                                  icon="mdi-cloud-upload-outline"
                                  class="d-sm-none"
                                  />
                                  <span class="d-none d-sm-block">Upload new photo</span>
                                  </VBtn>

                                  <input
                                  :ref="refInputEl"
                                  multiple
                                  type="file"
                                  name="file"
                                  accept=".jpeg,.png,.jpg,GIF"
                                  hidden
                                  @input="changeAvatar"
                                  >

                                  <VBtn
                                  type="reset"
                                  color="error"
                                  variant="tonal"
                                  @click="resetAvatar"
                                  >
                                  <span class="d-none d-sm-block">Reset</span>
                                  <VIcon
                                  icon="mdi-refresh"
                                  class="d-sm-none"
                                  />
                                  </VBtn>
                                  </div> 
                                -->
                                <!-- 👉 Image -->
                                <VCol cols="50">
                                  <VFileInput
                                    v-model="productInfor.images"
                                    clearable
                                    multiple="true"
                                    chips
                                    prepend-icon="mdi-camera"
                                    label="Select Image"
                                    accept="image/*"
                                    @click:clear="productDataLocal.value.images = []"
                                    @change="fileChange"
                                  />
                                  <div class="d-flex flex-wrap w-100">
                                    <VCard
                                      v-for="image in imageList"
                                      :key="image.imageList"
                                      :width="100"
                                      class="image"
                                    >
                                      <VImg
                                        :width="100"
                                        :height="100"
                                        content-class="text-center"
                                        alt="Image"
                                        cover
                                        :src="'storage/images/' + image.image"
                                      />
                                    </VCard>
                                  </div>
                                </VCol>


                                <p class="text-body-1 mb-0">
                                  Allowed JPG, GIF or PNG. Max size of 800K
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
                                      v-model="productInfor.name"
                                      label="Product Name"
                                    />
                                  </VCol>
                                  <!-- 👉 Category -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VSelect
                                      v-model="productInfor.category"
                                      label="Category"
                                      :items="categoryList"
                                      :item-title="formatName"
                                      :item-value="getId"
                                    />
                                  </VCol>

                                  <!-- 👉 Quantity -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VTextField
                                      v-model="productInfor.quantity"
                                      label="Quantity"
                                    />
                                  </VCol>

                                  <!-- 👉 Import Date -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VTextField
                                      v-model="productInfor.imported_date"
                                      type="date"
                                      label="Imported Date"
                                    />
                                  </VCol>

                                  <!-- 👉 Delivered From -->
                                  <VCol cols="12">
                                    <VTextField
                                      v-model="productInfor.delivered_from"
                                      label="Delivered From"
                                    />
                                  </VCol>

                                  <!-- 👉 Description -->
                                  <VCol cols="12">
                                    <VTextarea
                                      v-model="productInfor.description"
                                      label="Description"
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
                                        Properties
                                      </VCardTitle>
                                      <VBtn
                                        variant="tonal"
                                        class="mt-2 add-props"
                                        prepend-icon="mdi-plus"
                                        @click="addField"
                                      >
                                        Add
                                      </VBtn>
                                    </div>

                                    <!-- 👉 Form -->
                                    <div
                                      v-for="field in fields"
                                      :key="field.fields"
                                      class="d-flex flex-wrap form"
                                    >
                                      <!-- 👉 Quantity -->
                                      <VCol cols="12">
                                        <VTextField
                                          v-model="field.quantity"
                                          type="number"
                                          label="Quantity"
                                        />
                                      </VCol>

                                      <!-- 👉 Discount -->
                                      <VCol
                                        cols="12"
                                        md="6"
                                      >
                                        <VTextField
                                          v-model="field.sale_percentage"
                                          label="Discount"
                                        />
                                      </VCol>

                                      <!-- 👉 Brand -->
                                      <VCol
                                        cols="12"
                                        md="6"
                                      >
                                        <VSelect
                                          v-model="field.brand_id"
                                          label="Brand"
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
                                          label="Original Price"
                                        />
                                      </VCol>

                                      <!-- 👉 Selling Price -->
                                      <VCol
                                        md="6"
                                        cols="12"
                                      >
                                        <VTextField
                                          v-model="field.selling_price"
                                          label="Selling Price"
                                        />
                                      </VCol>


                                      <!-- 👉 Warehouse -->
                                      <VCol
                                        cols="12"
                                        md="6"
                                      >
                                        <VSelect
                                          v-model="field.warehouse_id"
                                          label="Warehouse"
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
                                          v-model="field.size_id"
                                          label="Size"
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
                                          label="Status"
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
                                          label="Expired Date"
                                        />
                                      </VCol>
                                      <div class="w-100 d-flex flex-wrap color ">
                                        <VRadioGroup
                                          v-model="field.color_id"
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
                                    <VBtn @click="update(product.id)">
                                      Update Product
                                    </VBtn>

                                    <VBtn
                                      color="secondary"
                                      variant="tonal"
                                      type="reset"
                                      @click="closeDialog"
                                    >
                                      Cancel
                                    </VBtn>
                                  </VCol>
                                </VRow>
                              </VForm>
                            </VCardText>
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
                              @click="deleteForm(product.id)"
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
                                @click="deletePro(product.id)"
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
  margin-top: 20px;
  margin-left: 40px;
}
.item-image{
  margin: 10px;
}
.add-props{
  position: absolute;
  right: 30px;

}
</style>