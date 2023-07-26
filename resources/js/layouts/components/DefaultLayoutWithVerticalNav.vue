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
        <!--
          <div
          class="d-flex align-center cursor-pointer"
          style="user-select: none;"
          > 
        -->
        <!-- 👉 Search Trigger button -->
        <!--
          <IconBtn>
          <VIcon icon="mdi-magnify" />
          </IconBtn>

          <span class="d-none d-md-flex align-center text-disabled">
          <span class="me-3">Search</span>
          <span class="meta-key">&#8984;K</span>
          </span>
          </div> 
        -->

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

        <!--
          <IconBtn class="me-2">
          <VIcon icon="mdi-bell-outline" />
          </IconBtn> 
        -->

        <NavbarThemeSwitcher class="me-2" />

        <UserProfile />
      </div>
    </template>

    <template #vertical-nav-content>
      <VerticalNavLink
        :item="{
          title: 'Trang chủ',
          icon: 'mdi-home-outline',
          to: '/dashboard',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Cài đặt tài khoản',
          icon: 'mdi-account-cog-outline',
          to: '/account-settings',
        }"
      />

      <!-- 👉 Users -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Người dùng',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Thêm Tài khoản',
          icon: 'mdi-account-plus',
          to: '/create_account',
        }"
      />

      <VerticalNavLink
        :item="{
          title: 'Nhân Viên',
          icon: 'mdi-account-card',
          to: '/view_employee',
        }"
      />

      <VerticalNavSectionTitle
        :item="{
          heading: 'Đơn Hàng',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Tạo Đơn Hàng',
          icon: 'mdi-package-up',
          to: '/add_order',
        }"
      />

      <VerticalNavLink
        :item="{
          title: 'Đơn Hàng',
          icon: 'mdi-view-grid-compact',
          to: '/view_order',
        }"
      />

      <!-- 👉 Warehouse -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Chi Nhánh',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Thêm Chi Nhánh',
          icon: 'mdi-face-agent',
          to: '/add_agent',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Chi Nhánh',
          icon: 'mdi-garage-variant',
          to: '/view_agent',
        }"
      />

      <!-- 👉 Warehouse -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Kho Hàng',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Thêm Kho',
          icon: 'mdi-store-plus-outline',
          to: '/add_warehouse',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Kho Hàng',
          icon: 'mdi-store-search-outline',
          to: '/view_warehouse',
        }"
      />

      <!-- 👉 Category -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Danh Mục',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Thêm Danh Mục',
          icon: 'mdi-card-plus',
          to: '/add_category',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Danh Mục',
          icon: 'mdi-card-text',
          to: '/view_category',
        }"
      />

      <!-- 👉 Product -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Sản Phẩm',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Thêm Sản Phẩm',
          icon: 'mdi-package-variant-closed-plus',
          to: '/add_product',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Sản Phẩm',
          icon: 'mdi-package-variant-closed',
          to: '/view_product',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Hàng Tồn Kho',
          icon: 'mdi-package-down',
          to: '/inventories',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Toàn bộ sản phẩm',
          icon: 'mdi-package-up',
          to: '/all_products',
        }"
      />

      <!-- 👉 Color -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Màu sắc',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Thêm Màu',
          icon: 'mdi-brush-outline',
          to: '/add_color',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Màu Sắc',
          icon: 'mdi-palette-outline',
          to: '/view_color',
        }"
      />

      <!-- 👉 Brand -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Thương Hiệu',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Thêm Thương Hiệu',
          icon: 'mdi-tag-plus-outline',
          to: '/add_brand',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Thương Hiệu',
          icon: 'mdi-tag-text',
          to: '/view_brands',
        }"
      />

      <!-- 👉 Size -->
      <VerticalNavSectionTitle
        :item="{
          heading: 'Kích Thước',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Thêm kích thước ',
          icon: 'mdi-weight',
          to: '/add_size',
        }"
      />
      <VerticalNavLink
        :item="{
          title: 'Kích Thước',
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
