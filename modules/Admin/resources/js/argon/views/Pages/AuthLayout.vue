<template>
  <div class="main-content bg-default">
    <base-nav
      v-model="showMenu"
      :transparent="true"
      class="navbar-horizontal navbar-main navbar-top navbar-dark"
      expand="lg"
      menu-classes="justify-content-end"
    >
      <div slot="brand" class="navbar-wrapper">
        <b-navbar-brand>
          <img src="/admin/dist/img/logo.svg">
        </b-navbar-brand>
      </div>
    </base-nav>

    <div class="main-content">
      <zoom-center-transition
        :duration="pageTransitionDuration"
        mode="out-in"
      >
        <slot></slot>
      </zoom-center-transition>
    </div>

    <footer id="footer-main" class="py-5">
      <b-container>
        <b-row align-v="center" class="justify-content-xl-between">
          <b-col xl="6">
            <div class="copyright text-center text-xl-left text-muted">
              © {{ year }} <a class="font-weight-bold ml-1" href="https://raxkor.com" target="_blank">Raxkor</a>
            </div>
          </b-col>
        </b-row>
      </b-container>
    </footer>
  </div>
</template>

<script>
import {BaseNav} from '@/argon/components'
import {ZoomCenterTransition} from 'vue2-transitions'

export default {
  components: {
    BaseNav,
    ZoomCenterTransition
  },
  props: {
    backgroundColor: {
      type: String,
      default: 'black'
    }
  },
  data() {
    return {
      showMenu: true,
      menuTransitionDuration: 250,
      pageTransitionDuration: 200,
      year: new Date().getFullYear(),
      pageClass: 'login-page'
    }
  },
  computed: {
    title() {
      return `${this.$route.name} Page`
    }
  },
  methods: {
    toggleNavbar() {
      document.body.classList.toggle('nav-open')
      this.showMenu = !this.showMenu
    },
    closeMenu() {
      document.body.classList.remove('nav-open')
      this.showMenu = false
    },
    setBackgroundColor() {
      document.body.classList.add('bg-default')
    },
    removeBackgroundColor() {
      document.body.classList.remove('bg-default')
    },
    updateBackground() {
      this.setBackgroundColor()
    }
  },
  beforeDestroy() {
    this.removeBackgroundColor()
  },
  beforeRouteUpdate(to, from, next) {
    // Close the mobile menu first then transition to next page
    if (this.showMenu) {
      this.closeMenu()
      setTimeout(() => {
        next()
      }, this.menuTransitionDuration)
    } else {
      next()
    }
  },
  watch: {
    $route: {
      immediate: true,
      handler: function () {
        this.updateBackground()
      }
    }
  }
}
</script>

<style lang="scss">
$scaleSize: 0.8;
@keyframes zoomIn8 {
  from {
    opacity: 0;
    transform: scale3d($scaleSize, $scaleSize, $scaleSize);
  }
  100% {
    opacity: 1;
  }
}

.main-content .zoomIn {
  animation-name: zoomIn8;
}

@keyframes zoomOut8 {
  from {
    opacity: 1;
  }
  to {
    opacity: 0;
    transform: scale3d($scaleSize, $scaleSize, $scaleSize);
  }
}

.main-content .zoomOut {
  animation-name: zoomOut8;
}
</style>
