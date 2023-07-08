<script setup>
import axiosIns from '@/plugins/axios'
import { useRouter } from 'vue-router'

const total_customer = ref()
const total_product = ref()
const total_order = ref()
const revenue = ref()
const router = useRouter()

const test = 0

onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('api/total_customer', {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    total_customer.value = Number(res.data)
    console.log(res.data)
  }).catch(err=>{
    console.log(err.data)
  })
})

onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('api/total_product', {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    total_product.value = Number(res.data)
    console.log(res.data)
  }).catch(err=>{
    console.log(err.data)
  })
})

onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('api/total_order', {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    total_order.value = Number(res.data)
    console.log(total_order.value)
  }).catch(err=>{
    console.log(err.data)
  })
})

onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('api/revenue', {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    revenue.value = Number(res.data) 
    console.log(revenue.value)
  }).catch(err=>{
    console.log(err.data)
  })
})

const view = link=>{
  router.push(link)
}

function abbreviateNumber(value) {
  var newValue = value
  if (value >= 1000) {
    var suffixes = ["", "K", "M", "B", "T"]
    var suffixNum = Math.floor( (""+value).length/3 )
    var shortValue = ''
    for (var precision = 2; precision >= 1; precision--) {
      shortValue = parseFloat( (suffixNum != 0 ? (value / Math.pow(1000, suffixNum) ) : value).toPrecision(precision))
      var dotLessShortValue = (shortValue + '').replace(/[^a-zA-Z 0-9]+/g, '')
      if (dotLessShortValue.length <= 2) { break }
    }
    if (shortValue % 1 != 0)  shortValue = shortValue.toFixed(1)
    newValue = shortValue+suffixes[suffixNum]
  }
  
  return newValue
}

const statistics = [
  {
    title: 'Sales',
    stats: abbreviateNumber(total_order),
    icon: 'mdi-trending-up',
    color: 'primary',
    link: '/view_order',
  },
  {
    title: 'Customers',
    stats: abbreviateNumber(total_customer),
    icon: 'mdi-account-outline',
    color: 'success',
    link: '/view_customer',
  },
  {
    title: 'Product',
    stats: abbreviateNumber(total_product),
    icon: 'mdi-cellphone-link',
    color: 'warning',
    link: '/view_product',
  },
  {
    title: 'Revenue',
    stats: abbreviateNumber(revenue),
    icon: 'mdi-currency-usd',
    color: 'info',
    link: '/revenue',
  },
]
</script>

<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Transactions</VCardTitle>

      <template #append>
        <div class="me-n3">
          <MoreBtn />
        </div>
      </template>
    </VCardItem>

    <VCardText>
      <h6 class="text-sm font-weight-medium mb-12">
        <span>Total 48.5% Growth 😎</span>
        <span class="font-weight-regular"> this month</span>
      </h6>

      <VRow>
        <VCol
          v-for="item in statistics"
          :key="item.title"
          cols="6"
          sm="3"
        >
          <div class="d-flex align-center">
            <div class="me-3">
              <VAvatar
                :color="item.color"
                variant="elevated"
                rounded
                size="42"
                class="elevation-1 transaction-item"
                @click="view(item.link)"
              >
                <VIcon
                  size="24"
                  :icon="item.icon"
                />
              </VAvatar>
            </div>

            <div class="d-flex flex-column">
              <span class="text-caption">
                {{ item.title }}
              </span>
              <span class="text-h6">{{ item.stats }}</span>
            </div>
          </div>
        </VCol>
      </VRow>
    </VCardText>
  </VCard>
</template>

<style scoped>
.transaction-item{
  cursor: pointer;
}
</style>
