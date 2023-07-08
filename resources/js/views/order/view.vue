<script setup>
import { useRouter, useRoute } from 'vue-router'
import axiosIns from '@/plugins/axios'
import { reactive } from 'vue'
import { orderStatus, alert } from '../../constants/cities'

const route = useRoute()
const router = useRouter()


const customer = reactive({
  id: null,
  firstname: '',
  lastname: '',
  email: '',
  phone: '',
  address: '',
})

const order = reactive({
  status: null,
  total_price: null,
  payment_mode: null,
})

const producList = ref([])

const id = route.params.id

const orderedStatus = reactive({
  iconColor: '#455A64',
  dotColor: '#E8EAF6',
})

const pendingStatus = reactive({
  iconColor: '#455A64',
  dotColor: '#E8EAF6',
})

const deliveringStatus = reactive({
  iconColor: '#455A64',
  dotColor: '#E8EAF6',
})

const receivedStatus = reactive({
  iconColor: '#455A64',
  dotColor: '#E8EAF6',
})


onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/order_customer/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    customer.id = res.data.id
    customer.firstname = res.data.firstname
    customer.lastname = res.data.lastname
    customer.phone = res.data.phone
    customer.email = res.data.email
    customer.address = res.data.address
    console.log(customer)
  }).catch(err=>{
    console.log(err)
  })
})

onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/order/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    order.status = res.data.status
    order.payment_mode = res.data.payment_mode
    order.total_price = res.data.total_price
    if(order.status == 'Ordered'){
      orderedStatus.dotColor = '#FFFFFF'
      orderedStatus.iconColor = '#00E676'
    }
    if(order.status == 'Pending'){
      orderedStatus.dotColor = '#FFFFFF'
      orderedStatus.iconColor = '#00E676'
      pendingStatus.dotColor = '#FFFFFF'
      pendingStatus.iconColor = '#00E676'
    }

    if(order.status == 'Canceled'){
      orderedStatus.dotColor = '#FFFFFF'
      orderedStatus.iconColor = '#D50000'
      pendingStatus.dotColor = '#FFFFFF'
      pendingStatus.iconColor = '#D50000'
    }

    if(order.status == 'Delivering'){
      orderedStatus.dotColor = '#FFFFFF'
      orderedStatus.iconColor = '#00E676'
      pendingStatus.dotColor = '#FFFFFF'
      pendingStatus.iconColor = '#00E676'
      deliveringStatus.dotColor = '#FFFFFF'
      deliveringStatus.iconColor = '#00E676'
    }

    if(order.status == 'Received'){
      orderedStatus.dotColor = '#FFFFFF'
      orderedStatus.iconColor = '#00E676'
      pendingStatus.dotColor = '#FFFFFF'
      pendingStatus.iconColor = '#00E676'
      deliveringStatus.dotColor = '#FFFFFF'
      deliveringStatus.iconColor = '#00E676'
      receivedStatus.dotColor = '#FFFFFF'
      receivedStatus.iconColor = '#00E676'
    }
    console.log(res.data)
  }).catch(err=>{
    console.log(err)
  })
})


onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/order_products/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    producList.value.push(...res.data)
    console.log(producList.value)
  }).catch(err=>{
    console.log(err)
  })
})

const viewProduct = id =>{
  router.push({ name: 'product', params: { id: id } })
}

const update = async id=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.post('/api/update/order/' + id, order, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    alert.title = 'Successfully'
    alert.status = true
    alert.text = 'Account Added Successfully'
    alert.color = 'rgba(39, 217, 11, 0.8)'
    console.log(res.data)

  }).catch(err=>{
    alert.title = 'Error'
    alert.status = true
    alert.text = err.response.data
    alert.color = 'rgba(222, 29, 29, 0.8)'
    console.log(err)
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
        :title="'ORDER ' + id"
        prepend-icon="mdi-package-variant-closed"
      >
        <VDivider />
        <div class="d-flex">
          <VIcon
            icon="mdi-account"
            class="icon-header"
          />
          <VCardTitle>CUSTOMER INFORMATION</VCardTitle>
          <VCol
            md="3"
            cols="6"
            class="status"
          >
            <VSelect
              v-model="order.status"
              label="Select"
              :items="orderStatus"
            />
          </VCol>
          <VBtn
            class="my-5"
            @click="update(id)"
          >
            Update
          </VBtn>
        </div>
        <VTable>
          <tr class="d-flex">
            <td class="d-flex w-50">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-id-card"
                />
                Customer ID:
              </div>
              <div class="infor">
                {{ customer.id }}
              </div>
            </td>
            <td class="d-flex w-50">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-ab-testing"
                />
                Full Name:
              </div>
              <div class="infor">
                {{ customer.lastname + ' ' + customer.firstname }}
              </div>
            </td>
          </tr>
          <tr class="d-flex">
            <td class="d-flex w-50">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-phone"
                />
                Phone Number:
              </div>
              <div class="infor">
                {{ customer.phone }}
              </div>
            </td>
            <td class="d-flex w-50">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-email-multiple-outline"
                />
                Email:
              </div>
              <div class="infor">
                {{ customer.email }}
              </div>
            </td>
          </tr>
          <tr class="d-flex">
            <td class="d-flex w-50">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-map-marker"
                />
                Address:
              </div>
              <div class="infor">
                {{ customer.address }}
              </div>
            </td>
            <td class="d-flex w-50">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-bank"
                />
                Payment Mode:
              </div>
              <div class="infor">
                {{ order.payment_mode }}
              </div>
            </td>
          </tr>
        </VTable>
        <div class="d-flex">
          <VIcon
            icon="mdi-package-variant"
            class="icon-header"
          />
          <VCardTitle>ORDER DETAIL</VCardTitle>
        </div>
        <VList>
          <VListItem
            v-for="product in producList"
            :key="product.id"
          >
            <VCard
              class="d-flex pt-2 card"
              @click="viewProduct(product.id)"
            >
              <div class="mx-3 product-name">
                {{ product.product_name }}
              </div>
              <div class="mx-3">
                Brand: {{ product.brand_name }}
              </div>
              <div class="mx-3">
                Size: {{ product.size_name }}
              </div>
              <div class="mx-3 color-name">
                {{ product.color_name }}
              </div>
              <div class="mx-3 ">
                <VSheet
                  :class="model"
                  class=" mb-2 "
                  elevation="12"
                  rounded="circle"
                  :color="product.color_code"
                  height="30"
                  width="30"
                />
              </div>
              <div class="mx-3">
                X{{ product.quantity }}
              </div>
              <div class="mx-3 ">
                <h5>Discount: {{ product.discount }}%</h5> 
              </div>
              <div class="mx-3 price">
                <h5>Price: ${{ product.price }}</h5>
              </div>
            </VCard>
          </VListItem>
        </VList>
        <div class="d-flex flex-row-reverse total-price py-4">
          <h3 class="">
            Total Price: ${{ order.total_price }}
          </h3>
        </div>
        <VTimeline direction="horizontal">
          <VTimelineItem
            size="40"
            :dot-color="orderedStatus.dotColor"
            :icon-color="orderedStatus.iconColor"
            icon="mdi-package-variant-closed-check"
          >
            <template #default>
              Ordered
            </template>
          </VTimelineItem>

          <VTimelineItem
            size="40"
            :dot-color="pendingStatus.dotColor"
            :icon-color="pendingStatus.iconColor"
            icon="mdi-cash-multiple"
          >
            <template #opposite>
              Pending
            </template>
          </VTimelineItem>

          <VTimelineItem
            size="40"
            :dot-color="deliveringStatus.dotColor"
            :icon-color="deliveringStatus.iconColor"
            icon="mdi-truck-delivery"
          >
            <template #default>
              Delivering
            </template>
          </VTimelineItem>

          <VTimelineItem
            size="40"
            :dot-color="receivedStatus.dotColor"
            :icon-color="receivedStatus.iconColor"
            icon="mdi-package-down"
          >
            <template #opposite>
              Received
            </template>
          </VTimelineItem>
        </VTimeline>
      </VCard>
    </VCol>
  </VRow>
</template>

<style scoped>
.title-table{
    width: 400px;
    max-width: 400px;
}
.title{
    margin-top: 5px;
    display: flex;
    margin-left: 10px;
    margin-bottom: 10px;
    font-weight: bold   
}
.icon{
    margin-right: 10px;
}
.avatar{
  margin-top: 10px;
  margin-left: 10px;
  margin-right: 20px;
}
.icon-header{
  margin-top: 11px;
  margin-left: 15px;
}
.warehouse{
  cursor: pointer;
  margin-top: 5px;
  margin-left: 20px;
  display: flex;
  margin-bottom: 10px;
  font-weight: bold 
}
.sliders{
    width: 500px;
    border: 1px solid #9155FD;
    border-radius: 10px
}
.color{
  width: 1000px;
}
.field{
  width: 230px;
  margin: 20px;
}
.infor{
    margin-top: 5px;
    position: relative;
    left: 50px;
    max-width: 300px;
}
.color-name{
    max-width: 200px;
    width: 200px;
}
.product-name{
    width: 150px;
    max-width: 150px;
}
.price{
    padding-left: 50px;
}
.card{
    cursor: pointer;
}
.card:hover{
    background-color: #D1C4E9;
}
.total-price{
    margin-right: 75px;
    margin-bottom: 30px;
}
.status{
    margin-left: 300px;
    margin-bottom: 20px;
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
</style>