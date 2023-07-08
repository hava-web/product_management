<script setup>
import { useStore } from 'vuex'
import { alert, payment } from '@/constants/cities'
import axiosIns from '@/plugins/axios'
import { reactive } from 'vue'

const orderData = {
  id: null,
  lastname: '',
  firstname: '',
  phone: '',
  email: '',
  address: '',
  payment_mode: null,
  products: [],
  total_price: null,
}

const warehouseList = ref([])
const attachment = ref([])
const productList = ref([])
const customerList = ref([])
const cusAvai = ref()

const cusAvaiInfo = reactive({
  id: null,
  payment_mode: '',
  firstname: '',
  lastname: '',
  phone: '',
  email: '',
  address: '',
})

const fields = ref([])
const showButton = ref(true)
const createCus = ref(false)
const useCus = ref(false)

const store = useStore()
const orderDataLocal = ref(structuredClone(orderData))

const field = reactive({
  status: false,
  colorProduct: null,
  content: null,
})

const resetForm = () => {
  orderDataLocal.value = structuredClone(orderData)
  fields.value = []
}

const addField = () => {
  fields.value.push({
    product: null,
    quantity: null,
    price: 0,
    discount: null,
    brand: null,
    color: null,
    size: null,
  })
}

const getAllWarehouse = computed(()=> store.getters.getAllWarehouse)
const getProducts = computed(() => store.getters.getProducts)
const getCustomers = computed(() => store.getters.getCustomers)

onMounted(async () => {
  await store.dispatch('getAllWarehouse')
  warehouseList.value.push(...getAllWarehouse.value)
  console.log(getAllWarehouse.value)
})

onMounted(async () => {
  await store.dispatch('getCustomers')
  customerList.value.push(...getCustomers.value)
  console.log(getCustomers.value)
})

onMounted(async () => {
  await store.dispatch('getProducts')
  productList.value.push(...getProducts.value)
  console.log(getProducts.value)
})


const getId = warehouse => {
  if (warehouse) {
    console.log(warehouse.id)

    return warehouse.id
  }
  
  return null
}

const clearProduct = field =>{
  field.product = null
  field.quantity = null
  field.brand = null
  field.color = null
  field.size = null
  field.discount = null
  field.selling_price = null
  field.product_quantity = null
  field.price = 0
  field.status = false
  field.colorProduct = null
  field.content = null
  field.brandList = []
  field.sizeList = []
  field.colorList = []
}

const clearCustomer = ()=>{
  cusAvai.value = null
  cusAvaiInfo.address = null
  cusAvaiInfo.email = null,
  cusAvaiInfo.firstname = ''
  cusAvaiInfo.lastname = ''
  cusAvaiInfo.phone = null
}

const formatName = warehouse=>{
  if (warehouse && warehouse.id && warehouse.name) {
    return `${warehouse.id} - ${warehouse.name}`
  } else if (typeof warehouse === 'string') {
    return warehouse
  }
  
  return ''
}

const cusInfor = customer => {
  if (customer && customer.id) {
    return `${customer.id} - ${customer.lastname}  ${customer.firstname} - ${customer.address} - ${customer.phone}`
  } else if (typeof customer === 'string') {
    return customer
  }
  
  return ''
}

const showCreateForm = () => {
  createCus.value = true
  useCus.value = false
}

const showCusAvail = () => {
  createCus.value = false
  useCus.value = true
}

const remove = field =>{
  // Find the index of the form object in the forms array
  const index = fields.value.indexOf(field)
    
  // Remove the form object from the forms array
  if (index !== -1) {
    fields.value.splice(index, 1)
  }
}

const handleProductSelection =  field=> {
  if (field.product && field.product.id) {
    field.brandList = []
    
    const accessToken = localStorage.getItem('accessToken')

    axiosIns.get('api/product_brands/' + field.product.id, {
      headers: {
        'Authorization': `Bearer ${accessToken}`,
      },
    }).then(res=>{
      console.log(res.data)

      // brandList.value.push(...res.data)
      field.brandList = res.data
      console.log(field.brandList)
    }).catch(err=>{
      console.log(err.data)
    })
  }
}

const customerHandle = customer =>{
  if (customer && customer.id) {
    console.log(customer.id)

    const accessToken = localStorage.getItem('accessToken')

    axiosIns.get('api/customer/' + customer.id, {
      headers: {
        'Authorization': `Bearer ${accessToken}`,
      },
    }).then(res=>{
      cusAvaiInfo.id = customer.id
      cusAvaiInfo.firstname = res.data.firstname
      cusAvaiInfo.lastname = res.data.lastname
      cusAvaiInfo.phone = res.data.phone
      cusAvaiInfo.email = res.data.email
      cusAvaiInfo.address = res.data.address
      console.log(cusAvaiInfo)
    
    }).catch(err=>{
      console.log(err.data)
    })
  }
}

const getSizesByBrand = field => {
  const accessToken = localStorage.getItem('accessToken')

  field.sizeList = []
  axiosIns.get('api/product_brand_size/' + field.product.id + '/' + field.brand, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res.data)
    field.sizeList = res.data
    console.log(field.sizeList)
  }).catch(err=>{
    console.log(err.data)
  })
}

const getColorByBraSiz = field => {
  const accessToken = localStorage.getItem('accessToken')

  field.colorList = []
  axiosIns.get('api/product_brand_size_color/' + field.product.id + '/' + field.brand + '/' + field.size, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res.data)
    field.colorList = res.data
    console.log(field.colorList)
    console.log(res.data)
  }).catch(err=>{
    console.log(err.data)
  })
}

const getDiscount = field =>{
  const accessToken = localStorage.getItem('accessToken')

  field.discount = null
  field.selling_price = null
  field.product_quantity = null
  axiosIns.get('api/get_discount/' + field.product.id + '/' + field.brand + '/' + field.size + '/' + field.color, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    field.discount = res.data.sale_percentage
    field.selling_price = res.data.selling_price
    field.product_quantity =res.data.quantity
    if(field.product_quantity > 0){
      field.status = true
      field.colorProduct = '#00C853'
      field.content = 'In Stock'
    }

    if(field.product_quantity <= 0){
      field.status = true
      field.colorProduct = '#F44336'
      field.content = 'Out Of Stock'
    }
    console.log(res.data)
  }).catch(err=>{
    console.log(err.data)
  })
}

const itemPrice = field => {
  if (field.product && field.product.id) {
    // const price = productList.value.find(product => product.id == field.product.id)
    field.price = Number(field.selling_price)*field.quantity - (Number(field.selling_price)*field.quantity * field.discount)/100
     
    return field.price
  }

  return 0
}

const totalPrice = ()=>{
  let total = 0
  console.log(fields.value)
  for(const field of fields.value){
    total += field.price 
  }
  orderDataLocal.value.total_price = total

  return total
}

const submit = async ()=>{
  const products = fields.value.map(field => ({
    product: field.product ? field.product.id : null,
    quantity: Number(field.quantity),
    brand: field.brand,
    price: field.price,
    discount: field.discount,
    color: field.color,
    size: field.size,
  }))

  if(cusAvai && useCus.value){
    orderDataLocal.value.id = cusAvaiInfo.id
    orderDataLocal.value.firstname = cusAvaiInfo.firstname
    orderDataLocal.value.lastname = cusAvaiInfo.lastname
    orderDataLocal.value.email = cusAvaiInfo.email
    orderDataLocal.value.payment_mode = cusAvaiInfo.payment_mode
    orderDataLocal.value.phone = cusAvaiInfo.phone
    orderDataLocal.value.address = cusAvaiInfo.address
  }

  orderDataLocal.value.products.push(...products)
  console.log(orderDataLocal.value)

  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.post('/api/add_order', orderDataLocal.value, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res)
    if(res.status === 201){
      alert.title = 'Successfully'
      alert.status = true
      alert.text = 'Account Added Successfully'
      alert.color = 'rgba(39, 217, 11, 0.8)'
      orderDataLocal.value = structuredClone(orderData)
      fields.value = []
    }
    else{
      alert.title = 'Warning'
      alert.status = true
      alert.text = 'Something went wrong'
      alert.color = 'rgba(234, 223, 30, 0.8)'
    }
  }).catch(err=>{
    console.log(err)
    alert.title = 'Error'
    alert.status = true
    alert.text = err.response.data.message
    alert.color = 'rgba(222, 29, 29, 0.8)'
    orderDataLocal.value = structuredClone(orderData)
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
        title="Create Order"
        prepend-icon="mdi-package-variant-closed-plus"
      >
        <VDivider />
        <VCardTitle class="d-flex">
          <VIcon icon="mdi-account" />
          Customer Information
          <div
            v-if="showButton"
            class="button-list"
          >
            <VBtn
              class="button"
              prepend-icon="mdi-account-multiple-plus-outline"
              @click="showCreateForm"
            >
              Create New Customer
            </VBtn>
            <VBtn
              class="button text-uppercase"
              prepend-icon="mdi-account-group"
              color="none"
              variant="tonal"
              @click="showCusAvail"
            >
              Use Available Customers
            </VBtn>
          </div>
        </VCardTitle>
        <VCardText>
          <!-- 👉 Customer Information -->
          <VRow />
          <VForm class="mt-6">
            <VRow v-if="createCus">
              <!-- 👉 First Name -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="orderDataLocal.firstname"
                  label="First Name"
                />
              </VCol>

              <!-- 👉 Last Name -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="orderDataLocal.lastname"
                  label="Last Name"
                />
              </VCol>
              <!-- 👉 Phone -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="orderDataLocal.phone"
                  label="Phone Number"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="orderDataLocal.email"
                  type="email"
                  label="Email"
                />
              </VCol>   

              <!-- 👉 Payment Mode -->
              <VCol
                cols="12"
                md="6"
              >
                <VSelect
                  v-model="orderDataLocal.payment_mode"
                  label="Payment Mode"
                  :items="payment"
                />
              </VCol>

              <!-- 👉 Address -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="orderDataLocal.address"
                  label="Address"
                />
              </VCol>
            </VRow>
            <VRow v-if="useCus">
              <VCol cols="12">
                <VCombobox
                  v-model="cusAvai"
                  clearable
                  chips
                  label="Customer"
                  :items="customerList"
                  :item-title="cusInfor"
                  :item-value="getId"
                  variant="solo-filled"
                  @click:clear="clearCustomer"
                  @update:search="customerHandle(cusAvai)"
                />
              </VCol>
              <VCard class="w-100">
                <div class="mx-5 my-5">
                  <div class="text-uppercase">
                    Full Name : {{ cusAvaiInfo.lastname + ' ' + cusAvaiInfo.firstname }}
                  </div>
                  <div class="text-uppercase">
                    Phone Number : {{ cusAvaiInfo.phone }} 
                  </div>
                  <div class="text-uppercase">
                    Email : {{ cusAvaiInfo.email }} 
                  </div>
                  <div class="text-uppercase">
                    Address : {{ cusAvaiInfo.address }} 
                  </div>
                </div>
              </VCard>
              <!-- 👉 Payment Mode -->
              <VCol cols="12">
                <VSelect
                  v-model="cusAvaiInfo.payment_mode"
                  label="Payment Mode"
                  :items="payment"
                />
              </VCol>
            </VRow>

            <VRow class="mt-8">
              <!-- 👉 products -->
              <div class="w-100">
                <div class="d-flex">
                  <VCardTitle
                    cols="12"
                    class="d-flex flex-wrap gap-4"
                  >
                    <VIcon icon="mdi-package-variant" />
                    Products
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
                  <!-- 👉 Product -->
                  <VCol
                    cols="12"
                    md="6"
                  >
                    <VCombobox
                      v-model="field.product"
                      clearable
                      chips
                      label="Product"
                      :items="productList"
                      :item-title="formatName"
                      :item-value="getId(field.product)"
                      variant="solo-filled"
                      @click:clear="clearProduct(field)"
                      @update:search="handleProductSelection(field)"
                    />
                  </VCol>

                  <!-- 👉 Quantity -->
                  <VCol
                    cols="12"
                    md="6"
                  >
                    <VTextField
                      v-model="field.quantity"
                      label="Quantity"
                    />
                  </VCol>

                  <!-- 👉 Brand -->
                  <VCol
                    cols="12"
                    md="6"
                  >
                    <VSelect
                      v-model="field.brand"
                      label="Brand"
                      :items="field.brandList"
                      :item-title="formatName"
                      :item-value="getId"
                      @update:model-value="getSizesByBrand(field)"
                    />
                  </VCol>

                  <!-- 👉 Size -->
                  <VCol
                    cols="12"
                    md="6"
                  >
                    <VSelect
                      v-model="field.size"
                      label="Size"
                      :items="field.sizeList"
                      :item-title="formatName"
                      :item-value="getId"
                      @update:model-value="getColorByBraSiz(field)"
                    />
                  </VCol>

                  <div class="d-flex flex-wrap w-100 color ">
                    <VRadioGroup
                      v-model="field.color"
                      inline
                      class="group"
                      @update:model-value="getDiscount(field)"
                    >
                      <VCol
                        v-for="color in field.colorList"
                        :key="color.id"
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
                  <div class="discout w-100 text-uppercase font-weight-regular">
                    Product Quantity: {{ field.product_quantity }} 
                    <VSheet
                      v-if="field.status"
                      :color="field.colorProduct"
                      border="rounded-0"
                      class="mx-10"
                    >
                      {{ field.content }}
                    </VSheet>
                  </div>
                  <div class="discout w-100 text-uppercase font-weight-regular">
                    Selling Price: ${{ field.selling_price }}
                  </div>
                  <div class="discout w-100 text-uppercase font-weight-regular">
                    discount: {{ field.discount }}%
                  </div>
                  <div class="font-weight-bold price-item text-uppercase">
                    Price: ${{ itemPrice(field) == NaN ? 0 : itemPrice(field) }}
                  </div>
                  <div class="remove justify-end w-100">
                    <VBtn @click="remove(field)">
                      Remove
                    </VBtn>
                  </div>
                </div>
              </div>
              <div class="w-100 d-flex justify-end">
                <VCardTitle class="">
                  <VIcon icon="mdi-currency-usd" />
                  Total Price: ${{ totalPrice() }}
                </VCardTitle>
              </div>

              <!-- 👉 Form Actions -->
              <VCol
                cols="12"
                class="d-flex flex-wrap gap-4"
              >
                <VBtn @click="submit">
                  Add Product
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
.button-list{
    position: absolute;
    right: 15px;
}
.button{
   margin-right: 15px;
}
.price{
  position: absolute;
}
.remove{
  margin-bottom: 10px;
  margin-right: 10px;
  display: flex;
}
.discout{
  margin-bottom: 10px;
  margin-right: 10px;
  margin-left: 10px;
  display: flex;
}
.price-item{
  margin-left: 10px;
  font-size: 20px;
}
</style>
