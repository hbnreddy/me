import NotificationView from './NotificationView'

const Notification = {
  install (Vue) {
    this.eventBus = new Vue()

    Vue.component('notification', NotificationView)

    Vue.prototype.$notification = {
      show (params) {
        Notification.eventBus.$emit('show', params)
      }
    }
  }
}

export default Notification
