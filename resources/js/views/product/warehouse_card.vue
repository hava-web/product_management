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
      <VCol
        cols="12"
        sm="1"
        md="12"
        lg="7"
        order="2"
        order-lg="1"
      >
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
              <div class="d-flex">
                <div class="a">
                  <VSheet
                    class="color align-self-center"
                    elevation="12"
                    rounded="circle"
                    :color="property.color_code"
                    height="50"
                    width="50"
                  />
                </div>
                <div class="infor">
                  <div class="w-100 font-weight-bold mb-3">
                    <VIcon icon="mdi-palette-swatch" />
                    Color Name:  <span class="font-weight-normal">{{ property.color_name }}</span>
                  </div>
                  <div class="w-100 font-weight-bold mb-3">
                    <VIcon icon="mdi-view-list-outline" />
                    Quantity: <span class="font-weight-normal">{{ property.quantity }}</span>
                  </div>
                  <div class="w-100 font-weight-bold mb-3">
                    <VIcon icon="mdi-weight" />
                    Size: <span class="font-weight-normal">{{ property.size_name }}</span>
                  </div>
                  <div class="w-100 font-weight-bold mb-3">
                    <VIcon icon="mdi-eye-outline" />
                    Status: <span class="font-weight-normal">{{ property.status }}</span>
                  </div>
                  <div class="w-100 font-weight-bold mb-3">
                    <VIcon icon="mdi-clipboard-text-clock-outline" />
                    Expired Date: <span class="font-weight-normal">{{ property.expired_date }}</span>
                  </div>
                </div>
              </div>
            </VListItem>
          </VList>
        </VCardText>
      </VCol>

      <VCol
        cols="12"
        sm="4"
        md="12"
        lg="5"
        order="1"
        order-lg="2"
        class="member-pricing-bg text-center"
      >
        <div class="membership-pricing d-flex flex-column align-center py-14 h-100 justify-center">
          <p class="mb-5">
            <sub class="text-h5">$</sub>
            <sup class="text-h2 font-weight-medium">899</sup>
            <sub class="text-h5">USD</sub>
          </p>

          <VBtn
            class="mt-8"
            @click="viewWarehouse"
          >
            View Warehouse
          </VBtn>
        </div>
      </VCol>
    </VRow>
  </VCard>
</template>

<style scoped>
.color{
    margin-top: 50px;
    margin-left: 10px;
    margin-right: 10px;
}
.infor{
    max-width: 180px;
    width: 180px;
}
</style>