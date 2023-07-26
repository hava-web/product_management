<script setup>
import axiosIns from '@/plugins/axios'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'


const store = useStore()
const userList = ref([])
const router = useRouter()

onMounted(async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/employees_users/', {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    userList.value.push(...res.data)
    console.log(userList.value)
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
  'HỌ VÀ TÊN',
  'EMAIL',
  'NGÀY SINH',
  'LƯƠNG',
]
</script>

<template>
  <VCard>
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
          v-for="row in userList"
          :key="row"
          class="user"
          @click="userView(row.employee_id)"
        >
          <!-- name -->

          <td>
            <div class="d-flex flex-column">
              <h6 class="text-sm font-weight-medium">
                {{ row.full_name }}
              </h6>
            </div>
          </td>

          <td
            class="text-sm"
            v-text="row.email"
          />
          <td
            class="text-sm"
            v-text="row.date_of_birth"
          />
          <td
            class="text-sm"
            v-text="`${row.salary} VND`"
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
