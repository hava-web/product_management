<script setup>
import axiosIns from '@/plugins/axios'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'

const props = defineProps({
  id: Number,
})

const productList = ref([])

onMounted(async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/products_infor/' + props.id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    productList.value.push(...res.data)
    console.log(res.data)
  }).catch(err=>{
    console.log(err)
  })
})

const statusColor = {
  Current: 'primary',
  Professional: 'success',
  Rejected: 'error',
  Resigned: 'warning',
  Applied: 'info',
}

const headers = [
  'CODE',
  'TÊN SẢN PHẨM',
  'THƯƠNG HIỆU',
  'ẢNH',
  'KÍCH THƯỚC',
  'MÀU SẮC',
  'SỐ LƯỢNG',
]
</script>

<template>
  <VCard
    title="Sản phẩm trong kho"
    prepend-icon="mdi-package-variant-closed"
  >
    <VTable
      :headers="headers"
      item-key="fullName"
      class="table-rounded"
      hide-default-footer
      disable-sort
    >
      <thead>
        <tr>
          <th
            v-for="header in headers"
            :key="header"
          >
            {{ header }}
          </th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="row in productList"
          :key="row"
          class="user"
        >
          <!-- name -->

          <td>
            <div class="d-flex flex-column">
              <h6 class="text-sm font-weight-medium">
                {{ row.product_code }}
              </h6>
            </div>
          </td>

          <td
            class="text-sm"
            v-text="row.name"
          />
          <td
            class="text-sm"
            v-text="row.brand_name"
          />
          <td>
            <VImg
              :width="50"
              class="item-image"
              alt="Image"
              content-class="text-center"
              cover
              :src="'/storage/images/' + row.image"
            />
          </td>
          <td
            class="text-sm"
            v-text="row.size_name"
          />
          <td>
            <VSheet
              :class="model"
              class="mt-2 mb-2 color"
              elevation="12"
              rounded="circle"
              :color="row.code_color"
              height="50"
              width="50"
            />
          </td>
          <td
            class="text-sm "
            v-text="row.quantity"
          />
          <!-- status -->
          <!--
            <td>
            <VChip
            size="small"
            :color="statusColor[status[row.status]]"
            class="text-capitalize"
            >
            {{ status[row.status] }}
            </VChip>
            </td> 
          -->
        </tr>
      </tbody>
    </VTable>
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