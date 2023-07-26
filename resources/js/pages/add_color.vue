<script setup>
import axiosIns from '@/plugins/axios'
import { reactive } from 'vue'
import { useStore } from 'vuex'

const alert = reactive({
  status: false,
  title: '',
  text: '',
  color: '',

})


const colorData = {
  name: '',
  code_color: '#F44336',
}


const store = useStore()
const accountDataLocal = ref(structuredClone(colorData))


const submit = async ()=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.post('/api/add_color', accountDataLocal.value, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  })
    .then(res=>{
      if(res.status === 201){
        alert.title = 'Thêm thành công'
        alert.status = true
        alert.text = 'Màu sắc đã được thêm thành công'
        alert.color = 'rgba(39, 217, 11, 0.8)'
        accountDataLocal.value = structuredClone(colorData)
        console.log(res)
      }
      else{
        alert.title = 'Cảnh báo'
        alert.status = true
        alert.text = 'Có lỗi gì đó'
        alert.color = 'rgba(234, 223, 30, 0.8)'
        console.log(res)
      }
    })
    .catch(err=>{
      alert.title = 'Lỗi'
      alert.status = true
      alert.text = err.response.data.message
      alert.color = 'rgba(222, 29, 29, 0.8)'
      console.log(accountDataLocal.value)
      console.log(err)
    })
}



const resetForm = () => {
  accountDataLocal.value = structuredClone(colorData)
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
        title="Thêm màu"
        prepend-icon="mdi-format-color-fill"
      >
        <VDivider />

        <VCardText>
          <!-- 👉 Form -->
          <VForm class="mt-6">
            <VRow>
              <!-- 👉 Color Name -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="accountDataLocal.name"
                  prepend-icon="mdi-rename"
                  label="Tên màu"
                />
              </VCol>

              <!-- 👉 Color Code -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="accountDataLocal.code_color"
                  prepend-icon="mdi-code-brackets"
                  label="Mã màu"
                />
              </VCol>

              <!-- 👉 Form Actions -->
              <VCol
                cols="12"
                class="d-flex flex-wrap gap-4"
              >
                <VBtn @click="submit">
                  Thêm màu
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