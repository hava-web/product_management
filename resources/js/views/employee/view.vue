<script setup>
import { useRouter, useRoute } from 'vue-router'
import axiosIns from '@/plugins/axios'
import { reactive } from 'vue'

const route = useRoute()
const router = useRouter()

const employee = reactive({
  image: '',
  firstname: '',
  lastname: '',
  username: '',
  email: '',
  phone: '',
  warehouse: null,
  date_of_birth: '',
  role: '',
  user_id: null,
  salary: '',
  city: '',
  address: '',
})

const warehouse = ref()

const id = route.params.id

onMounted( async () => {
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/employee/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    employee.image = res.data.image
    employee.firstname = res.data.firstname
    employee.lastname = res.data.lastname
    employee.date_of_birth = res.data.date_of_birth
    employee.city = res.data.city
    employee.warehouse = res.data.warehouse_id
    employee.phone = res.data.phone
    employee.salary = res.data.salary
    employee.address = res.data.address
    employee.user_id = res.data.user_id
    console.log(res.data)
  }).catch(err=>{
    console.log(err)
  })
})

watchEffect( async ()=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/warehouse/' + employee.warehouse, {
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

  await axiosIns.get('/api/user/' + employee.user_id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    employee.role = res.data.role
    employee.email = res.data.email
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
        title="Employee"
        prepend-icon="mdi-badge-account-outline"
      >
        <VDivider />
        <div class="d-flex">
          <VIcon
            icon="mdi-information-outline"
            class="icon-header"
          />
          <VCardTitle>BASIC INFORMATION</VCardTitle>
        </div>
        <VCardText class="d-flex">
          <!-- 👉 Avatar -->
          <VAvatar
            rounded="lg"
            size="200"
            class="me-6 avatar"
            :image="employee.image"
          />

          <!-- 👉 Upload Photo -->
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
                    icon="mdi-account-credit-card"
                  />
                  <div class="">
                    First Name:
                  </div>
                </div>
              </td>
              <td class="title">
                {{ employee.firstname }}
              </td>
            </tr>
            <tr>
              <td class="">
                <div class="title">
                  <VIcon
                    class="icon"
                    icon="mdi-account-credit-card-outline"
                  />
                  <div class="">
                    Last Name:
                  </div>
                </div>
              </td>
              <td class="title">
                {{ employee.lastname }}
              </td>
            </tr>
            <tr>
              <td class="">
                <div class="title">
                  <VIcon
                    class="icon"
                    icon="mdi-cake-variant"
                  />
                  <div class="">
                    Date Of Birth:
                  </div>
                </div>
              </td>
              <td class="title">
                {{ employee.date_of_birth }}
              </td>
            </tr>
            <tr>
              <td class="">
                <div class="title">
                  <VIcon
                    class="icon"
                    icon="mdi-city"
                  />
                  <div class="">
                    City:
                  </div>
                </div>
              </td>
              <td class="title">
                {{ employee.city }}
              </td>
            </tr>
            <tr>
              <td class="">
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
              <td class="title">
                {{ employee.address }}
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
                  icon="mdi-email-outline"
                />
                <div class="">
                  Email:
                </div>
              </div>
            </td>
            <td class="title">
              {{ employee.email }}
            </td>
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
            <td
              class="warehouse"
              @click="router.push('/view_warehouse/warehouse/' + employee.warehouse)"
            >
              {{ warehouse }}
            </td>
          </tr>
          <tr>
            <td class="title-table">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-account-tie"
                />
                <div class="">
                  Role:
                </div>
              </div>
            </td>
            <td class="title">
              {{ employee.role }}
            </td>
          </tr>
          <tr>
            <td class="title-table">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-phone"
                />
                <div class="">
                  Phone:
                </div>
              </div>
            </td>
            <td class="title">
              {{ employee.phone }}
            </td>
          </tr>
          <tr>
            <td class="title-table">
              <div class="title">
                <VIcon
                  class="icon"
                  icon="mdi-cash-multiple"
                />
                <div class="">
                  Salary:
                </div>
              </div>
            </td>
            <td class="title">
              ${{ employee.salary }}
            </td>
          </tr>
        </VTable>
        <VDivider />
        <VCard
          title="Employees List" 
          prepend-icon="mdi-account-box"
        />
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
</style>