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
    <b-container class="mt--7" fluid>
      <b-row>
        <b-col>
          <light-table/>
        </b-col>
      </b-row>
      <div class="mt-5"></div>
      <dark-table></dark-table>
    </b-container>
  </div>
</template>
<script>
import {Dropdown, DropdownItem, DropdownMenu, Table, TableColumn} from 'element-ui'
import projects from './Tables/projects'
import users from './Tables/users'
import LightTable from './Tables/RegularTables/LightTable'
import DarkTable from './Tables/RegularTables/DarkTable'

export default {
  components: {
    LightTable,
    DarkTable,
    [Dropdown.name]: Dropdown,
    [DropdownItem.name]: DropdownItem,
    [DropdownMenu.name]: DropdownMenu,
    [Table.name]: Table,
    [TableColumn.name]: TableColumn
  },
  data() {
    return {
      projects,
      users
    }
  }
}
</script>
<style>
.el-table.table-dark {
  background-color: #172b4d;
  color: #f8f9fe;
}

.el-table.table-dark th,
.el-table.table-dark tr {
  background-color: #172b4d;
}

.el-table.table-dark td,
.el-table.table-dark th.is-leaf {
  border-bottom: none;
}
</style>
