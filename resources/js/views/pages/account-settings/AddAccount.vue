<script setup>
import avatar1 from '@images/avatars/avatar-1.png'
import { useStore } from 'vuex'
import { roles } from '../../../constants/roles'
import { city, alert } from '../../../constants/cities'
import axiosIns from '@/plugins/axios'

const accountData = {
  image: avatar1,
  firstname: '',
  lastname: '',
  name: '',
  password: '',
  email: '',
  warehouse_id: null,
  phone: '',
  date_of_birth: '',
  role: null,
  salary: null,
  address: '',
}

const warehouseList = ref([])

const store = useStore()

const refInputEl = ref()
const accountDataLocal = ref(structuredClone(accountData))

const resetForm = () => {
  accountDataLocal.value = structuredClone(accountData)
}

const getAllWarehouse = computed(()=> store.getters.getAllWarehouse)

onMounted(async () => {
  await store.dispatch('getAllWarehouse')
  warehouseList.value.push(...getAllWarehouse.value)
  console.log(getAllWarehouse.value)
})

const getId = warehouse => warehouse.id

const formatName = warehouse=>{
  return warehouse.id + ' - ' + warehouse.name
}

const submit = async ()=>{
  console.log(accountDataLocal.value)

  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.post('/api/auth/register', accountDataLocal.value, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res)
    if(res.status === 201){
      alert.title = 'Thành công'
      alert.status = true
      alert.text = 'Tạo thêm tài khoản thành công'
      alert.color = 'rgba(39, 217, 11, 0.8)'
      accountDataLocal.value = structuredClone(accountData)
    }
    else{
      alert.title = 'Cảnh Báo'
      alert.status = true
      alert.text = 'Có lỗi đang xảy ra'
      alert.color = 'rgba(234, 223, 30, 0.8)'
    }
  }).catch(err=>{
    console.log(err)
    alert.title = 'Lỗi'
    alert.status = true
    alert.text = err.response
    alert.color = 'rgba(222, 29, 29, 0.8)'
  })
}


const changeAvatar = file => {
  const fileReader = new FileReader()
  const { files } = file.target
  if (files && files.length) {
    fileReader.readAsDataURL(files[0])
    fileReader.onload = () => {
      if (typeof fileReader.result === 'string')
        accountDataLocal.value.image = fileReader.result
    }
  }
}

// reset avatar image
const resetAvatar = () => {
  accountDataLocal.value.image = accountData.image
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
        title="Tạo tài khoản"
        prepend-icon="mdi-account-plus"
      >
        <VCardText class="d-flex">
          <!-- 👉 Avatar -->
          <VAvatar
            rounded="lg"
            size="100"
            class="me-6"
            :image="accountDataLocal.image"
          />

          <!-- 👉 Upload Photo -->
          <form class="d-flex flex-column justify-center gap-5">
            <div class="d-flex flex-wrap gap-2">
              <VBtn
                color="primary"
                @click="refInputEl?.click()"
              >
                <VIcon
                  icon="mdi-cloud-upload-outline"
                  class="d-sm-none"
                />
                <span class="d-none d-sm-block">Thêm ảnh mới</span>
              </VBtn>

              <input
                ref="refInputEl"
                type="file"
                name="file"
                accept=".jpeg,.png,.jpg,GIF"
                hidden
                @input="changeAvatar"
              >

              <VBtn
                type="reset"
                color="error"
                variant="tonal"
                @click="resetAvatar"
              >
                <span class="d-none d-sm-block">Reset</span>
                <VIcon
                  icon="mdi-refresh"
                  class="d-sm-none"
                />
              </VBtn>
            </div>

            <!--
              <p class="text-body-1 mb-0">
              Allowed JPG, GIF or PNG. Max size of 800K
              </p> 
            -->
          </form>
        </VCardText>

        <VDivider />

        <VCardText>
          <!-- 👉 Form -->
          <VForm class="mt-6">
            <VRow>
              <!-- 👉 First Name -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="accountDataLocal.firstname"
                  label="Tên"
                />
              </VCol>
              <!-- 👉 Last Name -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="accountDataLocal.lastname"
                  label="Họ"
                />
              </VCol>

              <!-- 👉 User Name -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="accountDataLocal.name"
                  label="Tên người dùng"
                />
              </VCol>

              <!-- 👉 Password -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="accountDataLocal.password"
                  type="password"
                  label="Mật khẩu"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="accountDataLocal.email"
                  label="E-mail"
                  type="email"
                />
              </VCol>

              <!-- 👉 Warehouse -->
              <VCol
                cols="12"
                md="6"
              >
                <VSelect
                  v-model="accountDataLocal.warehouse_id"
                  label="Kho"
                  :items="warehouseList"
                  :item-title="formatName"
                  :item-value="getId"
                />
              </VCol>

              <!-- 👉 Phone -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="accountDataLocal.phone"
                  label="Số điện thoại"
                />
              </VCol>

              <!-- 👉 Date of birth -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="accountDataLocal.date_of_birth"
                  type="date"
                  label="Ngày sinh"
                />
              </VCol>

              <!-- 👉 Role -->
              <VCol
                cols="12"
                md="6"
              >
                <VSelect
                  v-model="accountDataLocal.role"
                  label="Chức vụ"
                  :items="roles"
                />
              </VCol>

              <!-- 👉 Salary -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="accountDataLocal.salary"
                  label="Lương"
                />
              </VCol>
              
              <!-- 👉 City -->
              <VCol
                cols="12"
                md="6"
              >
                <VSelect
                  v-model="accountDataLocal.city"
                  label="Thành Phố"
                  :items="city"
                />
              </VCol>

              <!-- 👉 Address -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="accountDataLocal.address"
                  label="Địa chỉ"
                />
              </VCol>

              


              <!-- 👉 Form Actions -->
              <VCol
                cols="12"
                class="d-flex flex-wrap gap-4"
              >
                <VBtn @click="submit">
                  Thêm tài khoản 
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

    <!-- 👉 Deactivate Account -->
    <!--
      <VCol cols="12">
      <VCard title="Deactivate Account">
      <VCardText>
      <div>
      <VCheckbox
      v-model="isAccountDeactivated"
      label="I confirm my account deactivation"
      />
      </div>

      <VBtn
      :disabled="!isAccountDeactivated"
      color="error"
      class="mt-3"
      >
      Deactivate Account
      </VBtn>
      </VCardText>
      </VCard>
      </VCol> 
    -->
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
