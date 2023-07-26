<script setup>
import axiosIns from '@/plugins/axios'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'

const props = defineProps({
  id: Number,
})

const router = useRouter()

const view = id =>{
  router.push({ name: 'order', params: { id: id } })
}

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

const headers = [
  'ID',
  'KHÁCH HÀNG',
  'TỔNG GIÁ TRỊ',
  'NHÂN VIÊN',
  'TRẠNG THÁI',
  'PHƯƠNG THỨC THANH TOÁN',
]
</script>

<template>
  <VCard
    title="Đơn hàng chốt được"
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
          v-for="row in orderList"
          :key="row"
          class="user"
          @click="view(row.id)"
        >
          <!-- name -->

          <td>
            <div class="d-flex flex-column">
              <h6 class="text-sm font-weight-medium">
                {{ row.id }}
              </h6>
            </div>
          </td>

          <td
            class="text-sm"
            v-text="row.customer"
          />
          <td
            class="text-sm"
            v-text="row.total_price"
          />
          <td
            class="text-sm"
            v-text="row.employee"
          />
          <td
            class="text-sm"
            v-text="row.status"
          />
          <td
            class="text-sm"
            v-text="row.payment_mode"
          />
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