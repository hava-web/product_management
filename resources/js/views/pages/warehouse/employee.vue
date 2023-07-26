<script setup>
import axiosIns from '@/plugins/axios'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'

const props = defineProps({
  id: Number,
})

const store = useStore()
const employeeList = ref([])
const router = useRouter()

onMounted(async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/employees/' + props.id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    employeeList.value.push(...res.data)
    console.log(res.data)
  }).catch(err=>{
    console.log(err)
  })
})

const userView = id=>{
  router.push({ name: 'employee', params: { id: id } })
  console.log(id)
}


const statusColor = {
  Current: 'primary',
  Professional: 'success',
  Rejected: 'error',
  Resigned: 'warning',
  Applied: 'info',
}

const headers = [
  'ID',
  'HỌ VÀ TÊN',
  'ẢNH',
  'NGÀY SINH',
  'SỐ ĐIỆN THOẠI',
  'EMAIL',
]
</script>

<template>
  <VCard
    title="Danh sách nhân viên"
    prepend-icon="mdi-account-box"
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
          v-for="row in employeeList"
          :key="row"
          class="user"
          @click="userView(row.id)"
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
            v-text="row.lastname + ' ' + row.firstname" 
          />
          <td>
            <VImg
              :src="row.image"
              width="50"
              height="50"
            />
          </td>
          <td
            class="text-sm"
            v-text="row.date_of_birth"
          />
          <td
            class="text-sm"
            v-text="row.phone"
          />
          <td
            class="text-sm"
            v-text="row.email"
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