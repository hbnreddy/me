export default {
  cart: () => {
    const cart = localStorage.getItem('cart')

    if (!cart) {
      return false
    }

    return JSON.parse(cart)
  },

  token: () => {
    return localStorage.getItem('token')
  }
}
