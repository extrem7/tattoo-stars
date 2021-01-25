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
            <a slot="title-container" class="nav-link" href="#" @click.prevent>
              <BMedia class="align-items-center" no-body>
                  <span class="avatar avatar-sm rounded-circle">
                    <img :src="avatar"
                         alt="Image placeholder"
                         class="avatar-image">
                  </span>
                <BMediaBody class="ml-2 d-none d-lg-block">
              <span class="mb-0 text-sm  font-weight-bold">
                {{ user.name }}
              </span>
                </BMediaBody>
              </BMedia>
            </a>

            <BDropdownHeader class="noti-title">
              <h6 class="text-overflow m-0">Приветствую!</h6>
            </BDropdownHeader>
            <li>
              <InertiaLink
                :href="route('profile.edit')"
                class="dropdown-item">
                <i class="ni ni-settings-gear-65"></i>
                <span>Настройки</span>
              </InertiaLink>
            </li>
            <div class="dropdown-divider"></div>
            <BDropdownItem
              href="#logout"
              @click.prevent="logout">
              <i class="ni ni-user-run"></i>
              <span>Выйти</span>
            </BDropdownItem>
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
      default: '/admin/dist/img/logo.svg',
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
  computed: {
    user() {
      return this.$page.props.auth
    },
    avatar() {
      return this.user.avatar || '/admin/dist/img/no-avatar.png'
    }
  },
  methods: {
    logout() {
      this.$inertia.delete(this.route('logout'))
    },
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
