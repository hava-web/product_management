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

const imagePreview = ref(null)

const warehouseData = {
  name: '',
  image: null,
  status: 0,
}


const store = useStore()
const haveImg = ref(false)
const managerList = ref([])
const accountDataLocal = ref(structuredClone(warehouseData))


//Handle Image Input
function onFileChange(event){
  haveImg.value = true
  accountDataLocal.value.image = event.target.files[0]
  let reader = new FileReader()
  reader.addEventListener("load", function(){
    imagePreview.value = reader.result
  }.bind(), false)

  if(accountDataLocal.value.image &&  /\.(jpe?g|png|gif)$/i.test( accountDataLocal.value.image.name)){
    reader.readAsDataURL(accountDataLocal.value.image)
  }

}


const getAllUser = computed(() => store.getters.getAllUser)

onMounted( async () => {
  await store.dispatch('getAllUser') 
  managerList.value.push(...getAllUser.value)
  console.log(managerList.value)
})

const submit = async ()=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.post('/api/add_category', accountDataLocal.value, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  })
    .then(res=>{
      if(res.status === 201){
        alert.title = 'Thành công'
        alert.status = true
        alert.text = 'Danh mục đã được thêm thành công '
        alert.color = 'rgba(39, 217, 11, 0.8)'
        accountDataLocal.value = structuredClone(warehouseData)
        imagePreview.value = null
        haveImg.value = false
      }
      else{
        alert.title = 'Cảnh báo'
        alert.status = true
        alert.text = 'Có lỗi gì đó'
        alert.color = 'rgba(234, 223, 30, 0.8)'
        console.log(res.data)
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
  accountDataLocal.value = structuredClone(warehouseData)
  imagePreview.value = null
  haveImg.value = false
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
        title="Thêm danh mục"
        prepend-icon="mdi-card-text-outline"
      >
        <VDivider />

        <VCardText>
          <!-- 👉 Form -->
          <VForm class="mt-6">
            <VRow>
              <!-- 👉 Category Name -->
              <VCol cols="12">
                <VTextField
                  v-model="accountDataLocal.name"
                  prepend-icon="mdi-rename"
                  pre
                  label="Tên danh mục"
                />
              </VCol>

              <!-- 👉 Status -->
              <VCol
                cols="12"
                md="6"
              >
                <VFileInput
                  v-model="accountDataLocal.image"
                  clearable
                  prepend-icon="mdi-camera"
                  label="Thêm ảnh"
                  accept="image/*"
                  @click:clear="haveImg = false"
                  @change="onFileChange"
                />
                <VCard
                  :width="100"
                  class="image"
                >
                  <VImg
                    v-if="haveImg"
                    :width="100"
                    :height="100"
                    content-class="text-center"
                    alt="Image"
                    cover
                    :src="imagePreview"
                  />
                </VCard>
              </VCol>

              <VCol
                cols="12"
                md="6"
              >
                <VSwitch
                  v-model="accountDataLocal.status"
                  :true-value="1"
                  :false-value="0"
                  prepend-icon="mdi-list-status"
                  label="Trạng thái"
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
                  Thêm danh mục
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