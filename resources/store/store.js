import { createStore } from "vuex"
import axiosIns from "@/plugins/axios"

const store = createStore({
  state(){
    return{
      users: [],
      user: null,
      autheticated: false,
    }
  },
  getters: {
    getAllUser(state, users){
      state.users = users
    },
    getUser(state, user){
      state.user = user
    },
  },
  mutations: {
    
  },
  actions: {
    
  },
})

export default store