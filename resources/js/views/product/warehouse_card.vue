<script setup>
import { useRouter } from 'vue-router'
import axiosIns from '@/plugins/axios'

const props = defineProps({
  id: Number,
  productId: Number,
})

const router = useRouter()
const propertyList = ref([])

onMounted( async() => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/view_properties/' + props.productId + '/' + props.id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    propertyList.value.push(...res.data)
    console.log(propertyList.value)
  }).catch(err=>{
    console.log(err)
  })
})

const viewWarehouse = () => {
  router.push({ name: 'warehouse', params: { id: props.id } })
}
</script>

<template>
  <VCard>
    <VRow no-gutters>
      <VCol cols="12">
        <VCardItem>
          <VCardTitle>
            <VIcon icon="mdi-warehouse" /> 
            Warehouse {{ props.id }}
          </VCardTitle>
        </VCardItem>

        <VCardText class="justify-center">
          <VList lines="two">
            <VListItem
              v-for="property in propertyList"
              :key="property.propertyList"
            >
              <div class="d-flex w-100">
                <div class="d-flex align-items-center">
                  <VSheet
                    class="color"
                    elevation="12"
                    rounded="circle"
                    :color="property.color_code"
                    height="50"
                    width="50"
                  />
                </div>
                <div class="w-100">
                  <div class="d-flex">
                    <div class="w-100 font-weight-bold mb-3">
                      <VIcon icon="mdi-palette-swatch" />
                      Color Name:  <span class="font-weight-normal">{{ property.color_name }}</span>
                    </div>
                    <div class="w-100 font-weight-bold mb-3">
                      <VIcon icon="mdi-view-list-outline" />
                      Quantity: <span class="font-weight-normal">{{ property.quantity }}</span>
                    </div>
                  </div>
                  <div class="d-flex">
                    <div class="w-100 font-weight-bold mb-3">
                      <VIcon icon="mdi-weight" />
                      Size: <span class="font-weight-normal">{{ property.size_name }}</span>
                    </div>
                    <div class="w-100 font-weight-bold mb-3">
                      <VIcon icon="mdi-eye-outline" />
                      Status: <span class="font-weight-normal">{{ property.status }}</span>
                    </div>
                  </div>
                  <div class="d-flex">
                    <div class="w-100 font-weight-bold mb-3">
                      <VIcon icon="mdi-watermark" />
                      Brand: <span class="font-weight-normal">{{ property.brand_name }}</span>
                    </div>
                    <div class="w-100 font-weight-bold mb-3">
                      <VIcon icon="mdi-clipboard-text-clock-outline" />
                      Expired Date: <span class="font-weight-normal">{{ property.expired_date }}</span>
                    </div>
                  </div>
                  <div class="d-flex">
                    <div class="w-100 font-weight-bold mb-3">
                      <VIcon icon="mdi-currency-usd" />
                      Original Price: <span class="font-weight-normal">{{ property.original_price }}</span>
                    </div>
                    <div class="w-100 font-weight-bold mb-3">
                      <VIcon icon="mdi-cash-multiple" />
                      Selling Price: <span class="font-weight-normal">{{ property.selling_price }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </VListItem>
          </VList>
        </VCardText>
      </VCol>
      <VBtn
        class="mt-8"
        @click="viewWarehouse"
      >
        View Warehouse
      </VBtn>
    </VRow>
  </VCard>
</template>

<style scoped>
.color{
    margin-right: 10px;
}
.infor{
    max-width: 250px;
    width: 500px;
   
}
</style>