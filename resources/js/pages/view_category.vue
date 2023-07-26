<script setup>
import { useStore } from 'vuex'
import { useRouter, useRoute } from 'vue-router'
import { reactive } from 'vue'
import { roles } from '@/constants/roles'
import axiosIns from '@/plugins/axios'


const store = useStore()
const route = useRoute()
const page = ref()
const length = ref()
const cancel = ref(false)
const show = ref(true)
const dialog = ref(false)
const categoryList = ref([])
const haveImg = ref(false)
const imagePreview = ref(null)


const alert = reactive({
  status: false,
  title: '',
  text: '',
  color: '',
})

const error = reactive({
  status: false,
  title: '',
  text: '',
  color: '',
})

const searchQuery = ref('')

const filteredItems = computed(() => {
  return categoryList.value.filter(item => {
    return item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) 
  })
})


const getCategoryByPage = computed(()=>{
  return store.getters.getCategoryByPage
})

const categoryInfo = reactive({
  id: null,
  name: '',
  image: null,
  status: null,
})

function onFileChange(event){
  haveImg.value = true
  categoryInfo.image = event.target.files[0]
  let reader = new FileReader()
  reader.addEventListener("load", function(){
    imagePreview.value = reader.result
  }.bind(), false)

  if(categoryInfo.image &&  /\.(jpe?g|png|gif)$/i.test( categoryInfo.image.name)){
    reader.readAsDataURL(categoryInfo.image)
  }

}

const updateForm = async id=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.get('/api/category/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    categoryInfo.id = res.data.id
    categoryInfo.image = res.data.image
    categoryInfo.name = res.data.name
    categoryInfo.status = res.data.status
    imagePreview.value = 'storage/images/' + categoryInfo.image
    haveImg.value = categoryInfo.image ? true : false
    console.log(res.data)
  }).catch(err=>{
    console.log(err)
  })
}

const update = async id=>{
  const accessToken = localStorage.getItem('accessToken')

  await axiosIns.post('/api/update/category/' + id, categoryInfo, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res)
    dialog.value = false
    alert.status = true
    alert.title = 'Cập nhật thành công'
    alert.text = 'Danh mục đã được cập nhật thành công'
    alert.color = 'rgba(39, 217, 11, 0.8)'
  }).catch(err=>{
    console.log(categoryInfo)
    console.log(err)
    error.status = true
    error.title = 'You have some errors'
    error.text = err.response.data.message
    error.color = 'rgba(222, 29, 29, 0.8)'
  })
  console.log(categoryInfo)
} 

const deleteForm = id=>{
  console.log(id)
}

const deleteCat = id=>{
  const accessToken = localStorage.getItem('accessToken')

  axiosIns.get('/api/delete/category/' + id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res)
    cancel.value = false
    alert.status = true
    alert.title = 'Xóa thành công '
    alert.text = 'Danh mục đã đước xóa thành công'
    alert.color = 'rgba(39, 217, 11, 0.8)'

    const index = categoryList.value.findIndex(cat => cat.id === id)

    categoryList.value.splice(index, 1)
  }).catch(err=>{
    console.log(err)
  })
}

// Call the action to retrieve the warehouse data and set the initial value of currentcategoryList to the result.

store.dispatch('getCategoryByPage', page.value).then(() => {
  categoryList.value.push(...getCategoryByPage.value.data)
  console.log(categoryList.value)
  length.value = Math.ceil(getCategoryByPage.value.total / getCategoryByPage.value.data.length)
})

// Call this function whenever the "page" value changes.
function updatecategoryList() {
  store.dispatch('getCategoryByPage', page.value).then(() => {
    categoryList.value = getCategoryByPage.value.data
  })
}

watch(page, updatecategoryList)


watchEffect(() => {
  const employeePath = /^\/view_employee\/employee\/\d+$/

  show.value = !employeePath.test(route.path)
})
</script>

<template>
  <RouterView />
  <Transition name="slide-fade">
    <VAlert 
      v-if="alert.status"
      :color="alert.color"
      icon="mdi-check-circle-outline"
      :title="alert.title"
      closable
      class="alert"
      max-width="400px"
      :text="alert.text"
      @click:close="alert.status = false"
    />
  </Transition>
  <VRow v-if="show">
    <VCol cols="12">
      <VCard 
        title="Tất cả danh mục"
        prepend-icon="mdi-store-plus-outline"
      >
        <template #append>
          <div class="me-n3 tool">
            <VCol
              cols="auto"
              class="d-flex"
            >
              <VTextField
                v-model="searchQuery"
                title="Search"
                class="mx-3"
                prepend-inner-icon="mdi-magnify"
                placeholder="Search"
              />
            </VCol>
          </div>
        </template> 
        <VDivider />
        <VTable>
          <thead>
            <tr>
              <th class="text-uppercase">
                ID
              </th>
              <th class="text-uppercase text-center">
                Tên danh mục 
              </th>
              <th class="text-uppercase text-center">
                Thời gian tạo
              </th>
              <th class="text-uppercase text-center">
                Ảnh
              </th>
              <th class="text-uppercase text-center">
                Cài đặt
              </th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="category in filteredItems"
              :key="category.categoryList"
            >
              <td>
                {{ category.id }}
              </td>
              <td class="text-center">
                {{ category.name }}
              </td>
              <td class="text-center">
                {{ category.created_at }}
              </td>
              <td class="text-center">
                <div class="image">
                  <VImg
                    :width="20"
                    class="item-image"
                    alt="Image"
                    content-class="text-center"
                    cover
                    :src="'storage/images/' + category.image"
                  />
                </div>
              </td>
              <td class="text-center">
                <VBtn
                  class="me-2"
                  icon="mdi-bell-outline"
                  color="none"
                >
                  <VIcon icon="mdi-dots-vertical" />
                  <VMenu
                    activator="parent"
                    location="right"
                  >
                    <VList>
                      <VListItem>
                        <VDialog
                          v-model="dialog"
                          persistent
                          width="800px"
                        >
                          <template #activator="{ props }">
                            <VBtn
                              color="none"
                              v-bind="props"
                              icon="mdi-pencil"
                              @click="updateForm(category.id)"
                            >
                              <VIcon icon="mdi-pencil" />
                              <VTooltip
                                activator="parent"
                                location="top"
                              >
                                Update
                              </VTooltip>
                            </VBtn>
                          </template>
                          <Transition name="slide-fade">
                            <VAlert 
                              v-if="error.status"
                              :color="error.color"
                              icon="mdi-alert"
                              :title="error.title"
                              closable
                              class="alert"
                              max-width="400px"
                              :text="error.text"
                              @click:close="error.status = false"
                            />
                          </Transition>
                          <VCard
                            prepend-icon="mdi-store-edit"
                            title=" Update Employee "
                          >
                            <VCardText>
                              <!-- 👉 Form -->
                              <VForm class="mt-6">
                                <VRow>
                                  <!-- 👉 Category Name -->
                                  <VCol cols="12">
                                    <VTextField
                                      v-model="categoryInfo.name"
                                      prepend-icon="mdi-rename"
                                      pre
                                      label="Category Name"
                                    />
                                  </VCol>

                                  <!-- 👉 Status -->
                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VFileInput
                                      :clearable="false"
                                      prepend-icon="mdi-camera"
                                      label="Select Image"
                                      accept="image/*"
                                      @change="onFileChange"
                                    />
                                    <VCard
                                      v-if="haveImg"
                                      :width="100"
                                      class="image"
                                    >
                                      <VImg
                                        v-model="categoryInfo.image"
                                        :width="100"
                                        :height="100"
                                        content-class="text-center"
                                        alt="Image"
                                        :src="imagePreview"
                                        cover
                                      />
                                    </VCard>
                                  </VCol>

                                  <VCol
                                    cols="12"
                                    md="6"
                                  >
                                    <VSwitch
                                      v-model="categoryInfo.status"
                                      :true-value="1"
                                      :false-value="0"
                                      prepend-icon="mdi-list-status"
                                      label="Status"
                                      color="primary"
                                      :value="status"
                                      hide-details
                                    />
                                  </VCol>
                                </VRow>
                              </VForm>
                            </VCardText>
                            <VCardActions>
                              <VSpacer />
                              <VBtn
                                color="red"
                                variant="elevated"
                                class="btn-cancel"
                                prepend-icon="mdi-close"
                                @click="dialog = false"
                              >
                                Hủy bỏ 
                              </VBtn>
                              <VBtn
                                color="primary"
                                variant="elevated"
                                prepend-icon="mdi-pencil-outline"
                                @click="update(category.id)"
                              >
                                Cập nhật
                              </VBtn>
                            </VCardActions>
                          </VCard>
                        </VDialog>
                      </VListItem>
                      <VListItem>
                        <VDialog
                          v-model="cancel"
                          width="auto"
                        >
                          <template #activator="{ props }">
                            <VBtn 
                              icon="mdi-delete-empty"
                              v-bind="props"
                              color="none"
                              @click="deleteForm(category.id)"
                            >
                              <VIcon icon="mdi-delete-empty" />
                              <VTooltip
                                activator="parent"
                                location="top"
                              >
                                Delete
                              </VTooltip>
                            </VBtn>
                          </template>
                          <VCard
                            prepend-icon="mdi-alert"
                            title="Xóa danh mục"
                          >
                            <VCardText>
                              Bạn có chắc chắn là bạn muốn xóa thông tin này không ?
                            </VCardText>
                            <VCardActions>
                              <VSpacer />
                              <VBtn
                                color="green-darken-1"
                                prepend-icon="mdi-close"
                                variant="elevated"
                                @click="cancel = false"
                              >
                                Hủy bỏ
                              </VBtn>
                              <VBtn
                                color="red"
                                prepend-icon="mdi-trash-can-outline"
                                variant="elevated"
                                @click="deleteCat(category.id)"
                              >
                                Xóa
                              </VBtn>
                            </VCardActions>
                          </VCard>
                        </VDialog>
                      </VListItem>
                    </VList>
                  </VMenu>
                </VBtn>
              </td>
            </tr>
          </tbody>
        </VTable>
      </VCard>
      <VPagination
        v-model="page"
        :length="length"
        rounded="circle"
      />
    </VCol>
  </VRow>
</template>

<style scoped>
.btn-cancel{
  background-color: #E53935;
  color: white;
}
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
  display: flex;
  margin-top: 20px;
  margin-left: 40px;
}
.item-image{
  margin: 10px;
}
.tool{
  width: 400px;
}
</style>