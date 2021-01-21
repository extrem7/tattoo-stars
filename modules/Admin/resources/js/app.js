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
    this.$inertia.on('success', (event) => {
      set({title: `${event.detail.page.props.metaInfo.title} - TattooStars`})
    })
  }
}).$mount(el)
