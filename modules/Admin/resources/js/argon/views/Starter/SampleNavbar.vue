<template>
  <base-nav
    class="navbar navbar-top navbar-expand navbar-light navbar-expand-lg navbar-dark"
    container-classes="container-fluid"
    type=""
  >
    <!-- Search form -->
    <b-form id="navbar-search-main" class="navbar-search form-inline mr-sm-3 navbar-search-dark">
      <b-form-group class="mb-0">
        <b-input-group class="input-group-alternative input-group-merge">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
          </div>
          <b-form-input placeholder="Search" type="text"></b-form-input>
        </b-input-group>
      </b-form-group>
      <button aria-label="Close" class="close" data-action="search-close" data-target="#navbar-search-main"
              type="button">
        <span aria-hidden="true">×</span>
      </button>
    </b-form>
    <!-- Navbar links -->
    <b-navbar-nav class="align-items-center ml-md-auto">
      <!-- This item dont have <b-nav-item> becouse they add class 'nav-link' which is not needed here -->
      <li class="nav-item d-xl-none">
        <!-- Sidenav toggler -->
        <div class="pr-3 sidenav-toggler sidenav-toggler-dark"
             @click="toggleSidebar">
          <div class="sidenav-toggler-inner">
            <i class="sidenav-toggler-line"></i>
            <i class="sidenav-toggler-line"></i>
            <i class="sidenav-toggler-line"></i>
          </div>
        </div>
      </li>
      <!-- This item dont have <b-nav-item> because item have data-action/data-target on tag <a>, wich we cant add -->
      <li class="nav-item d-sm-none">
        <a class="nav-link" data-action="search-show" data-target="#navbar-search-main" href="#">
          <i class="ni ni-zoom-split-in"></i>
        </a>
      </li>
    </b-navbar-nav>
    <b-navbar-nav class="align-items-center ml-auto ml-md-0">
      <b-nav-item-dropdown right>
        <b-dropdown-item class="pr-0" href="#">
          <b-media class="align-items-center" no-body>
                  <span class="avatar avatar-sm">
                    <b-img alt="Image placeholder" rounded="circle"
                           src="https://cdn3.iconfinder.com/data/icons/avatars-round-flat/33/avat-01-512.png"/>
                  </span>
            <b-media-body class="ml-2 d-none d-lg-block">
              <span class="mb-0 text-sm  font-weight-bold">John Snow</span>
            </b-media-body>
          </b-media>
        </b-dropdown-item>
        <b-dropdown>
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
          <b-dropdown-item href="#!">
            <i class="ni ni-calendar-grid-58"></i>
            <span>Activity</span>
          </b-dropdown-item>
          <b-dropdown-item href="#!">
            <i class="ni ni-support-16"></i>
            <span>Support</span>
          </b-dropdown-item>
          <div class="dropdown-divider"></div>
          <b-dropdown-item href="#!">
            <i class="ni ni-user-run"></i>
            <span>Logout</span>
          </b-dropdown-item>
        </b-dropdown>
      </b-nav-item-dropdown>
    </b-navbar-nav>
  </base-nav>
</template>
<script>
import {CollapseTransition} from 'vue2-transitions'
import {BaseNav, Modal} from '@/argon/components'

export default {
  components: {
    CollapseTransition,
    BaseNav,
    Modal
  },
  computed: {
    routeName() {
      /*const { name } = this.$route;
      return this.capitalizeFirstLetter(name);*/
      return ''
    },
    isRTL() {
      return this.$rtl.isRTL
    }
  },
  data() {
    return {
      activeNotifications: false,
      showMenu: false,
      searchModalVisible: false,
      searchQuery: ''
    }
  },
  methods: {
    capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1)
    },
    toggleNotificationDropDown() {
      this.activeNotifications = !this.activeNotifications
    },
    closeDropDown() {
      this.activeNotifications = false
    },
    toggleSidebar() {
      this.$sidebar.displaySidebar(!this.$sidebar.showSidebar)
    },
    hideSidebar() {
      this.$sidebar.displaySidebar(false)
    }
  }
}
</script>
<style scoped>
.top-navbar {
  top: 0px;
}
</style>
