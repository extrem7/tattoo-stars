<template>
  <nav id="sidenav-main" class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white">
    <div class="container-fluid">

      <!--Toggler-->
      <navbar-toggle-button @click.native="showSidebar"></navbar-toggle-button>
      <inertia-link class="navbar-brand" href="/">
        <img :src="logo" alt="..." class="navbar-brand-img">
      </inertia-link>

      <slot name="mobile-right">
        <ul class="nav align-items-center d-md-none">
          <base-dropdown class="nav-item" menu-on-right tag="li" title-tag="a">
            <a slot="title-container" class="nav-link" href="#" role="button">
              <div class="media align-items-center">
                <span class="avatar avatar-sm rounded-circle">
                  <img alt="Image placeholder" src="/admin/dist/img/no-avatar.png">
                </span>
              </div>
            </a>

            <b-dropdown-header class="noti-title">
              <h6 class="text-overflow m-0">Welcome!</h6>
            </b-dropdown-header>
            <b-dropdown-item href="#!">
              <i class="ni ni-single-02"></i>
              <span>My profile</span>
            </b-dropdown-item>
            <b-dropdown-item href="#!">
              <i class="ni ni-settings-gear-65"></i>
              <span>Settings</span>
            </b-dropdown-item>
            <div class="dropdown-divider"></div>
            <b-dropdown-item href="/logout" @click.prevent="logout">
              <i class="ni ni-user-run"></i>
              <span>Logout</span>
            </b-dropdown-item>
          </base-dropdown>
        </ul>
      </slot>
      <slot></slot>
      <div v-show="$sidebar.showSidebar" id="sidenav-collapse-main" class="navbar-collapse collapse show">

        <div class="navbar-collapse-header d-md-none">
          <div class="row">
            <div class="col-6 collapse-brand">
              <inertia-link href="/">
                <img :src="logo">
              </inertia-link>
            </div>
            <div class="col-6 collapse-close">
              <navbar-toggle-button @click.native="closeSidebar"></navbar-toggle-button>
            </div>
          </div>
        </div>

        <ul class="navbar-nav">
          <slot name="links">
          </slot>
        </ul>
        <!--Divider-->
        <hr class="my-3">
        <!--Heading-->
        <!--<h6 class="navbar-heading text-muted">Documentation</h6>-->
        <!--Navigation-->
        <!--<ul class="navbar-nav mb-md-3">
          <li class="nav-item">
            <a class="nav-link"
               href="https://www.creative-tim.com/learning-lab/bootstrap-vue/alerts/argon-dashboard">
              <i class="ni ni-spaceship"></i> Getting started
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link"
               href="https://www.creative-tim.com/learning-lab/bootstrap-vue/colors/argon-dashboard">
              <i class="ni ni-palette"></i> Foundation
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link"
               href="https://www.creative-tim.com/learning-lab/bootstrap-vue/alerts/argon-dashboard">
              <i class="ni ni-ui-04"></i> Components
            </a>
          </li>
        </ul>-->
      </div>
    </div>
  </nav>
</template>

<script>
import NavbarToggleButton from '@/argon/components/NavbarToggleButton'

export default {
  name: 'sidebar',
  components: {
    NavbarToggleButton
  },
  props: {
    logo: {
      type: String,
      default: 'admin/dist/img/logo.svg',
      description: 'Sidebar app logo'
    },
    autoClose: {
      type: Boolean,
      default: true,
      description: 'Whether sidebar should autoclose on mobile when clicking an item'
    }
  },
  provide() {
    return {
      autoClose: this.autoClose
    }
  },
  methods: {
    closeSidebar() {
      this.$sidebar.displaySidebar(false)
    },
    showSidebar() {
      this.$sidebar.displaySidebar(true)
    }
  },
  beforeDestroy() {
    if (this.$sidebar.showSidebar) {
      this.$sidebar.showSidebar = false
    }
  }
}
</script>
