import Vue from 'vue'

import {InertiaApp, plugin} from '@inertiajs/inertia-vue'

Vue.use(plugin)

import './plugins'

import DashboardPlugin from './argon/plugins/dashboard-plugin'

Vue.use(DashboardPlugin)

import {InertiaProgress} from '@inertiajs/progress'

InertiaProgress.init()

const el = document.getElementById('app')

new Vue({
  render: h => h(InertiaApp, {
    props: {
      initialPage: JSON.parse(el.dataset.page),
      resolveComponent: name => import(`./pages/${name}`).then(module => module.default),
    },
  }),
  mounted() {
    const {set} = this.$meta().addApp('TattooStars')
    const messageTypes = {
      destroy: {
        style: 'success',
        icon: 'fas fa-trash'
      }
    }
    this.$inertia.on('success', (event) => {
      const props = event.detail.page.props

      set({title: `${props.metaInfo.title} - TattooStars`})

      if (props.flash.message !== undefined) {
        const type = messageTypes[props.flash.type]
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
