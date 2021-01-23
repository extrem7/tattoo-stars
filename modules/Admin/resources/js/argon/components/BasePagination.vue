<template>
  <div>
    <b-pagination :align="align" :per-page="perPage" :size="size" :total-rows="total" :value="value" first-number
                  last-number @change="(val) => $emit('input', val)">
      <template v-slot:prev-text>
        <a aria-label="Previous" class="page-link">
          <span aria-hidden="true"><i aria-hidden="true" class="fa fa-angle-left"></i></span>
        </a>
      </template>
      <template v-slot:next-text>
        <a aria-label="Next" class="page-link">
          <span aria-hidden="true"><i aria-hidden="true" class="fa fa-angle-right"></i></span>
        </a>
      </template>
    </b-pagination>
  </div>
</template>
<script>
export default {
  name: 'BasePagination',
  props: {
    pageCount: {
      type: Number,
      default: 0,
      description:
        'Pagination page count. This should be specified in combination with perPage'
    },
    perPage: {
      type: Number,
      default: 10,
      description:
        'Pagination per page. Should be specified with total or pageCount'
    },
    total: {
      type: Number,
      default: 0,
      description:
        'Can be specified instead of pageCount. The page count in this case will be total/perPage'
    },
    value: {
      type: Number,
      default: 1,
      description: 'Pagination value'
    },
    size: {
      type: String,
      default: '',
      description: 'Pagination size'
    },
    align: {
      type: String,
      default: '',
      description: 'Pagination alignment (e.g center|start|end)'
    }
  },
}
</script>
