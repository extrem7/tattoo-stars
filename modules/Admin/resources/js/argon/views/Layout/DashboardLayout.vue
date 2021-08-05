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
        <SideBarItem
          v-if="can('stories.index')"
          :link="{
            name: 'Stories',
            path: route('stories.index'),
            icon: 'ni ni-image text-primary',
            active:routeIncludes(['stories'])
          }"
        />
        <SideBarItem
          v-if="can('promotions.index')"
          :link="{
            name: 'Рекламные посты',
            path: route('promotions.index'),
            icon: 'ni ni-notification-70 text-primary',
            active:routeIncludes(['promotions'])
          }"
        />
        <SideBarItem
          v-if="can('banners.index')"
          :link="{
            name: 'Банеры',
            path: route('banners.index'),
            icon: 'ni ni-notification-70 text-primary',
            active:routeIncludes(['banners'])
          }"
        />
        <SideBarItem
          v-if="can('pages.index')"
          :link="{
            name: 'Страницы приложения',
            path: route('pages.index'),
            icon: 'ni ni-books text-primary',
            active:routeIncludes(['pages'])
          }"
        />
        <SideBarItem
          v-if="can('faqs.index')"
          :link="{
            name: 'FAQ',
            path: route('faqs.index'),
            icon: 'fa fa-question text-primary',
            active:routeIncludes(['faqs'])
          }"
        />
        <SideBarItem
          v-if="can('admin-panel.telescope')"
          :link="{
            name: 'Telescope',
            path: '/telescope',
            icon: 'ni ni-spaceship text-primary'
          }"
        />
        <BNavItem
          class="nav-item"
          @click.prevent="logout"
        >
          <i class="ni ni-user-run text-primary"></i>
          <span class="nav-link-text">Выйти</span>
        </BNavItem>
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
import DashboardNavbar from './DashboardNavbar.vue'
import ContentFooter from './ContentFooter.vue'
//import DashboardContent from './Content.vue'
import {FadeTransition} from 'vue2-transitions'

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

export default {
  components: {
    DashboardNavbar,
    ContentFooter,
    //DashboardContent,
    FadeTransition
  },
  mounted() {
    this.initScrollbar()
  },
  methods: {
    initScrollbar() {
      let isWindows = navigator.platform.startsWith('Win')
      if (isWindows) {
        initScrollbar('sidenav')
      }
    }
  }
}
</script>
