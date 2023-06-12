<script setup>
import avatar1 from '@images/avatars/avatar-1.png'
import { useStore } from 'vuex'

const warehouseData = {
  warehousename: '',
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

const submit = ()=>{
  console.log(accountDataLocal.value)
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
                  v-model="accountDataLocal.warehousename"
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
                <VBtn @click="submit">Add Warehouse</VBtn>

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