// Polyfills for js features used in the Dashboard but not supported in some browsers (mainly IE)
import '@/polyfills'
// Notifications plugin. Used on Notifications page
import Notifications from '@/argon/components/NotificationPlugin'
// Validation plugin used to validate forms
import {localize, configure, extend} from 'vee-validate'
// A plugin file where you could register global components used across the app
import GlobalComponents from './globalComponents'
// A plugin file where you could register global directives
import GlobalDirectives from './globalDirectives'
// Sidebar on the right. Used as a local plugin in DashboardLayout.vue
import SideBar from '@/argon/components/SidebarPlugin'

// element ui language configuration
import lang from 'element-ui/lib/locale/lang/en'
import locale from 'element-ui/lib/locale'

locale.use(lang)

// vue-bootstrap
import {BootstrapVue, IconsPlugin} from 'bootstrap-vue'

// asset imports
import * as rules from 'vee-validate/dist/rules'
import ru from 'vee-validate/dist/locale/ru.json'

localize('ru', ru)

Object.keys(rules).forEach(rule => {
  extend(rule, {
    ...rules[rule], // copies rule configuration
    message: ru[rule] // assign message
  })
})

export default {
  install(Vue) {
    Vue.use(GlobalComponents)
    Vue.use(GlobalDirectives)
    Vue.use(SideBar)
    Vue.use(Notifications)
    Vue.use(BootstrapVue)
    Vue.use(IconsPlugin)
    configure({
      classes: {
        valid: 'is-valid',
        invalid: 'is-invalid',
        dirty: ['is-dirty', 'is-dirty'], // multiple classes per flag!
      }
    })
  }
}
