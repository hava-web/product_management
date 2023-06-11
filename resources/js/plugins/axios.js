import axios from 'axios'

const axiosIns = axios.create({
  // You can add your headers here
  // ================================
  baseURL: 'http://127.0.0.1:8000/',
  headers: { 'Accept': 'application/json' },
})

export default axiosIns