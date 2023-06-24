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


const brandData = {
  name: '',
  status: 0,
}


const store = useStore()
const accountDataLocal = ref(structuredClone(brandData))


const submit = async ()=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.post('/api/add_brand', accountDataLocal.value, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  })
    .then(res=>{
      if(res.status === 201){
        alert.title = 'Successfully'
        alert.status = true
        alert.text = 'Brand Added Successfully'
        alert.color = 'rgba(39, 217, 11, 0.8)'
        accountDataLocal.value = structuredClone(brandData)
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
  accountDataLocal.value = structuredClone(brandData)
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
        title="Add Brand"
        prepend-icon="mdi-tag-heart"
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
                  label="Brand Name"
                />
              </VCol>

              <!-- 👉 Color Code -->
              <VCol
                cols="12"
                md="6"
              >
                <VSwitch
                  v-model="accountDataLocal.status"
                  :true-value="1"
                  :false-value="0"
                  prepend-icon="mdi-list-status"
                  label="Status"
                  color="primary"
                  :value="status"
                  hide-details
                />
              </VCol>

              <!-- 👉 Form Actions -->
              <VCol
                cols="12"
                class="d-flex flex-wrap gap-4"
              >
                <VBtn @click="submit">
                  Add Brand
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