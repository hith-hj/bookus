<template>
  <div>
    <b-card>

      <!-- Media -->
      <b-media class="mb-0">
        <template #aside />
      </b-media>
      <!--        <error-msg v-show="iserror"  :msg="msg" ></error-msg>-->
      <!--        <success-msg v-show="issuccess" :msg="msg"></success-msg>-->
      <validation-observer ref="simpleRules">
        <!-- User Info: Input Fields -->
        <b-form>
          <b-row>
            <!-- Field: first -->
            <b-col cols="6">
              <b-form-group
                label="First Name"
                label-for="first_name"
              >
                <validation-provider
                  #default="{ errors }"
                  name="first_name"
                  rules="required"
                >
                  <b-form-input
                    v-model="version"
                    :state="errors.length > 0 ? false : null"
                    placeholder="last name"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group
                label="duration"
                label-for="duration"
              >
                <validation-provider
                  #default="{ errors }"
                  name="duration"
                  rules="required"
                >
                  <b-form-input
                    v-model="duration"
                    :state="errors.length > 0 ? false : null"
                    placeholder="duration"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group
                label="Arabic Name"
                label-for="Arabic Name"
              >
                <validation-provider
                  #default="{ errors }"
                  name="name_ar"
                  rules="required"
                >
                  <b-form-input
                    v-model="name_ar"
                    :state="errors.length > 0 ? false : null"
                    placeholder="Arabic Name"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group
                label="Phone"
                label-for="phone"
              >
                <validation-provider
                  #default="{ errors }"
                  name="phone"
                  rules="required"
                >
                  <b-form-input
                    v-model="phone"
                    :state="errors.length > 0 ? false : null"
                    placeholder="last name"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group
                label="whatsapp"
                label-for="whatsapp"
              >
                <validation-provider
                  #default="{ errors }"
                  name="whatsapp"
                  rules="required"
                >
                  <b-form-input
                    v-model="whatsapp"
                    :state="errors.length > 0 ? false : null"
                    placeholder="last name"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>
            </b-col>
            <b-col cols="6">
              <b-form-group
                label="Landline"
                label-for="landline"
              >
                <validation-provider
                  #default="{ errors }"
                  name="first_name"
                  rules="required"
                >
                  <b-form-input
                    v-model="landline"
                    :state="errors.length > 0 ? false : null"
                    placeholder="last name"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>
            </b-col>
          </b-row>

          <!-- Action Buttons -->
          <b-button
            variant="primary"
            class=""
            type="submit"
            @click.prevent="validationForm"
          >
            update
          </b-button>
          <b-button
            variant="outline-secondary"
            type="reset"
          >
            Reset
          </b-button>
        </b-form>
      </validation-observer>
    </b-card>

  </div>
</template>

<script>
import {
  BButton,
  BMedia,
  BAvatar,
  BRow,
  BCol,
  BFormGroup,
  BFormInput,
  BFormFile,
  BForm,
  BTable,
  BCard,
  BInputGroup,
  BCardHeader,
  BCardTitle,
  BFormTextarea,
  BFormCheckbox, BFormTimepicker,
  BInputGroupPrepend,
  BInputGroupAppend, BFormDatepicker,
} from 'bootstrap-vue'

import axios from 'axios'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, alphaNum, email } from '@validations'

import store from '@/store/index'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
// import ErrorMsg from '@/components/Aleart/Error'
// import SuccessMsg from '@/components/Aleart/Success'
import SingleImageVue from '@/components/SingleImag/SingleImage.vue'

import vSelect from 'vue-select'

export default {
  components: {
    BButton,
    BFormTimepicker,
    BMedia,
    BAvatar,
    BRow,
    BCol,
    BFormGroup,
    BFormInput,
    BFormFile,
    BForm,
    BTable,
    BCard,
    BCardHeader,
    BCardTitle,
    BFormCheckbox,
    BInputGroupPrepend,
    vSelect,
    BFormTextarea,
    BInputGroupAppend,
    BInputGroup,
    BFormDatepicker,
    ValidationProvider,
    ValidationObserver,
    SingleImageVue,

  },
  data() {
    return {
      // codeSimple,
      required,
      email,
      settings: {},
      version: '',
      duration: '',
      name_ar: '',
      phone: '',
      whatsapp: '',
      landline: '',
    }
  },
  computed: {

  },
  mounted() {
    this.getSetting()
  },

  methods: {
    validationForm() {
      this.$refs.simpleRules.validate()
        .then(success => {
          if (success) {
            // eslint-disable-next-line
                      const userToken = localStorage.getItem("userToken");
            const token = JSON.parse(userToken)

            const config = {
              headers: {
                'content-type': 'multipart/form-data',
                Authorization: `Bearer ${token}`,
              },
            }
            const formdata = new FormData()

            formdata.append('version', this.version)
            formdata.append('duration', this.duration)
            formdata.append('name_ar', this.name_ar)
            formdata.append('phone', this.phone)
            formdata.append('whatsapp', this.whatsapp)
            formdata.append('landline', this.landline)
            formdata.append('_method', 'put')
            axios
              .post('/api/admin/settings/1', formdata, config)

              .then(() => {
                this.$toast({
                  component: ToastificationContent,
                  timeout: 5000,
                  props: {
                    title: 'update successfuly',
                    icon: 'EditIcon',
                    variant: 'success',
                  },

                })
                this.msg = 'update Successufly'
                this.iserror = false
                this.issuccess = true
              })
              .catch(error => {
                if (error.response.status == 422) {
                  this.msg = error.response.data.message
                  this.iserror = true
                  this.issuccess = false
                }
              })
          }
        })
    },
    getSetting() {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)

      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }
      axios
        .get('/api/admin/settings', config)
        .then(res => {
          this.settings = res.data.content
          this.version = this.settings.version
          this.duration = this.settings.duration
          this.name_ar = this.settings.name_ar
          this.phone = this.settings.phone
          this.whatsapp = this.settings.whatsapp
          this.landline = this.settings.landline
        })
        .catch(err => {
          console.log(err)
        })
    },
  },

}
</script>

<style lang="scss" scoped>
.per-page-selector {
}

.Status-filter-select {
    ::v-deep .vs__selected-options {
        flex-wrap: nowrap;
    }

    ::v-deep .vs__selected {
        width: 100px;
    }
}
</style>
<style>
.costoum-image {
    padding-left: 24px;
    padding-bottom: 10px;
    padding-top: 38px;
    height: 75px;
}
</style>
<style lang="scss">
@import "~@resources/scss/vue/libs/vue-select.scss";
</style>
