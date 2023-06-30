<script setup>
import { useStore } from 'vuex'
import { useRouter, useRoute } from 'vue-router'
import { reactive } from 'vue'
import axiosIns from '@/plugins/axios'


const store = useStore()
const route = useRoute()
const router = useRouter()
const page = ref()
const length = ref()
const cancel = ref(false)
const show = ref(true)
const dialog = ref(false)
const productList = ref([])
const haveImg = ref(false)
const imagePreview = ref(null)


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


const getProductByPage = computed(()=>{
  return store.getters.getProductByPage
})

const productInfor = reactive({
  id: null,
  name: '',
  image: null,
  status: null,
})

function onFileChange(event){
  haveImg.value = true
  productInfor.image = event.target.files[0]
  let reader = new FileReader()
  reader.addEventListener("load", function(){
    imagePreview.value = reader.result
  }.bind(), false)

  if(productInfor.image &&  /\.(jpe?g|png|gif)$/i.test( productInfor.image.name)){
    reader.readAsDataURL(productInfor.image)
  }

}

const viewProduct = id =>{
  show.value = false
  router.push({ name: 'product', params: { id: id } })
  console.log(id)
}

const getImageById = async id => {
  var image = ref()
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns('/api/get_image/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{

    image.value = res.data.image
    
  }).catch(err=>{
    console.log(err)
  })

  return image.value
}




// const updateForm = async id=>{
//   const accessToken = localStorage.getItem('accessToken')

//   await axiosIns.get('/api/product/' + id, {
//     headers: {
//       'Authorization': `Bearer ${accessToken}`,
//     },
//   }).then(res=>{
//     productInfor.id = res.data.id
//     productInfor.image = res.data.image
//     productInfor.name = res.data.name
//     productInfor.status = res.data.status
//     imagePreview.value = 'storage/images/' + productInfor.image
//     haveImg.value = productInfor.image ? true : false
//     console.log(res.data)
//   }).catch(err=>{
//     console.log(err)
//   })
// }

// const update = async id=>{
//   const accessToken = localStorage.getItem('accessToken')

//   await axiosIns.post('/api/update/product/' + id, productInfor, {
//     headers: {
//       'Authorization': `Bearer ${accessToken}`,
//     },
//   }).then(res=>{
//     console.log(res)
//     dialog.value = false
//     alert.status = true
//     alert.title = 'Updated Successfully'
//     alert.text = 'Categoty Updated Successfully'
//     alert.color = 'rgba(39, 217, 11, 0.8)'
//   }).catch(err=>{
//     console.log(productInfor)
//     console.log(err)
//     error.status = true
//     error.title = 'You have some errors'
//     error.text = err.response.data.message
//     error.color = 'rgba(222, 29, 29, 0.8)'
//   })
//   console.log(productInfor)
// } 

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
        title="All Employees"
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
                            prepend-icon="mdi-store-edit"
                            title=" Update Employee "
                          >
                            <VCardText>
                              <!-- 👉 Form -->
                              <VForm class="mt-6">
                                <VRow>
                                  <!-- 👉 product Name -->
                                  <VCol cols="12">
                                    <VTextField
                                      v-model="productInfor.name"
                                      prepend-icon="mdi-rename"
                                      pre
                                      label="product Name"
                                    />
                                  </VCol>

                                  <!-- 👉 Status -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VFileInput
                                      :clearable="false"
                                      prepend-icon="mdi-camera"
                                      label="Select Image"
                                      accept="image/*"
                                      @change="onFileChange"
                                    />
                                    <VCard
                                      v-if="haveImg"
                                      :width="100"
                                      class="image"
                                    >
                                      <VImg
                                        v-model="productInfor.image"
                                        :width="100"
                                        :height="100"
                                        content-class="text-center"
                                        alt="Image"
                                        :src="imagePreview"
                                        cover
                                      />
                                    </VCard>
                                  </VCol>

                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VSwitch
                                      v-model="productInfor.status"
                                      :true-value="1"
                                      :false-value="0"
                                      prepend-icon="mdi-list-status"
                                      label="Status"
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
                                Cancel
                              </VBtn>
                              <VBtn
                                color="primary"
                                variant="elevated"
                                prepend-icon="mdi-pencil-outline"
                                @click="update(product.id)"
                              >
                                Update
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
</style>