<script setup>
import axiosIns from '@/plugins/axios'
import avatar1 from '@images/avatars/avatar-1.png'
import { reactive } from 'vue'
import { useStore } from 'vuex'

const alert = reactive({
  status: false,
  title: '',
  text: '',
  color: '',

})

const warehouseData = {
  name: '',
  manager: null,
  city: '',
  status: '',
  address: '',
}

const city = [
  'Ho Chi Minh City',
  'Hanoi',
  'Da Nang',
  'Can Tho',
  'Hai Phong',
  'Nha Trang',
  'Hue',
  'Ba Ria',
  'Vung Tau',
  'Qui Nhon',
  'Rach Gia',
  'Sa Dec',
  'Vinh',
  'Ha Tinh',
  'Thai Nguyen',
  'Lang Son',
  'Dien Bien Phu',
  'Da Lat',
  'Pleiku',
  'Phan Thiet',
  'Ha Long',
  'Tam Ky',
]

const status = [
  'Open',
  'Closed',
  'Modifing',
]

const store = useStore()
const managerList = ref([])
const accountDataLocal = ref(structuredClone(warehouseData))
const isAccountDeactivated = ref(false)

const getAllUser = computed(() => store.getters.getAllUser)

onMounted( async () => {
  await store.dispatch('getAllUser') 
  managerList.value.push(...getAllUser.value)
  console.log(managerList.value)
})

const submit = async ()=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.post('/api/add_wareshouse', accountDataLocal.value, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  })
    .then(res=>{
      if(res.status === 201){
        alert.title = 'Successfully'
        alert.status = true
        alert.text = 'Warehouse Added Successfully'
        alert.color = 'rgba(39, 217, 11, 0.8)'
        accountDataLocal.value = structuredClone(warehouseData)
      }
      else{
        alert.title = 'Warning'
        alert.status = true
        alert.text = 'Something went wrong'
        alert.color = 'rgba(234, 223, 30, 0.8)'
      }
    })
    .catch(err=>{
      alert.title = 'Error'
      alert.status = true
      alert.text = err.response.data.message
      alert.color = 'rgba(222, 29, 29, 0.8)'
      console.log(err)
    })
}

const getId = manager => manager.id

const formatName = ()=>{
  return managerList.value.map(manager => manager.id + ' - ' + manager.name)
}

const resetForm = () => {
  accountDataLocal.value = structuredClone(warehouseData)
}


// reset avatar image
const resetAvatar = () => {
  accountDataLocal.value.avatarImg = accountData.avatarImg
}
</script>

<template>
  <Transition name="slide-fade">
    <VAlert 
      v-if="alert.status"
      :color="alert.color"
      icon=""
      :title="alert.title"
      closable
      class="alert"
      max-width="400px"
      :text="alert.text"
      @click:close="alert.status = false"
    />
  </Transition>
  <VRow>
    <VCol cols="12">
      <VCard 
        title="Add Warehouse"
        prepend-icon="mdi-store-plus-outline"
      >
        <VDivider />

        <VCardText>
          <!-- 👉 Form -->
          <VForm class="mt-6">
            <VRow>
              <!-- 👉 Warehouse Name -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="accountDataLocal.name"
                  label="Warehouse Name"
                />
              </VCol>

              <!-- 👉 Manager -->
              <VCol
                md="6"
                cols="12"
              >
                <VSelect
                  v-model="accountDataLocal.manager"
                  :items="managerList"
                  :item-value="getId"
                  :item-title="formatName"
                  label="Manager"
                />
              </VCol>

              <!-- 👉 City -->
              <VCol
                cols="12"
                md="6"
              >
                <VSelect
                  v-model="accountDataLocal.city"
                  :items="city"
                  label="City"
                />
              </VCol>

              <!-- 👉 Status -->
              <VCol
                cols="12"
                md="6"
              >
                <VSelect
                  v-model="accountDataLocal.status"
                  :items="status"
                  label="Status"
                />
              </VCol>

              <!-- 👉 Address -->
              <VCol cols="12">
                <VTextField
                  v-model="accountDataLocal.address"
                  label="Address"
                />
              </VCol>

              <!-- 👉 Form Actions -->
              <VCol
                cols="12"
                class="d-flex flex-wrap gap-4"
              >
                <VBtn @click="submit">
                  Add Warehouse
                </VBtn>

                <VBtn
                  color="secondary"
                  variant="tonal"
                  type="reset"
                  @click.prevent="resetForm"
                >
                  Reset
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<style scoped>
  .alert{
  position: absolute;
  top: 20px;
  right: 10px;
  z-index: 100;
}

.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.8s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
</style>