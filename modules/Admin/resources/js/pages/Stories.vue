<template>
  <IndexLayout
    :current-page="currentPage"
    :per-page="perPage"
    :resource="resource"
    :total="total"
    @change:current-page="currentPage = $event">
    <template slot="header">
      <div class="d-flex justify-content-between align-items-center">
        <h3 class="mb-0 text-white">Stories</h3>
      </div>
    </template>
    <BTable
      v-show="total"
      ref="table"

      :busy="isBusy"
      :current-page="currentPage"
      :fields="fields"

      :items="()=>initial"
      :per-page="perPage"
      :sort-by.sync="sortBy"

      :sort-desc.sync="sortDesc"
      dark
      hover

      sort-icon-left
      @context-changed="update">
      <template #cell(user)="{item:{user}}">
        ID:{{ `${user.id} ${user.nickname}` }}
      </template>
      <template #cell(rating)="{item:{rating,likes,dislikes}}">
        {{ rating }} ({{ likes }}👍|{{ dislikes }}👎)
      </template>
      <template #cell(total)="{item:{total,totalToday}}">
        {{ totalToday }}|{{ total }}
      </template>
      <template #cell(created_at)="{item:{createdAt}}">
        {{ createdAt | moment('DD.MM.YYYY HH:mm') }}
      </template>
      <template #cell(included)="{item:{id,included}}">
        <div class="d-flex justify-content-center">
          <BFormCheckbox
            :checked="included"
            @change="includeStory(id)"
          />
        </div>
      </template>
      <template #cell(excluded)="{item:{id,excluded}}">
        <div class="d-flex justify-content-center">
          <BFormCheckbox
            :checked="excluded"
            @change="excludeStory(id)"
          />
        </div>
      </template>
      <template #cell(actions)="{item:{id,images}}">
        <ActionsButtons
          :id="id"
          :resource="resource"
          @delete="destroy(id)"
        >
          <a
            href="#"
            class="btn btn-primary"
            @click.prevent="show(images)"
          >Просмотреть</a>
        </ActionsButtons>
      </template>
    </BTable>
    <CoolLightBox
      :items="gallery.items"
      :index="gallery.index"
      @close="gallery.index = null">
    </CoolLightBox>
  </IndexLayout>
</template>

<script>
import index from '@/mixins/crud/index'
import {BFormCheckbox} from 'bootstrap-vue'
import CoolLightBox from 'vue-cool-lightbox'

export default {
  components: {BFormCheckbox, CoolLightBox},
  mixins: [index],
  data() {
    return {
      fields: [
        {key: 'id', sortable: true},
        {key: 'user', label: 'Пользователь'},
        {key: 'postId', sortable: true, sortKey: 'post_id'},
        {key: 'rating', label: 'Рейтинг📈', sortable: true},
        {key: 'views', label: 'Просмотров👀', sortable: true, sortKey: 'views_count'},
        {key: 'total', label: 'Добавлений👀', sortable: true},
        {key: 'included', label: 'Добавить завтра'},
        {key: 'excluded', label: 'Заблокировать завтра'},
        {key: 'created_at', label: 'Добавлено📅', thClass: 'date-column', sortable: true},
      ],
      actionsClass: 'actions-column w-auto',
      gallery: {
        items: [],
        index: null
      },
    }
  },
  methods: {
    includeStory(id) {
      this.$inertia.post(this.route(`${this.resource}.include`, id), null, {
        only: ['data', 'flash'],
        preserveState: false,
        preserveScroll: true,
        replace: true
      })
    },
    excludeStory(id) {
      this.$inertia.post(this.route(`${this.resource}.exclude`, id), null, {
        only: ['data', 'flash'],
        preserveState: false,
        preserveScroll: true,
        replace: true
      })
    },
    show(images) {
      this.gallery.items = images
      this.gallery.index = 0
    }
  }
}
</script>
