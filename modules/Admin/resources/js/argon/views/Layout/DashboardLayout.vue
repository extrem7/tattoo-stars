<template>
  <div class="wrapper">
    <Notifications/>
    <SideBar>
      <template slot="links">
        <SideBarItem
            :link="{
            name: 'Главная',
            path: route('dashboard'),
            icon: 'ni ni-tv-2 text-primary',
            active:isRoute('dashboard')
          }"
        />
        <SideBarItem
            v-if="can('users.index')"
            :link="{
            name: 'Пользователи',
            path: route('users.index'),
            icon: 'ni ni-single-02 text-primary',
            active:routeIncludes(['users','profile'])
          }"
        />
      </template>
    </SideBar>
    <div class="main-content">
      <DashboardNavbar/>

      <div @click="$sidebar.displaySidebar(false)">
        <fade-transition :duration="200" mode="out-in" origin="center top">
          <slot></slot>
        </fade-transition>
      </div>
      <ContentFooter/>
    </div>
  </div>
</template>

<script>
import PerfectScrollbar from 'perfect-scrollbar'
import 'perfect-scrollbar/css/perfect-scrollbar.css'

function hasElement(className) {
  return document.getElementsByClassName(className).length > 0
}

function initScrollbar(className) {
  if (hasElement(className)) {
    new PerfectScrollbar(`.${className}`)
  } else {
    // try to init it later in case this component is loaded async
    setTimeout(() => {
      initScrollbar(className)
    }, 100)
  }
}

import DashboardNavbar from './DashboardNavbar.vue'
import ContentFooter from './ContentFooter.vue'
import DashboardContent from './Content.vue'
import {FadeTransition} from 'vue2-transitions'

export default {
  components: {
    DashboardNavbar,
    ContentFooter,
    DashboardContent,
    FadeTransition
  },
  methods: {
    initScrollbar() {
      let isWindows = navigator.platform.startsWith('Win')
      if (isWindows) {
        initScrollbar('sidenav')
      }
    }
  },
  mounted() {
    this.initScrollbar()
  }
}
</script>

<style lang="scss">
</style>
