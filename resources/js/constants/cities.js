import { reactive } from "vue"

export const city = [
  'Ho Chi Minh City',
  'Hanoi',
  'Da Nang',
  'Can Tho',
  'Hai Phong',
  'Nha Trang',
  'Hue',
  'Ba Ria',
  'Vung Tau',
  'Qui Nhon',
  'Rach Gia',
  'Sa Dec',
  'Vinh',
  'Ha Tinh',
  'Thai Nguyen',
  'Lang Son',
  'Dien Bien Phu',
  'Da Lat',
  'Pleiku',
  'Phan Thiet',
  'Ha Long',
  'Tam Ky',
]

export const alert = reactive({
  status: false,
  title: null,
  text: null,
  color: null,
})

export const payment = [
  'Online Payment',
  'Cash on Delivery',
]

export const orderStatus = [
  'Ordered',
  'Pending',
  'Delivering',
  'Received',
  'Canceled',
]