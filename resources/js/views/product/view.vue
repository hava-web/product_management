<script setup>
import { useRouter, useRoute } from 'vue-router'
import axiosIns from '@/plugins/axios'
import WarehouseCard from './warehouse_card.vue'
import { reactive } from 'vue'

const route = useRoute()
const router = useRouter()

const product = reactive({
  name: '',
  category: null,
  quantity: null,
  status: null,
  product_code: '',
  warehouse: null,
  imported_date: null,
  expired_date: null,
  size: null,
  warehouse_id: null,
  delivered_from: '',
  description: '',
})


const warehouse = ref()
const category = ref()
const warehouseList = ref([])

// const colorData = ref([])
const imageList = ref([])

const id = route.params.id

onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/product/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    product.name = res.data.name
    product.category = res.data.category
    product.quantity = res.data.quantity
    product.original_price = res.data.original_price
    product.selling_price = res.data.selling_price
    product.product_code = res.data.product_code
    product.imported_date = res.data.imported_date
    product.delivered_from = res.data.delivered_from
    product.description = res.data.description
    console.log(res.data)
  }).catch(err=>{
    console.log(err)
  })
})

onMounted( async() => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/get_warehouses/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    warehouseList.value.push(...res.data)
    console.log(res.data)
  }).catch(err=>{
    console.log(err)
  })
})

onMounted(async ()=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/get_images/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    imageList.value.push(...res.data)
    console.log(imageList.value)

  }).catch(err=>{
    console.log(err)
  })
})

const getDataColor = (id, dataArray) => {
  const result = dataArray.find(item => item.id == id)
  
  return result ? result.code_color : null
}




watchEffect( async ()=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/warehouse/' + product.warehouse_id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    warehouse.value = res.data.id + ' - ' + res.data.name
    console.log(res.data)

  }).catch(err=>{
    console.log(err)
  })
}, { immediate: true })

watchEffect( async ()=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/category/' + product.category, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    category.value = res.data.id + ' - ' + res.data.name
    console.log(res.data)

  }).catch(err=>{
    console.log(err)
  })
}, { immediate: true })
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard 
        :title="'Mã sản phẩm: ' + id"
        prepend-icon="mdi-package-variant-closed"
      >
        <VDivider />
        <div class="d-flex">
          <VIcon
            icon="mdi-information-outline"
            class="icon-header"
          />
          <VCardTitle>Thông tin cơ bản</VCardTitle>
        </div>
        <VCardText class="d-flex">
          <!-- 👉 Avatar -->
          <VCarousel
            height="300"
            class="sliders"
            hide-delimiter-background
            show-arrows="hover"
          >
            <VCarouselItem
              v-for="(image, i) in imageList"
              :key="i"
              :src="'/storage/images/' + image.image"
              cover
            />
          </VCarousel>
          <VTable>
            <tr>
              <td class="">
                <div class="title">
                  <VIcon
                    class="icon"
                    icon="mdi-id-card"
                  />
                  <div class="">
                    ID:
                  </div>
                </div>
              </td>
              <td class="title">
                {{ id }}
              </td>
            </tr>
            <tr>
              <td class="">
                <div class="title">
                  <VIcon
                    class="icon"
                    icon="mdi-palette-swatch"
                  />
                  <div class="">
                    Tên sản phẩm
                  </div>
                </div>
              </td>
              <td class="title">
                {{ product.name }}
              </td>
            </tr>
            <tr>
              <td class="">
                <div class="title">
                  <VIcon
                    class="icon"
                    icon="mdi-shape"
                  />
                  <div class="">
                    Danh mục:
                  </div>
                </div>
              </td>
              <td class="title">
                {{ category }}
              </td>
            </tr>
            <tr>
              <td class="">
                <div class="title">
                  <VIcon
                    class="icon"
                    icon="mdi-watermark"
                  />
                  <div class="">
                    Mã nhập:
                  </div>
                </div>
              </td>
              <td class="title">
                {{ product.product_code }}
              </td>
            </tr>
            <tr>
              <td class="">
                <div class="title">
                  <VIcon
                    class="icon"
                    icon="mdi-view-list-outline"
                  />
                  <div class="">
                    Số lượng:
                  </div>
                </div>
              </td>
              <td class="title">
                {{ product.quantity }}
              </td>
            </tr>
          </VTable>
        </VCardText>
        <VTable>
          <tr>
            <td class="title-table">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-import"
                />
                <div class="">
                  Ngày nhập:
                </div>
              </div>
            </td>
            <td class="title">
              {{ product.imported_date }}
            </td>
          </tr>
          <tr>
            <td class="title-table">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-image-text"
                />
                <div class="">
                  Mô tả:
                </div>
              </div>
            </td>
            <td class="title">
              {{ product.description }}
            </td>
          </tr>
        </VTable>
        <VDivider />
        <div class="d-flex">
          <div class="w-100">
            <VCardTitle>
              <VIcon icon="mdi-format-paint" />
              Kho hàng
            </VCardTitle>
            <div class="d-flex flex-wrap test">
              <VCol
                v-for="item in warehouseList"
                :key="item.warehouseList"
                class="d-flex "
                md="6"
                cols="2"
              >
                <WarehouseCard
                  :id="item"
                  :product-id="id"
                />
                <!--
                  <div class="d-flex pa-4">
                  <VSheet
                  class="mt-2 mb-2 "
                  elevation="12"
                  :color="getDataColor(color.color_id, colorData)"
                  rounded="circle"
                  height="50"
                  width="50"
                  />
                  <div class="field">
                  Quantity:
                  {{ color.quantity }} products
                  </div>
                  </div> 
                -->
              </VCol>
            </div>
          </div>
        </div>
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
    margin-left: 20px;
    display: flex;
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
</style>