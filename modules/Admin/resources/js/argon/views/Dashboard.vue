<template>
  <div>

    <base-header class="pb-6 pb-8 pt-5 pt-md-8 bg-gradient-success">
      <!-- Card stats -->
      <b-row>
        <b-col md="6" xl="3">
          <stats-card class="mb-4"
                      icon="ni ni-active-40"
                      sub-title="350,897"
                      title="Total traffic"
                      type="gradient-red">

            <template slot="footer">
              <span class="text-success mr-2">3.48%</span>
              <span class="text-nowrap">Since last month</span>
            </template>
          </stats-card>
        </b-col>
        <b-col md="6" xl="3">
          <stats-card class="mb-4"
                      icon="ni ni-chart-pie-35"
                      sub-title="2,356"
                      title="Total traffic"
                      type="gradient-orange">

            <template slot="footer">
              <span class="text-success mr-2">12.18%</span>
              <span class="text-nowrap">Since last month</span>
            </template>
          </stats-card>
        </b-col>
        <b-col md="6" xl="3">
          <stats-card class="mb-4"
                      icon="ni ni-money-coins"
                      sub-title="924"
                      title="Sales"
                      type="gradient-green">

            <template slot="footer">
              <span class="text-danger mr-2">5.72%</span>
              <span class="text-nowrap">Since last month</span>
            </template>
          </stats-card>

        </b-col>
        <b-col md="6" xl="3">
          <stats-card class="mb-4"
                      icon="ni ni-chart-bar-32"
                      sub-title="49,65%"
                      title="Performance"
                      type="gradient-info">

            <template slot="footer">
              <span class="text-success mr-2">54.8%</span>
              <span class="text-nowrap">Since last month</span>
            </template>
          </stats-card>
        </b-col>
      </b-row>
    </base-header>

    <!--Charts-->
    <b-container class="mt--7" fluid>
      <b-row>
        <b-col class="mb-5 mb-xl-0" xl="8">
          <card header-classes="bg-transparent" type="default">
            <b-row slot="header" align-v="center">
              <b-col>
                <h6 class="text-light text-uppercase ls-1 mb-1">Overview</h6>
                <h5 class="h3 text-white mb-0">Sales value</h5>
              </b-col>
              <b-col>
                <b-nav class="nav-pills justify-content-end">
                  <b-nav-item
                    :active="bigLineChart.activeIndex === 0"
                    class="mr-2 mr-md-0"
                    link-classes="py-2 px-3"
                    @click.prevent="initBigChart(0)">
                    <span class="d-none d-md-block">Month</span>
                    <span class="d-md-none">M</span>
                  </b-nav-item>
                  <b-nav-item
                    :active="bigLineChart.activeIndex === 1"
                    link-classes="py-2 px-3"
                    @click.prevent="initBigChart(1)"
                  >
                    <span class="d-none d-md-block">Week</span>
                    <span class="d-md-none">W</span>
                  </b-nav-item>
                </b-nav>
              </b-col>
            </b-row>
            <line-chart
              ref="bigChart"
              :chart-data="bigLineChart.chartData"
              :extra-options="bigLineChart.extraOptions"
              :height="350"
            >
            </line-chart>
          </card>
        </b-col>

        <b-col class="mb-5 mb-xl-0" xl="4">
          <card header-classes="bg-transparent">
            <b-row slot="header" align-v="center">
              <b-col>
                <h6 class="text-uppercase text-muted ls-1 mb-1">Performance</h6>
                <h5 class="h3 mb-0">Total orders</h5>
              </b-col>
            </b-row>

            <bar-chart
              ref="barChart"
              :chart-data="redBarChart.chartData"
              :height="350"
            >
            </bar-chart>
          </card>
        </b-col>
      </b-row>
      <!-- End charts-->

      <!--Tables-->
      <b-row class="mt-5">
        <b-col class="mb-5 mb-xl-0" xl="8">
          <page-visits-table></page-visits-table>
        </b-col>
        <b-col class="mb-5 mb-xl-0" xl="4">
          <social-traffic-table></social-traffic-table>
        </b-col>
      </b-row>
      <!--End tables-->
    </b-container>

  </div>
</template>
<script>
// Charts
import * as chartConfigs from '@/argon/components/Charts/config'
import LineChart from '@/argon/components/Charts/LineChart'
import BarChart from '@/argon/components/Charts/BarChart'

// Components
import BaseProgress from '@/argon/components/BaseProgress'
import StatsCard from '@/argon/components/Cards/StatsCard'

// Tables
import SocialTrafficTable from './Dashboard/SocialTrafficTable'
import PageVisitsTable from './Dashboard/PageVisitsTable'

export default {
  components: {
    LineChart,
    BarChart,
    BaseProgress,
    StatsCard,
    PageVisitsTable,
    SocialTrafficTable
  },
  data() {
    return {
      bigLineChart: {
        allData: [
          [0, 20, 10, 30, 15, 40, 20, 60, 60],
          [0, 20, 5, 25, 10, 30, 15, 40, 40]
        ],
        activeIndex: 0,
        chartData: {
          datasets: [
            {
              label: 'Performance',
              data: [0, 20, 10, 30, 15, 40, 20, 60, 60],
            }
          ],
          labels: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        },
        extraOptions: chartConfigs.blueChartOptions,
      },
      redBarChart: {
        chartData: {
          labels: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
          datasets: [{
            label: 'Sales',
            data: [25, 20, 30, 22, 17, 29]
          }]
        },
        extraOptions: chartConfigs.blueChartOptions
      }
    }
  },
  methods: {
    initBigChart(index) {
      let chartData = {
        datasets: [
          {
            label: 'Performance',
            data: this.bigLineChart.allData[index]
          }
        ],
        labels: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      }
      this.bigLineChart.chartData = chartData
      this.bigLineChart.activeIndex = index
    }
  },
  mounted() {
    this.initBigChart(0)
  }
}
</script>
<style>
.el-table .cell {
  padding-left: 0px;
  padding-right: 0px;
}
</style>
