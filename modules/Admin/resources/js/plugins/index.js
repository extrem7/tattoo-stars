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
    /* eslint-disable no-undef */
    route: (name, params, absolute) => route(`admin.${name}`, params, absolute),
    isRoute: (name) => route(null, {}, null, Ziggy).current(`admin.${name}`),
    routeIncludes: (fragments) => route(null, {}, null, Ziggy)
      .current()
      .match(new RegExp(`(${fragments.join('|')})`)),
    /* eslint-enable no-undef */
    can(permission) {
      return !!this.$page.props.auth.permissions.includes(permission)
    },

    logout() {
      this.$inertia.delete(this.route('logout'))
    },
  },
})
