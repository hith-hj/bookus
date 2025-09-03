<template>
  <b-card>
    <b-tabs
      v-if="true"
      pills
    >
      <span class="d-none d-sm-inline">Permission Team</span>
      <div>
        <!-- Media -->
        <b-media class="mb-2">
          <template #aside />
        </b-media>
        <error-msg
          v-show="iserror"
          :msg="msg"
        />
        <success-msg
          v-show="issuccess"
          :msg="msg"
        />

        <validation-observer ref="simpleRules">
          <!-- User Info: Input Fields -->
          <b-form>
              <b-row >
                  <b-col cols="3">
                      Permission
                  </b-col>
                  <b-col cols="2">
                      Basic
                  </b-col>
                  <b-col cols="2">
                     Low
                  </b-col>
                  <b-col cols="2">
                      Medium
                  </b-col>
                  <b-col cols="2">
                     High
                  </b-col>
              </b-row>
              <br>
              <div  v-for="(permission) in permissions" :key="permission" class="m-2">
            <b-row >
              <b-col cols="3">
                  {{permission}}

              </b-col>
                <b-col cols="2" >
                    <b-form-checkbox v-model="checkedBookinOnline" name="check-button" switch >
                    </b-form-checkbox>
                </b-col>
                <b-col cols="2">
                    <b-form-checkbox v-model="checkedBookinOnline" name="check-button" switch >
                    </b-form-checkbox>
                </b-col>
                <b-col cols="2">
                    <b-form-checkbox v-model="checkedBookinOnline" name="check-button" switch >
                    </b-form-checkbox>
                </b-col>
                <b-col cols="2">
                    <b-form-checkbox v-model="checkedBookinOnline" name="check-button" switch >
                    </b-form-checkbox>
                </b-col>
            </b-row>
                  <hr>
              </div>
            <br>
            <!-- Action Buttons -->
            <b-button
              variant="primary"
              class="mb-1 mb-sm-0 mr-0 mr-sm-1"
              type="submit"
              @click.prevent="testsend"
            >
              Save changes
            </b-button>
            <b-button
              variant="outline-secondary"
              type="reset"
            >
              Reset
            </b-button>
          </b-form>
        </validation-observer>
      </div>
      <!--            </b-tab>-->
    </b-tabs>
  </b-card>
</template>

<script>
import {
  BCard,
  BCardText,
  BAlert,
  BRow,
  BCol,
  BLink,
  BTab,
  BTabs,
  BButton,
  BMedia,
  BAvatar,
  BFormGroup,
  BFormInput,
  BFormFile,
  BForm,
  BTable,
  BCardHeader,
  BCardTitle,
  BFormTextarea,
  BFormCheckbox,
  BInputGroupPrepend,
} from 'bootstrap-vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required } from '@validations'

import axios from 'axios'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import ErrorMsg from '@/components/Aleart/Error'
import SuccessMsg from '@/components/Aleart/Success'
import vSelect from 'vue-select'

export default {
  components: {
    BCard,
    BCardText,
    BAlert,BFormCheckbox,
    BRow,
    BCol,
    BLink,
    BTab,
    BTabs,
    BButton,
    BMedia,
    BAvatar,
    BFormGroup,
    BFormInput,
    BFormFile,
    BForm,
    BTable,
    BCardHeader,
    BCardTitle,
    BInputGroupPrepend,
    BFormTextarea,

    ValidationProvider,
    ValidationObserver,
    vSelect,

    ErrorMsg,
    SuccessMsg,
  },
  setup() {
    const statusOptions = ['active', 'inactive']

    return { statusOptions }
  },
  data() {
    return {
      required,
      permissions: {},

      status: 'active',

      msg: '',
      iserror: false,
      issuccess: false,
    }
  },
  watch: {

  },
  mounted() {
    this.getTeamPermission()
  },
  methods: {
    validationForm() {
      this.$refs.simpleRules.validate().then(success => {
        if (success) {
          const userToken = localStorage.getItem('userToken')
          const token = JSON.parse(userToken)

          const config = {
            headers: { Authorization: `Bearer ${token}`, 'content-type': 'multipart/form-data' },
          }
          const link = 'api/admin/categories/3'
          const formdata = new FormData()
          formdata.append('name', this.category.name)

          if (this.status == 'active') formdata.append('status', 1)
          else formdata.append('status', 0)

          formdata.append('_method', 'PUT')
          axios.post(link, formdata, config)
            .then(res => {
              this.$router.replace('/categoriesList').then(() => {
                this.$toast({
                  component: ToastificationContent,
                  timeout: 5000,
                  props: {
                    title: 'add category successfuly',
                    icon: 'EditIcon',
                    variant: 'success',
                  },
                })
              })
              this.msg = 'Add category Successufly'
              this.iserror = false
              this.issuccess = true
            }).catch(error => {
              if (error.response.status == 422) {
                this.msg = error.response.data.message
                this.iserror = true
                this.issuccess = false
              }
            })
        }
      })
    },
    testsend() {
      this.$refs.simpleRules.validate().then(success => {
        if (success) {
          const userToken = localStorage.getItem('userToken')
          const token = JSON.parse(userToken)
          const config = {
            headers: { Authorization: `Bearer ${token}`, 'content-type': 'multipart/form-data' },
          }
          // let link="api/admin/categories/3" ;
          const formdata = new FormData()
          formdata.append('name', this.category.name)

          if (this.status == 'active') formdata.append('status', 1)
          else formdata.append('status', 0)

          formdata.append('_method', 'put')
          axios.post(`/api/admin/categories/${this.$route.params.id}`, formdata, config)
            .then(res => {
              this.$router.replace('/categoriesList').then(() => {
                this.$toast({
                  component: ToastificationContent,
                  timeout: 5000,
                  props: {
                    title: 'edit category successfuly',
                    icon: 'EditIcon',
                    variant: 'success',
                  },
                })
              })
              this.msg = 'edit category Successufly'
              this.iserror = false
              this.issuccess = true
            }).catch(error => {
              if (error.response.status == 422) {
                this.msg = error.response.data.message
                this.iserror = true
                this.issuccess = false
              }
            })
        }
      })
    },
    getTeamPermission() {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)

      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }
      axios
        .get('/api/admin/team_permission', config)
        .then(res => {
          this.permissions = res.data.content
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
    width: 190px;

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
