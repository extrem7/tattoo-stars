import Vue from 'vue'

import VueCompositionAPI from '@vue/composition-api'

Vue.use(VueCompositionAPI)

import {InertiaApp, plugin} from '@inertiajs/inertia-vue'

Vue.use(plugin)

import './plugins'

import DashboardPlugin from './argon/plugins/dashboard-plugin'

Vue.use(DashboardPlugin)

import {InertiaProgress} from '@inertiajs/progress'

InertiaProgress.init()

const el = document.getElementById('app')

import DashboardLayout from '@/argon/views/Layout/DashboardLayout'

new Vue({
  render: h => h(InertiaApp, {
    props: {
      initialPage: JSON.parse(el.dataset.page),
      resolveComponent: name => import(`./pages/${name}`)
        .then(({default: page}) => {
          page.layout = page.layout === undefined ? DashboardLayout : page.layout
          return page
        })
    },
  }),
  mounted() {
    const {set} = this.$meta().addApp('TattooStars')
    const messageTypes = {
      created: {
        icon: 'fas fa-plus-circle'
      },
      destroy: {
        icon: 'fas fa-trash'
      }
    }

    this.$inertia.on('success', (event) => {

      const props = event.detail.page.props

      set({title: `${props.metaInfo.title} - TattooStars`})

      if (props.flash.message !== undefined) {
        const type = messageTypes[props.flash.type] || {
          type: 'success',
          icon: null
        }
        this.$notify({
          message: props.flash.message,
          icon: type.icon,
          type: type.style,
        })
      }
      if (props.flash.error !== undefined) {
        this.$notify({
          message: props.flash.error,
          icon: '',
          type: 'warning',
        })
      }
    })
  }
}).$mount(el)
