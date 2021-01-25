<template>
  <div class="user-create-or-edit">
    <BaseHeader class="pb-6 pb-8 pt-5 pt-md-8 bg-gradient-success"/>
    <div class="container-fluid mt--9">
      <b-row align-h="center">
        <b-col lg="4">
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
                      {{ !isEdit ? 'Создать нового' : 'Редактировать' }} пользователя
                      <span v-if="isEdit">{{ user.name }}</span>
                    </h3>
                  </BCardHeader>
                  <BCardBody>
                    <AvatarUploader
                      v-if="isEdit"
                      :id="user.id"
                      :avatar="user.avatar"
                    />

                    <BaseInput
                      v-model="form.name"
                      :rules="{required: true,max:255}"
                      alternative
                      class="mb-3"
                      label="Имя"
                      name="name"
                      placeholder="Имя"
                    />
                    <BaseInput
                      v-model="form.email"
                      :rules="{required: true, email: true}"
                      alternative
                      class="mb-3"
                      label="Email"
                      name="email"
                      placeholder="Email"
                      prepend-icon="ni ni-email-83"
                      type="email"
                    />
                    <BaseInput
                      v-model="form.password"
                      :rules="{required: !isEdit, min: 8}"
                      alternative
                      class="mb-3"
                      label="Пароль"
                      name="password"
                      placeholder="Пароль"
                      prepend-icon="ni ni-lock-circle-open"
                    />
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
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
import DashboardLayout from '@/argon/views/Layout/DashboardLayout'
import AvatarUploader from '@/components/AvatarUploader'

export default {
  props: {
    errors: Object,
    user: Object
  },
  layout: (h, page) => h(DashboardLayout, [page]),
  components: {
    AvatarUploader
  },
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        role: null,
      },
      //roles: [...this.shared('roles')],
      avatar: null,
    }
  },
  computed: {
    isEdit() {
      return !!this.user
    }
  },
  created() {
    this.$inertia.on('error', e => {
      this.$refs.formValidator.setErrors(this.errors)
    })

    if (this.isEdit) {
      this.form.name = this.user.name
      this.form.email = this.user.email
      this.form.password = ''
    }
  },
  methods: {
    submit() {
      this.$inertia.visit(
        !this.isEdit ? this.route('users.index') : this.route('users.update', this.user.id),
        {
          method: !this.isEdit ? 'post' : 'patch',
          data: this.form,
          preserveState: !this.isEdit
        })
    },
    back() {
      history.back()
    }
  }
}
</script>
