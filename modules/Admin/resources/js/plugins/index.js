import Vue from 'vue'

import './axios'

import VueMeta from 'vue-meta'
import VueMoment from 'vue-moment'
import VueSimpleAlert from 'vue-simple-alert'

Vue.use(VueMeta, {
  refreshOnceOnNavigation: true
})
Vue.use(VueMoment)
Vue.use(VueSimpleAlert)

Vue.mixin({
  methods: {
    route: (name, params, absolute) => route(`admin.${name}`, params, absolute)
  }
})
