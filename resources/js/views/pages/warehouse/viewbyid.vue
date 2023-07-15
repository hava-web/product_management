<script setup>
import { useRouter, useRoute } from 'vue-router'
import axiosIns from '@/plugins/axios'
import { reactive } from 'vue'

const route = useRoute()
const router = useRouter()
const employeeList = ref([])

const warehouse = reactive({
  id: '',
  name: '',
  manager: null,
  city: '',
  status: '',
  address: '',
})

const manager = ref('')

const id = route.params.id

onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/warehouse/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    warehouse.id = res.data.id
    warehouse.name = res.data.name
    warehouse.manager = res.data.manager
    warehouse.city = res.data.city
    warehouse.status = res.data.status
    warehouse.address = res.data.address
  }).catch(err=>{
    console.log(err)
  })
})

onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/employees/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    employeeList.value.push(...res.data)
    console.log(employeeList.value)
  }).catch(err=>{
    console.log(err)
  })
})

watchEffect( async ()=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/user/' + warehouse.manager, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    manager.value = res.data.name
    console.log(res.data)

  }).catch(err=>{
    console.log(err)
  })
}, { immediate: true })

const viewEmployee = id =>{
  router.push({ name: 'employee', params: { id: id } })
}
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard 
        title="Warehouse"
        prepend-icon="mdi-store-plus-outline"
      >
        <VDivider />
        <VTable>
          <tr>
            <td class="title-table">
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
            <td>{{ warehouse.id }}</td>
          </tr>
          <tr>
            <td class="title-table">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-warehouse"
                />
                <div class="">
                  Warehouse Name:
                </div>
              </div>
            </td>
            <td>{{ warehouse.name }}</td>
          </tr>
          <tr>
            <td class="title-table">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-account-tie"
                />
                <div class="">
                  Manager:
                </div>
              </div>
            </td>
            <td>{{ manager }}</td>
          </tr>
          <tr>
            <td class="title-table">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-city-variant"
                />
                <div class="">
                  City:
                </div>
              </div>
            </td>
            <td>{{ warehouse.city }}</td>
          </tr>
          <tr>
            <td class="title-table">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-bulletin-board"
                />
                <div class="">
                  Status:
                </div>
              </div>
            </td>
            <td>{{ warehouse.status }}</td>
          </tr>
          <tr>
            <td class="title-table">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-map-marker"
                />
                <div class="">
                  Address:
                </div>
              </div>
            </td>
            <td>{{ warehouse.address }}</td>
          </tr>
        </VTable>
        <VDivider />
        <VCard
          title="Employees List" 
          prepend-icon="mdi-account-box"
        />
        <VList>
          <VCard
            v-for="employee in employeeList"
            :key="employee.id"
            class="employee mx-3 my-2"
            @click="viewEmployee(employee.id)"
          >
            <div class="d-flex">
              <div class=" mx-5">
                ID: {{ employee.id }}
              </div>
              <div class="mx-5">
                Full Name: {{ employee.lastname + ' ' + employee.firstname }}
              </div>
              <div class="px-5">
                <VImg
                  :src="employee.image"
                  width="50"
                  height="50"
                />
              </div>
              <div class="mx-5">
                Date: {{ employee.date_of_birth }}
              </div>
              <div class="mx-5">
                Phone: {{ employee.phone }}
              </div>
              <div class="mx-5">
                Email: {{ employee.email }}
              </div>
            </div>
          </VCard>
        </VList>
      </VCard>
    </VCol>
  </VRow>
</template>

<style scoped>
.title-table{
    width: 500px;
    max-width: 500px;
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
.employee{
  cursor: pointer;
}
.employee:hover{
  background: #ECEFF1;
}
</style>