<template>
  <div class="page-create-or-edit">
    <BaseHeader class="pb-6 pb-8 pt-5 pt-md-8 bg-gradient-success"/>
    <div class="container-fluid mt--9">
      <BRow align-h="center">
        <BCol lg="10" md="12" xl="8">
          <div class="card-wrapper">
            <ValidationObserver ref="formValidator" v-slot="{handleSubmit}">
              <BForm
                ref="form"
                @submit.prevent="handleSubmit(submit)">
                <BCard
                  bg-variant="secondary"
                  no-body>
                  <BCardHeader header-bg-variant="transparent">
                    <h3 class="mb-0">
                      {{ !isEdit ? 'Создать новую' : 'Редактировать' }} страницу
                      <span v-if="isEdit"> "{{ page.title.ru }}"</span>
                    </h3>
                  </BCardHeader>
                  <BCardBody>
                    <BFormGroup label="Язык">
                      <BFormSelect
                        id="locale"
                        v-model="locale"
                        :options="locales"/>
                    </BFormGroup>
                    <BaseInput
                      v-model="form.title[locale]"
                      :label="`Заголовок на ${nativeLocale}`"
                      :rules="{required: true,max:255}"
                      alternative
                      class="mb-3"
                      name="title"
                      placeholder="Заголовок"
                    />
                    <BaseInput
                      :label="`Контент на ${nativeLocale}`"
                      :rules="{required: true}"
                      alternative
                      class="mb-3"
                      name="body">
                      <Editor
                        id="excerpt"
                        v-model="form.body[locale]"
                        :placeholder="`Контент на ${nativeLocale}`"
                      />
                      <Invalid
                        :errors="errors"
                        name="body"
                      />
                    </BaseInput>
                  </BCardBody>
                  <BCardFooter
                    class="d-flex justify-content-between"
                    footer-bg-variant="transparent">
                    <BBtn
                      variant="primary"
                      @click="back">
                      Назад
                    </BBtn>
                    <BBtn
                      type="submit"
                      variant="success">
                      {{ !isEdit ? 'Создать' : 'Сохранить' }}
                    </BBtn>
                  </BCardFooter>
                </BCard>
              </BForm>
            </ValidationObserver>
          </div>
        </BCol>
      </BRow>
    </div>
  </div>
</template>

<script>
import Editor from '@/components/includes/Editor'
import Invalid from '@/components/includes/Invalid'

export default {
  components: {
    Editor,
    Invalid
  },
  provide() {
    return {
      errors: this.errors
    }
  },
  props: {
    errors: Object,
    page: Object,
    localization: Object
  },
  data() {
    return {
      form: {
        title: {},
        body: {}
      },
      translatable: ['title', 'body'],
      locale: this.localization.default
    }
  },
  computed: {
    isEdit() {
      return !!this.page
    },
    nativeLocale() {
      return this.localization.locales[this.locale].on
    },
    locales() {
      return Object.entries(this.localization.locales).map(([value, {name: text}]) => ({text, value}))
    }
  },
  created() {
    this.$inertia.on('error', () => {
      this.$refs.formValidator.setErrors(this.errors)
    })

    if (this.isEdit) {
      this.form.title = this.page.title
      this.form.body = this.page.body
    }
  },
  methods: {
    submit() {
      this.$inertia.visit(
        !this.isEdit ? this.route('pages.store') : this.route('pages.update', this.page.id),
        {
          method: !this.isEdit ? 'post' : 'patch',
          data: this.form,
          preserveState: true,
          preserveScroll: true
        })
    },
    back() {
      history.back()
    }
  }
}
</script>
