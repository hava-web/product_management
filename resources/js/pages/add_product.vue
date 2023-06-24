<script setup>
import avatar1 from '@images/avatars/avatar-1.png'
import { useStore } from 'vuex'
import { roles } from '../constants/roles'
import { city, alert } from '@/constants/cities'
import axiosIns from '@/plugins/axios'

const productData = {
  image: avatar1,
  name: '',
  category: '',
  description: '',
  brand: null,
  original_price: null,
  selling_price: null,
  quantiy: null,
  status: null,
  import_date: null,
  expired_date: null,
  warehouse_id: null,
  deliveredFrom: null,
  salePercentage: null,
}

const warehouseList = ref([])

const store = useStore()

const refInputEl = ref()
const productDataLocal = ref(structuredClone(productData))

const resetForm = () => {
  productDataLocal.value = structuredClone(productData)
}

const getAllWarehouse = computed(()=> store.getters.getAllWarehouse)

onMounted(async () => {
  await store.dispatch('getAllWarehouse')
  warehouseList.value.push(...getAllWarehouse.value)
  console.log(getAllWarehouse.value)
})

const getId = warehouse => warehouse.id

const formatName = warehouse=>{
  return warehouse.id + ' - ' + warehouse.name
}

// const submit = async ()=>{
//   console.log(productDataLocal.value)

//   const accessToken = localStorage.getItem('accessToken')

//   await axiosIns.post('/api/auth/register', productDataLocal.value, {
//     headers: {
//       'Authorization': `Bearer ${accessToken}`,
//     },
//   }).then(res=>{
//     console.log(res)
//     if(res.status === 201){
//       alert.title = 'Successfully'
//       alert.status = true
//       alert.text = 'Account Added Successfully'
//       alert.color = 'rgba(39, 217, 11, 0.8)'
//       productDataLocal.value = structuredClone(warehouseData)
//     }
//     else{
//       alert.title = 'Warning'
//       alert.status = true
//       alert.text = 'Something went wrong'
//       alert.color = 'rgba(234, 223, 30, 0.8)'
//     }
//   }).catch(err=>{
//     console.log(err)
//     alert.title = 'Error'
//     alert.status = true
//     alert.text = err.response.data.message
//     alert.color = 'rgba(222, 29, 29, 0.8)'
//   })
// }


const changeAvatar = file => {
  const fileReader = new FileReader()
  const { files } = file.target
  if (files && files.length) {
    fileReader.readAsDataURL(files[0])
    fileReader.onload = () => {
      if (typeof fileReader.result === 'string')
        productDataLocal.value.image = fileReader.result
    }
  }
}

// reset avatar image
const resetAvatar = () => {
  productDataLocal.value.image = productData.image
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
        title="Add Product"
        prepend-icon="mdi-package-variant-closed-plus"
      >
        <VCardText class="d-flex">
          <!-- 👉 Avatar -->
          <VAvatar
            rounded="lg"
            size="100"
            class="me-6"
            :image="productDataLocal.image"
          />

          <!-- 👉 Upload Photo -->
          <form class="d-flex flex-column justify-center gap-5">
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
                  v-model="productDataLocal.name"
                  label="Product Name"
                />
              </VCol>
              <!-- 👉 Category -->
              <VCol
                cols="12"
                md="6"
              >
                <VSelect
                  v-model="productDataLocal.warehouse_id"
                  label="Category"
                  :items="warehouseList"
                  :item-title="formatName"
                  :item-value="getId"
                />
              </VCol>

              <!-- 👉 Description -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="productDataLocal.category"
                  label="Description"
                />
              </VCol>

              <!-- 👉 Warehouse -->
              <VCol
                cols="12"
                md="6"
              >
                <VSelect
                  v-model="productDataLocal.warehouse_id"
                  label="Warehouse"
                  :items="warehouseList"
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
                  v-model="productDataLocal.category"
                  label="Original Price"
                />
              </VCol>

              <!-- 👉 Selling Price -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="productDataLocal.category"
                  label="Selling Price"
                />
              </VCol>

              <!-- 👉 Quantity -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="productDataLocal.phone"
                  label="Quantity"
                />
              </VCol>

              <!-- 👉 Status -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="productDataLocal.phone"
                  label="Status"
                />
              </VCol>

              <!-- 👉 Import Date -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="productDataLocal.date_of_birth"
                  type="date"
                  label="Imported Date"
                />
              </VCol>

              <!-- 👉 Exprired Date -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="productDataLocal.date_of_birth"
                  type="date"
                  label="Expired Date"
                />
              </VCol>

              <!-- 👉 Delivered From -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="productDataLocal.salary"
                  label="Delivered From"
                />
              </VCol>

              <!-- 👉 Saling Percentange -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="productDataLocal.address"
                  label="Saling Percentange"
                />
              </VCol>

              


              <!-- 👉 Form Actions -->
              <VCol
                cols="12"
                class="d-flex flex-wrap gap-4"
              >
                <VBtn @click="submit">
                  Create Account
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

    <!-- 👉 Deactivate Account -->
    <!--
      <VCol cols="12">
      <VCard title="Deactivate Account">
      <VCardText>
      <div>
      <VCheckbox
      v-model="isAccountDeactivated"
      label="I confirm my account deactivation"
      />
      </div>

      <VBtn
      :disabled="!isAccountDeactivated"
      color="error"
      class="mt-3"
      >
      Deactivate Account
      </VBtn>
      </VCardText>
      </VCard>
      </VCol> 
    -->
  </VRow>
</template>

<style scoped>
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
</style>
