<script setup>
import avatar1 from '@images/avatars/avatar-1.png'

const warehouseData = {
  warehousename: 'Warehouse',
  manager: 'Doe',
  city: 'johnDoe@example.com',
  status: 'ThemeSelection',
  address: '123 Main St, New York, NY 10001',
}

const refInputEl = ref()
const accountDataLocal = ref(structuredClone(warehouseData))
const isAccountDeactivated = ref(false)

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
                  v-model="accountDataLocal.firstName"
                  label="Warehouse Name"
                />
              </VCol>

              <!-- 👉 Manager -->
              <VCol
                md="6"
                cols="12"
              >
                <VTextField
                  v-model="accountDataLocal.lastName"
                  label="Manager"
                />
              </VCol>

              <!-- 👉 City -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="accountDataLocal.email"
                  label="City"
                />
              </VCol>

              <!-- 👉 Status -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  v-model="accountDataLocal.org"
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
                <VBtn>Save changes</VBtn>

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