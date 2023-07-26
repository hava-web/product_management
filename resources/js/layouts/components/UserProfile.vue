<script setup>
import avatar1 from '@images/avatars/avatar-1.png'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import axios from 'axios'

const store = useStore()
const user = ref()
const employee_id = ref()
const router = useRouter()

const getUser = computed(()=> store.state.user)

onMounted( async () => {
  console.log(store)
  await store.dispatch('getUser')
  user.value = getUser.value
  console.log(user.value)

  const accessToken = localStorage.getItem('accessToken')

  await axios.get('api/user_employee/' + user.value.id, {
    headers: {
      'Authorization': `Bearer ${accessToken}`,
    },
  }).then(res=>{
    console.log(res.data)
    employee_id.value = res.data.id
  }).catch(err=>{
    console.log(err.data)
  })

  // watchEffect(() => {
  //   const getUser = store.state.user

  //   user.value = getUser
    
  //   console.log(user.value)
  // })
})

const setting = ()=>{
  router.push('/account-settings')
}

const profile = () =>{
  router.push({ name: 'employee', params: { id: employee_id.value } })
}

const logout = async ()=>{
  await store.dispatch('logout')
  router.push('/login')
}
</script>

<template>
  <VBadge
    dot
    location="bottom right"
    offset-x="3"
    offset-y="3"
    color="success"
    bordered
  >
    <VAvatar
      class="cursor-pointer"
      color="primary"
      variant="tonal"
    >
      <VImg :src="avatar1" />

      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="230"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar
                    color="primary"
                    variant="tonal"
                  >
                    <VImg :src="avatar1" />
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ user.name }}
            </VListItemTitle>
            <VListItemSubtitle>{{ user.role }}</VListItemSubtitle>
          </VListItem>
          <VDivider class="my-2" />

          <!-- 👉 Profile -->
          <VListItem link>
            <template #prepend>
              <VIcon
                class="me-2"
                icon="mdi-account-outline"
                size="22"
              />
            </template>

            <VListItemTitle @click="profile">
              Profile
            </VListItemTitle>
          </VListItem>

          <!-- 👉 Settings -->
          <VListItem link>
            <template #prepend>
              <VIcon
                class="me-2"
                icon="mdi-cog-outline"
                size="22"
              />
            </template>

            <VListItemTitle @click="setting">
              Settings
            </VListItemTitle>
          </VListItem>

          <!-- 👉 Pricing -->
          <!--
            <VListItem link>
            <template #prepend>
            <VIcon
            class="me-2"
            icon="mdi-currency-usd"
            size="22"
            />
            </template>

            <VListItemTitle>Pricing</VListItemTitle>
            </VListItem> 
          -->

          <!-- 👉 FAQ -->
          <!--
            <VListItem link>
            <template #prepend>
            <VIcon
            class="me-2"
            icon="mdi-help-circle-outline"
            size="22"
            />
            </template>

            <VListItemTitle>FAQ</VListItemTitle>
            </VListItem> 
          -->

          <!-- Divider -->
          <VDivider class="my-2" />

          <!-- 👉 Logout -->
          <VListItem>
            <template #prepend>
              <VIcon
                class="me-2"
                icon="mdi-logout"
                size="22"
                @click="logout"
              />
            </template>

            <VListItemTitle>Logout</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>
