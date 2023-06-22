/* eslint-disable import/order */
import '@/@iconify/icons-bundle'
import App from '@/App.vue'
import vuetify from '@/plugins/vuetify'
import { loadFonts } from '@/plugins/webfontloader'
import router from '@/router'
// eslint-disable-next-line import/no-unresolved
import '@core-scss/template/index.scss'
import '@layouts/styles/index.scss'
import VueDatePicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import '@styles/styles.scss'
import store from '../store/store'
import { createPinia } from 'pinia'
import { createApp } from 'vue'

loadFonts()


// Create vue app
const app = createApp(App)

app.component('VueDatePicker', VueDatePicker)

// Use plugins
app.use(vuetify)
app.use(createPinia())
app.use(router)
app.use(store)

// Mount vue app
app.mount('#app')
