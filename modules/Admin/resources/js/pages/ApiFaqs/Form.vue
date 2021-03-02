<template>
  <div class="faq-create-or-edit">
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
                      {{ !isEdit ? 'Создать новый' : 'Редактировать' }} вопрос
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
                      v-model="form.question[locale]"
                      :label="`Вопрос на ${nativeLocale}`"
                      :rules="{required: true,max:255}"
                      alternative
                      class="mb-3"
                      name="question"
                      placeholder="Вопрос">
                      <template slot="errors-backend">
                        <Invalid
                          :errors="errors"
                          name="answer"
                        />
                      </template>
                    </BaseInput>
                    <BaseInput
                      :label="`Ответ на ${nativeLocale}`"
                      :rules="{required: true}"
                      alternative
                      class="mb-3"
                      name="answer">
                      <Editor
                        id="excerpt"
                        v-model="form.answer[locale]"
                        :height="200"
                        :placeholder="`Ответ на ${nativeLocale}`"
                      />
                      <Invalid
                        :errors="errors"
                        name="answer"
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
    faq: Object,
    localization: Object
  },
  data() {
    return {
      form: {
        question: {},
        answer: {}
      },
      translatable: ['question', 'answer'],
      locale: this.localization.default
    }
  },
  computed: {
    isEdit() {
      return !!this.faq
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
      this.form.question = this.faq.question
      this.form.answer = this.faq.answer
    }
    for (let field of this.translatable) {
      this.locales.forEach(({value}) => {
        if (!this.form[field][value]) this.form[field][value] = ''
      })
    }
  },
  methods: {
    submit() {
      this.$inertia.visit(
        !this.isEdit ? this.route('faqs.store') : this.route('faqs.update', this.faq.id),
        {
          method: !this.isEdit ? 'post' : 'patch',
          data: this.form,
          preserveState: true,
          preserveScroll: true
        })
    }
    ,
    back() {
      history.back()
    }
  }
}
</script>
