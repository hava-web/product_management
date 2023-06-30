<script setup>
import { useTheme } from 'vuetify'
import upgradeBannerDark from '@images/pro/upgrade-banner-dark.png'
import upgradeBannerLight from '@images/pro/upgrade-banner-light.png'
import VerticalNavLayout from '@layouts/components/VerticalNavLayout.vue'
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue'
import VerticalNavSectionTitle from '@/@layouts/components/VerticalNavSectionTitle.vue'

// Components
import Footer from '@/layouts/components/Footer.vue'
import NavbarThemeSwitcher from '@/layouts/components/NavbarThemeSwitcher.vue'
import UserProfile from '@/layouts/components/UserProfile.vue'

const vuetifyTheme = useTheme()

const upgradeBanner = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? upgradeBannerLight : upgradeBannerDark
})
</script>

<template>
  <VerticalNavLayout>
    <!-- 👉 navbar -->
    <template #navbar="{ toggleVerticalOverlayNavActive }">
      <div class="d-flex h-100 align-center">
        <!-- 👉 Vertical nav toggle in overlay mode -->
        <IconBtn
          class="ms-n3 d-lg-none"
          @click="toggleVerticalOverlayNavActive(true)"
        >
          <VIcon icon="mdi-menu" />
        </IconBtn>

        <!-- 👉 Search -->
        <div
          class="d-flex align-center cursor-pointer"
          style="user-select: none;"
        >
          <!-- 👉 Search Trigger button -->
          <IconBtn>
            <VIcon icon="mdi-magnify" />
          </IconBtn>

          <span class="d-none d-md-flex align-center text-disabled">
            <span class="me-3">Search</span>
            <span class="meta-key">&#8984;K</span>
          </span>
        </div>

        <VSpacer />

        <!--
          <IconBtn
          class="me-2"
          href="https://github.com/themeselection/materio-vuetify-laravel-admin-template-free"
          target="_blank"
          rel="noopener noreferrer"
          >
          <VIcon icon="mdi-github" />
          </IconBtn> 
        -->

        <IconBtn class="me-2">
          <VIcon icon="mdi-bell-outline" />
        </IconBtn>

        <NavbarThemeSwitcher class="me-2" />

        <UserProfile />
      </div>
    </template>

    <template #vertical-nav-content>
      <VerticalNavLink
        :item="{
          title: 'Dashboard',
          icon: 'mdi-home-outline',
          to: '/dashboard',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Account Settings',
          icon: 'mdi-account-cog-outline',
          to: '/account-settings',
        }"
      />

      <!-- 👉 Users -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Users',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Create Account',
          icon: 'mdi-account-plus',
          to: '/create_account',
        }"
      />

      <VerticalNavLink
        :item="{
          title: 'View Employee',
          icon: 'mdi-account-card',
          to: '/view_employee',
        }"
      />

      <!-- 👉 Warehouse -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Warehouse',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Add Warehouse',
          icon: 'mdi-store-plus-outline',
          to: '/add_warehouse',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'View Warehouse',
          icon: 'mdi-store-search-outline',
          to: '/view_warehouse',
        }"
      />

      <!-- 👉 Category -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Category',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Add Category',
          icon: 'mdi-card-plus',
          to: '/add_category',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'View Category',
          icon: 'mdi-card-text',
          to: '/view_category',
        }"
      />

      <!-- 👉 Product -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Product',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Add Product',
          icon: 'mdi-package-variant-closed-plus',
          to: '/add_product',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'View Product',
          icon: 'mdi-package-variant-closed',
          to: '/view_product',
        }"
      />

      <!-- 👉 Color -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Color',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Add Color',
          icon: 'mdi-brush-outline',
          to: '/add_color',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'View Color',
          icon: 'mdi-palette-outline',
          to: '/view_color',
        }"
      />

      <!-- 👉 Brand -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Brand',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Add Brand',
          icon: 'mdi-tag-plus-outline',
          to: '/add_brand',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'View Brand',
          icon: 'mdi-tag-text',
          to: '/view_brands',
        }"
      />

      <!-- 👉 Size -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Size',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Add Size',
          icon: 'mdi-weight',
          to: '/add_size',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'View Size',
          icon: 'mdi-format-line-weight',
          to: '/view_sizes',
        }"
      />
      <!-- 👉 Pages -->
      <!--
        <VerticalNavSectionTitle
        :item="{
        heading: 'Page',
        }"
        />
        <VerticalNavLink
        :item="{
        title: 'Login',
        icon: 'mdi-login',
        to: '/login',
        }"
        />
        <VerticalNavLink
        :item="{
        title: 'Register',
        icon: 'mdi-account-plus-outline',
        to: '/register',
        }"
        />
        <VerticalNavLink
        :item="{
        title: 'Error',
        icon: 'mdi-information-outline',
        to: '/no-existence',
        }"
        /> 
      -->

      <!-- 👉 User Interface -->
      <!--
        <VerticalNavSectionTitle
        :item="{
        heading: 'User Interface',
        }"
        />
        <VerticalNavLink
        :item="{
        title: 'Typography',
        icon: 'mdi-alpha-t-box-outline',
        to: '/typography',
        }"
        />
        <VerticalNavLink
        :item="{
        title: 'Icons',
        icon: 'mdi-eye-outline',
        to: '/icons',
        }"
        />
        <VerticalNavLink
        :item="{
        title: 'Cards',
        icon: 'mdi-credit-card-outline',
        to: '/cards',
        }"
        />
        <VerticalNavLink
        :item="{
        title: 'Tables',
        icon: 'mdi-table',
        to: '/tables',
        }"
        />
        <VerticalNavLink
        :item="{
        title: 'Form Layouts',
        icon: 'mdi-form-select',
        to: '/form-layouts',
        }"
        /> 
      -->
    </template>

    <template #after-vertical-nav-items>
      <!-- 👉 illustration -->
      <a
        href="https://themeselection.com/item/materio-vuetify-laravel-admin-template"
        target="_blank"
        rel="noopener noreferrer"
        style="margin-left: 7px;"
      >
        <img
          :src="upgradeBanner"
          alt="upgrade-banner"
          transition="scale-transition"
          class="upgrade-banner mx-auto"
          style="max-width: 230px;"
        >
      </a>
    </template>

    <!-- 👉 Pages -->
    <slot />

    <!-- 👉 Footer -->
    <template #footer>
      <Footer />
    </template>
  </VerticalNavLayout>
</template>

<style lang="scss" scoped>
.meta-key {
  border: thin solid rgba(var(--v-border-color), var(--v-border-opacity));
  border-radius: 6px;
  block-size: 1.5625rem;
  line-height: 1.3125rem;
  padding-block: 0.125rem;
  padding-inline: 0.25rem;
}
</style>
