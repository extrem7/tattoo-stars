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
          <b-card class="border-0" no-body>
            <div id="map-custom" class="map-canvas"
                 style="height: 600px;"></div>
          </b-card>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>
<script>
import {API_KEY} from './Maps/API_KEY'
import GoogleMapsLoader from 'google-maps'

GoogleMapsLoader.KEY = API_KEY

export default {
  methods: {
    initMap(google) {
      let map, lat = 40.748817, lng = -73.985428, color = '#5e72e4'
      map = document.getElementById('map-custom')

      let myLatlng = new google.maps.LatLng(lat, lng)
      let mapOptions = {
        zoom: 12,
        scrollwheel: false,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        styles: [{
          'featureType': 'administrative',
          'elementType': 'labels.text.fill',
          'stylers': [{'color': '#444444'}]
        }, {
          'featureType': 'landscape',
          'elementType': 'all',
          'stylers': [{'color': '#f2f2f2'}]
        }, {
          'featureType': 'poi',
          'elementType': 'all',
          'stylers': [{'visibility': 'off'}]
        }, {
          'featureType': 'road',
          'elementType': 'all',
          'stylers': [{'saturation': -100}, {'lightness': 45}]
        }, {
          'featureType': 'road.highway',
          'elementType': 'all',
          'stylers': [{'visibility': 'simplified'}]
        }, {
          'featureType': 'road.arterial',
          'elementType': 'labels.icon',
          'stylers': [{'visibility': 'off'}]
        }, {
          'featureType': 'transit',
          'elementType': 'all',
          'stylers': [{'visibility': 'off'}]
        }, {'featureType': 'water', 'elementType': 'all', 'stylers': [{'color': color}, {'visibility': 'on'}]}]
      }

      map = new google.maps.Map(map, mapOptions)

      let marker = new google.maps.Marker({
        position: myLatlng,
        map: map,
        animation: google.maps.Animation.DROP,
        title: 'Hello World!'
      })

      let contentString = '<div class="info-window-content"><h2>Argon Dashboard PRO</h2>' +
        '<p>A beautiful premium dashboard for Bootstrap 4.</p></div>'

      let infowindow = new google.maps.InfoWindow({
        content: contentString
      })

      google.maps.event.addListener(marker, 'click', function () {
        infowindow.open(map, marker)
      })
    }
  },
  mounted() {
    GoogleMapsLoader.load(google => {
      this.initMap(google)
    })
  }
}
</script>
