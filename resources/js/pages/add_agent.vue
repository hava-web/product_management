<script setup>
import axiosIns from '@/plugins/axios'
import { reactive } from 'vue'
import { useStore } from 'vuex'
import { city, agentStatus } from '@/constants/cities'

const alert = reactive({
  status: false,
  title: '',
  text: '',
  color: '',

})


const agentData = {
  name: '',
  city: null,
  address: '',
  status: null,
}


const store = useStore()
const accountDataLocal = ref(structuredClone(agentData))


const submit = async ()=>{
  console.log(accountDataLocal.value)

  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.post('/api/add_agent', accountDataLocal.value, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  })
    .then(res=>{
      if(res.status === 201){
        alert.title = 'Successfully'
        alert.status = true
        alert.text = 'Agent Added Successfully'
        alert.color = 'rgba(39, 217, 11, 0.8)'
        accountDataLocal.value = structuredClone(agentData)
        console.log(res)
      }
      else{
        alert.title = 'Warning'
        alert.status = true
        alert.text = 'Something went wrong'
        alert.color = 'rgba(234, 223, 30, 0.8)'
        console.log(res)
      }
    })
    .catch(err=>{
      alert.title = 'Error'
      alert.status = true
      alert.text = err.response.data.message
      alert.color = 'rgba(222, 29, 29, 0.8)'
      console.log(accountDataLocal.value)
      console.log(err)
    })
}

const resetForm = () => {
  accountDataLocal.value = structuredClone(agentData)
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
        title="Thêm chi nhánh"
        prepend-icon="mdi-tag-heart"
      >
        <VDivider />

        <VCardText>
          <!-- 👉 Form -->
          <VForm class="mt-6">
            <VRow>
              <!-- 👉 Agent Name -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="accountDataLocal.name"
                  label="Tên chi nhánh"
                />
              </VCol>

              <!-- 👉 City -->
              <VCol
                cols="12"
                md="6"
              >
                <VSelect
                  v-model="accountDataLocal.city"
                  label="Thành phố"
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

              <!-- 👉 Status -->
              <VCol
                cols="12"
                md="6"
              >
                <VSelect
                  v-model="accountDataLocal.status"
                  label="Trạng thái"
                  :items="agentStatus"
                />
              </VCol>

              <!-- 👉 Form Actions -->
              <VCol
                cols="12"
                class="d-flex flex-wrap gap-4"
              >
                <VBtn @click="submit">
                  Thêm chi nhánh
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
.image{
  margin-top: 10px;
  margin-left: 40px;
  display: flex;
}
</style>