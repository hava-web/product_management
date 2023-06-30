import { createStore } from "vuex"
import axiosIns from "@/plugins/axios"

const store = createStore({
  state(){
    return{
      users: null,
      user: null,
      categories: null,
      brands: null,
      colors: null,
      sizes: null,
      warehousesByPage: null,
      employeesByPage: null,
      categoryBypage: null,
      colorByPage: null,
      brandByPage: null,
      sizeByPage: null,
      productByPage: null,
      warehouses: null,
      isAuthenticated: localStorage.getItem('isAuthenticated') || false,
    }
  },
  getters: {
    getAllUser: state => state.users,
    getUser: state => state.user,
    getCategories: state => state.categories,
    getBrands: state => state.brands,
    getColors: state => state.colors,
    getSizes: state => state.sizes,
    getAllWarehouse: state => state.warehouses,
    getWarehousebyPage: state=> state.warehousesByPage,
    getemployeesByPage: state => state.employeesByPage,
    getCategoryByPage: state => state.categoryBypage,
    getColorByPage: state => state.colorByPage,
    getBrandByPage: state => state.brandByPage,
    getSizeByPage: state => state.sizeByPage,
    getProductByPage: state => state.productByPage,
    isAuthenticated: state => state.isAuthenticated,
  },
  mutations: {
    getUser(state, user){
      state.user = user
    },
    getAllUser(state, users){
      state.users = users
    },
    getCategories(state, categories){
      state.categories = categories
    },
    getBrands(state, brands){
      state.brands = brands
    },
    getColors(state, colors){
      state.colors = colors
    },
    getSizes(state, sizes){
      state.sizes = sizes
    },
    getWarehouseByPage(state, warehousesByPage){
      state.warehousesByPage = warehousesByPage
    },
    getEmployeesByPage(state, employeesByPage){
      state.employeesByPage = employeesByPage
    },
    getCategoryByPage(state, categoryBypage){
      state.categoryBypage = categoryBypage
    },
    getColorByPage(state, colorByPage){
      state.colorByPage = colorByPage
    },
    getBrandByPage(state, brandByPage){
      state.brandByPage = brandByPage
    },
    getSizeByPage(state, sizeByPage){
      state.sizeByPage = sizeByPage
    },
    getProductByPage(state, productByPage){
      state.productByPage = productByPage
    },
    getAllWarehouse(state, warehouses){
      state.warehouses = warehouses
    },
    logout(state){
      state.user = null
      state.isAuthenticated = false
    },
    
    //Not used
    login(state){
      state.isAuthenticated = localStorage.getItem('isAuthenticated') || false
    },
  },
  actions: {
    async getUser({ commit }, user){
      const accessToken = localStorage.getItem('accessToken')

      await  axiosIns.get('/api/auth/user', {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      })
        .then(res=>{
          console.log(res)
          localStorage.setItem('user', JSON.stringify(res.data))
          user = JSON.parse(localStorage.getItem('user'))
          commit('getUser', user)
        })
        .catch(err=>{
          console.log(err)
        })
    },

    async getAllUser({ commit }, users){
      const accessToken = localStorage.getItem('accessToken')

      await axiosIns.get('/api/auth/all_users', {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      })
        .then(res =>{
          users = res.data
          console.log(users)
          commit('getAllUser', users)
        })
        .catch(err =>{
          console.log(err)
        })
    },

    async getCategories({ commit }){
      const accessToken = localStorage.getItem('accessToken')

      await axiosIns.get('/api/all_categories', {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      })
        .then(res =>{
          console.log(res.data)
          commit('getCategories', res.data)
        })
        .catch(err =>{
          console.log(err)
        })
    },

    async getBrands({ commit }){
      const accessToken = localStorage.getItem('accessToken')

      await axiosIns.get('/api/all_brands', {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      })
        .then(res =>{
          console.log(res.data)
          commit('getBrands', res.data)
        })
        .catch(err =>{
          console.log(err)
        })
    },

    async getColors({ commit }){
      const accessToken = localStorage.getItem('accessToken')

      await axiosIns.get('/api/all_colors', {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      })
        .then(res =>{
          console.log(res.data)
          commit('getColors', res.data)
        })
        .catch(err =>{
          console.log(err)
        })
    },

    async getSizes({ commit }){
      const accessToken = localStorage.getItem('accessToken')

      await axiosIns.get('/api/all_sizes', {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      })
        .then(res =>{
          console.log(res.data)
          commit('getSizes', res.data)
        })
        .catch(err =>{
          console.log(err)
        })
    },

    async getWarehouseByPage({ commit }, page){
      const accessToken = localStorage.getItem('accessToken')

      await axiosIns.get('/api/view_warehouse?page=' + page, {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      })
        .then(res =>{
          console.log(res)
          commit('getWarehouseByPage', res.data)
        })
        .catch(err => {
          console.log(err)
        })
    },

    async getEmployeesByPage({ commit }, page){
      const accessToken = localStorage.getItem('accessToken')

      await axiosIns.get('/api/employees?page=' + page, {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      })
        .then(res =>{
          console.log(res)
          commit('getEmployeesByPage', res.data)
        })
        .catch(err => {
          console.log(err)
        })
    },

    async getCategoryByPage({ commit }, page){
      const accessToken = localStorage.getItem('accessToken')

      await axiosIns.get('/api/view_categories?page=' + page, {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      })
        .then(res =>{
          console.log(res)
          commit('getCategoryByPage', res.data)
        })
        .catch(err => {
          console.log(err)
        })
    },

    async getColorByPage({ commit }, page){
      const accessToken = localStorage.getItem('accessToken')

      await axiosIns.get('/api/view_colors?page=' + page, {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      })
        .then(res =>{
          console.log(res)
          commit('getColorByPage', res.data)
        })
        .catch(err => {
          console.log(err)
        })
    },

    async getBrandByPage({ commit }, page){
      const accessToken = localStorage.getItem('accessToken')

      await axiosIns.get('/api/view_brands?page=' + page, {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      })
        .then(res =>{
          console.log(res)
          commit('getBrandByPage', res.data)
        })
        .catch(err => {
          console.log(err)
        })
    },

    async getSizeByPage({ commit }, page){
      const accessToken = localStorage.getItem('accessToken')

      await axiosIns.get('/api/view_sizes?page=' + page, {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      })
        .then(res =>{
          console.log(res)
          commit('getSizeByPage', res.data)
        })
        .catch(err => {
          console.log(err)
        })
    },

    async getProductByPage({ commit }, page){
      const accessToken = localStorage.getItem('accessToken')

      await axiosIns.get('/api/view_products?page=' + page, {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      })
        .then(res =>{
          console.log(res)
          commit('getProductByPage', res.data)
        })
        .catch(err => {
          console.log(err)
        })
    },

    async logout({ commit }){
      const accessToken = localStorage.getItem('accessToken')

      await axiosIns.get('/api/auth/logout', {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      })
        .then(res=>{
          console.log(res)
          localStorage.removeItem('accessToken')
          localStorage.removeItem('user')
          localStorage.removeItem('users')
          localStorage.removeItem('isAuthenticated')
          localStorage.removeItem('warehouses')
          commit('logout')
        })
        .catch(err=>{
          console.log(err)
        })
    },

    async getAllWarehouse({ commit }){
      const accessToken = localStorage.getItem('accessToken')

      await axiosIns.get('/api/all_warehouse', {
        headers: {
          'Authorization': `Bearer ${accessToken}`,
        },
      }).then(res=>{
        console.log(res)
        commit('getAllWarehouse', res.data)
      }).catch(err=>{
        console.log(err)
      })
    },
  },
})

export default store