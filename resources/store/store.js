import { createStore } from "vuex"
import axiosIns from "@/plugins/axios"

const store = createStore({
  state(){
    return{
      users: null,
      user: null,
      warehousesByPage: null,
      employeesByPage: null,
      warehouses: null,
      isAuthenticated: localStorage.getItem('isAuthenticated') || false,
    }
  },
  getters: {
    getAllUser: state => state.users,
    getUser: state => state.user,
    getAllWarehouse: state => state.warehouses,
    getWarehousebyPage: state=> state.warehousesByPage,
    getemployeesByPage: state => state.employeesByPage,
    isAuthenticated: state => state.isAuthenticated,
  },
  mutations: {
    getUser(state, user){
      state.user = user
    },
    getAllUser(state, users){
      state.users = users
    },
    getWarehouseByPage(state, warehousesByPage){
      state.warehousesByPage = warehousesByPage
    },
    getEmployeesByPage(state, employeesByPage){
      state.employeesByPage = employeesByPage
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