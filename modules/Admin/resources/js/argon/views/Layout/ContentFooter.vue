<template>
  <footer class="footer px-4">
    <b-row align-v="center" class="justify-content-lg-between">
      <b-col lg="6">
        <div class="copyright text-center text-lg-left text-muted">
          © {{ year }} <a class="font-weight-bold ml-1" href="https://raxkor.com" target="_blank">Raxkor</a>
        </div>
      </b-col>
      <div class="copyright text-right text-muted">
        <a class="font-weight-bold ml-1" href="/">TattooStars</a> v{{ $page.props.version }}
      </div>
    </b-row>
  </footer>
</template>

<script>
export default {
  data() {
    return {
      year: new Date().getFullYear()
    }
  }
}
</script>
