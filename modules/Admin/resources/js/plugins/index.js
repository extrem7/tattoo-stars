import Vue from 'vue'

import './axios'

import VueMeta from 'vue-meta'

Vue.use(VueMeta, {
  refreshOnceOnNavigation: true
})

Vue.mixin({
  methods: {
    route: (name, params, absolute) => route(`admin.${name}`, params, absolute)
  }
})
