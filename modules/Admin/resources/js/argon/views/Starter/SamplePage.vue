<template>
  <div>
    <base-header class="pb-6">
      <b-row align-v="center" class="py-4">
        <b-col cols="7" lg="6">
          <h6 class="h2 text-white d-inline-block mb-0">Default</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <route-bread-crumb></route-bread-crumb>
          </nav>
        </b-col>
        <b-col class="text-right" cols="5" lg="6">
          <base-button size="sm" type="neutral">New</base-button>
          <base-button size="sm" type="neutral">Filters</base-button>
        </b-col>
      </b-row>
      <!-- Card stats -->
      <b-row>
        <b-col md="6" xl="3">
          <stats-card icon="ni ni-active-40"
                      sub-title="350,897"
                      title="Total traffic"
                      type="gradient-red">

            <template slot="footer">
              <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
              <span class="text-nowrap">Since last month</span>
            </template>
          </stats-card>
        </b-col>
        <b-col md="6" xl="3">
          <stats-card icon="ni ni-chart-pie-35"
                      sub-title="2,356"
                      title="Total traffic"
                      type="gradient-orange">

            <template slot="footer">
              <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 12.18%</span>
              <span class="text-nowrap">Since last month</span>
            </template>
          </stats-card>
        </b-col>
        <b-col md="6" xl="3">
          <stats-card icon="ni ni-money-coins"
                      sub-title="924"
                      title="Sales"
                      type="gradient-green">

            <template slot="footer">
              <span class="text-danger mr-2"><i class="fa fa-arrow-down"></i> 5.72%</span>
              <span class="text-nowrap">Since last month</span>
            </template>
          </stats-card>

        </b-col>
        <b-col md="6" xl="3">
          <stats-card icon="ni ni-chart-bar-32"
                      sub-title="49,65%"
                      title="Performance"
                      type="gradient-info">

            <template slot="footer">
              <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 54.8%</span>
              <span class="text-nowrap">Since last month</span>
            </template>
          </stats-card>
        </b-col>
      </b-row>
    </base-header>
    <b-container class="mt--6" fluid>
      <b-row class="starter-page">
        Sample page
      </b-row>
    </b-container>
  </div>
</template>
<script>
import RouteBreadCrumb from '@/argon/components/Breadcrumb/RouteBreadcrumb'
import StatsCard from '@/argon/components/Cards/StatsCard'

export default {
  name: 'starter-page',
  components: {
    StatsCard,
    RouteBreadCrumb
  },
  data() {
    return {}
  }
}
</script>
<style>
.starter-page {
  min-height: calc(100vh - 380px);
}
</style>
