<template>
  <div :class="[`${resource}-index`]">
    <BaseHeader class="pb-6 pb-8 pt-5 pt-md-8 bg-gradient-success"/>
    <BContainer class="mt--9" fluid>
      <BRow>
        <BCol>
          <Card
            class="shadow bg-default"
            header-classes="bg-transparent"
            no-body>
            <template slot="header">
              <slot name="header"></slot>
            </template>
            <BCol
              class="d-flex align-items-center justify-content-center justify-content-between flex-column flex-sm-row mb-3"
              cols="12">
              <CreateBtn v-if="can(`${resource}.create`)"/>
              <slot name="search"></slot>
            </BCol>
            <div class="table-responsive" scroll-region>
              <slot></slot>
            </div>
          </Card>
          <BPagination
            v-if="usePagination && total"
            :per-page="perPage"
            :total-rows="total"
            :value="currentPage"
            class="mt-4"
            @change="$emit('change:current-page',$event)"
          />
        </BCol>
      </BRow>
    </BContainer>
  </div>
</template>

<script>
import CreateBtn from '@/components/includes/tables/CreateBtn'

export default {
  components: {
    CreateBtn
  },
  props: {
    resource: String,
    usePagination: {
      type: Boolean,
      default: false
    },
    total: Number,
    currentPage: Number,
    perPage: Number,
  }
}
</script>
