<script setup>
import { useRouter, useRoute } from 'vue-router'
import axiosIns from '@/plugins/axios'
import { reactive } from 'vue'
import { orderStatus, alert } from '../../constants/cities'

const route = useRoute()
const router = useRouter()
const orderList = ref([])


const customer = reactive({
  id: null,
  firstname: '',
  lastname: '',
  email: '',
  phone: '',
  address: '',
})


const id = route.params.id


onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/customer/' + id, {
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

  await axiosIns.get('/api/order_by_cus/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    orderList.value.push(...res.data)
    console.log(res.data)
  }).catch(err=>{
    console.log(err)
  })
})


const viewOrder = id =>{
  router.push({ name: 'order', params: { id: id } })
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
        :title="'CUSTOMER ' + id"
        prepend-icon="mdi-package-variant-closed"
      >
        <VDivider />
        <div class="d-flex my-5">
          <VIcon
            icon="mdi-account"
            class="icon-header"
          />
          <VCardTitle>CUSTOMER INFORMATION</VCardTitle>
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
          </tr>
        </VTable>
        <div class="d-flex">
          <VIcon
            icon="mdi-package-variant"
            class="icon-header"
          />
          <VCardTitle>ORDER LIST</VCardTitle>
        </div>
        <VList>
          <VListItem
            v-for="order in orderList"
            :key="order.id"
          >
            <VCard
              class="d-flex pt-2 card"
              @click="viewOrder(order.id)"
            >
              <div class="mx-3 product-name">
                {{ order.id }}
              </div>
              <div class="mx-3 product-name">
                Total Price: ${{ order.total_price }}
              </div>
              <div class="mx-3 product-name">
                Status: {{ order.status }}
              </div>
              <div class="mx-3 color-name">
                Payment Mode: {{ order.payment_mode }}
              </div>
            </VCard>
          </VListItem>
        </VList>
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
    max-width: 300px;
    width: 300px;
}
.product-name{
    width: 200px;
    max-width: 200px;
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