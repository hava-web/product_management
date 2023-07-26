<script setup>
import axiosIns from '@/plugins/axios'
import { useRouter } from 'vue-router'

const props = defineProps({
  id: Number,
})

const orderList = ref([])

onMounted(async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/orders_list_emp/' + props.id, {
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

const profit = ()=>{
    
}

const headers = [
  'ID',
  'CUSTOMER',
  'TOTAL PRICE',
  'EMPLOYEE',
  'STATUS',
  'PAYMENT MODE',
]
</script>

<template>
  <VCard>
    <div class="mx-3 my-3">
      Profit:
    </div>
  </VCard>
</template>

<style scoped>
.user{
  cursor: pointer;
}
.user:hover{
  background: #ECEFF1;
}
</style>