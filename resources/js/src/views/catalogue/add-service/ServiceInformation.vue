<template>
  <div>

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

          <!-- Field: Service Name -->
          <b-col
            cols="6"
          >
            <b-form-group
              label="Service Name"
              label-for="servicw_name"
            >
              <validation-provider
                #default="{ errors }"
                name="service Name"
                rules="required"
              >
                <b-form-input
                  v-model="ServiceName"
                  :state="errors.length > 0 ? false:null"
                  placeholder="service name"
                />
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <!-- Field: treatment Name  : this be list dropdown-->
          <b-col
            cols="6"

            class=""
          >

            <label>select Treatment</label>

            <v-select
              v-model="TreatmentType"
              :options="TreatmentOption"
              :clearable="false"
              class="w-100 mx-100"
            />
          </b-col>

          <!-- Category list -->
          <b-col
            cols="6"

            class=""
          >

            <label>Select Category:</label>

            <v-select
              v-model="serviceCategory"
              label="name"
              :reduce="(name) => name.id"
              :options="serviceCategoryOption"
              :clearable="false"

              class="w-100"
            />
          </b-col>

          <!-- description  -->
          <b-col
            cols="12"
          >
            <b-form-group>
              <label>Description</label>
              (<b class="w-100 float-end">{{ "       "+descriptionLength }}/1000</b>)

              <validation-provider
                #default="{ errors }"
                name="Description"
                rules=""
              >

                <b-form-textarea
                  id="textarea"
                  v-model="description"
                  placeholder="Enter something..."
                  rows="3"
                  max-rows="6"
                  maxlength="1000"
                  @keyup="calclength"
                />

                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>

          <!-- Field: Aftercare description -->

          <b-col
            cols="12"
          >
            <b-form-group>
              <label for="Aftercare_description">Aftercare description</label>
              (<b class="w-100 float-end">{{ "       "+AfterCareDescriptionLength }}/1000</b>)

              <validation-provider
                #default="{ errors }"
                name="Aftercare_description"
                rules=""
              >
                <b-form-textarea
                  id="textarea"
                  v-model="AfterCareDescription"
                  placeholder="Enter something..."
                  rows="3"
                  maxlength="1000"
                  max-rows="6"
                  @keyup="calclength"
                />

                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>

          <!-- Service available for -->
          <b-col
            cols="6"

            class=""
          >

            <label>Service available for:</label>

            <v-select
              v-model="gender"
              :options="genderOption"
              :clearable="false"
              class="w-100"
            />
          </b-col>

          <b-col
            cols="12"
            class="m-2"
          >
            <b-form-checkbox
              v-model="checkedBookinOnline"
              name="check-button"
              switch
            >
              <b>Online Booking</b>
            </b-form-checkbox>

            <b-form-checkbox
              v-model="extraTime"
              name="check-button2"
              switch
            >
              <b>extra time</b>
            </b-form-checkbox>
          </b-col>

        </b-row>
        <div>
          <h4>Pricing and Duration</h4>
          <b-card
            title="Add the pricing options and duration of the service"
            header-tag="header"
            bg-variant="light"
            class="mx-3 my-3"
          >
            <template #header />
            <b-row>
              <!-- Field: Service Name -->
              <b-col
                cols="4"
              >
                <label>Duration </label>

                <v-select
                  v-model="Duration"
                  :options="DurationOption"
                  :clearable="false"
                  class="w-100"
                />
              </b-col>
              <b-col
                cols="4"
              >
                <label>Price type </label>

                <v-select
                  v-model="PriceType"
                  :options="PriceTypeOption"
                  :clearable="false"
                  class="w-100"
                />
              </b-col>
              <b-col
                v-if="PriceType != 'Free'"
                cols="4"
              >
                <label>Retail price<b v-if="PriceType == 'From'">(from)</b></label>

                <b-form-input
                  v-model="RetailPrice"
                  placeholder=""
                  type="number"
                />

              </b-col>
            </b-row>

          </b-card>

        </div>
        <!-- Action Buttons -->
        <b-button
          variant="primary"
          class=""
          type="submit"
          @click.prevent="validationForm"
        >
          Save
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
  BCardHeader,
  BCardTitle,
  BFormTextarea,
  BFormCheckbox,
  BInputGroupPrepend,
} from 'bootstrap-vue'

import axios from 'axios'
import { useInputImageRenderer } from '@core/comp-functions/forms/form-utils'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, alphaNum, email } from '@validations'

import VuePhoneNumberInput from 'vue-phone-number-input'
import 'vue-phone-number-input/dist/vue-phone-number-input.css'
import store from '@/store/index'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
// import ErrorMsg from '@/components/Aleart/Error'
// import SuccessMsg from '@/components/Aleart/Success'
// import SingleImageVue from 'src/components/SingleImag/SingleImage.vue'
import SingleImageVue from '@/components/SingleImag/SingleImage.vue'
import Cleave from 'vue-cleave-component'
import vSelect from 'vue-select'

export default {

  components: {
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
    BCardHeader,
    BCardTitle,
    BFormCheckbox,
    BInputGroupPrepend,
    vSelect,
    BFormTextarea,

    ValidationProvider,
    ValidationObserver,
    SingleImageVue,
    // ErrorMsg,
    // SuccessMsg,
    VuePhoneNumberInput,

  },
  setup() {

  },
  data() {
    return {
      // codeSimple,
      required,
      email,
      ServiceName: '',
      Duration: '',
      TreatmentType: '',
      serviceCategory: '',
      serviceCategoryOption: [],
      description: '',
      results: {},
      AfterCareDescription: '',
      msg: '',
      descriptionLength: 0,
      AfterCareDescriptionLength: 0,
      checkedBookinOnline: false,
      extraTime: false,
      iserror: false,
      issuccess: false,
      TreatmentOption: ['option 1', 'option 2', 'option 3', 'option 4', 'option 5', 'option 6'],
      genderOption: ['Everyone', 'Females', 'Males'],
      DurationOption: ['5min', '10min', '15min', '20min', '25min',
        '30min', '35min', '40min', '45min', '50min', '55min', '1h', '1h5min',
        '1h10min', '1h15min', '1h20min', '1h25min', '1h30min', '1h35min', '1h40m',
        '1h45min', '1h55min', '2h', '2h15min',
        '2h30min', '2h45', '3h', '3h15min',
        '3h30min', '3h45', '4h',
        '4h30min', '5h',
        '5h30min', '6h',
        '6h30min', '7h',
        '7h30min', '8h', '9h', '10h', '11h', '12h'],
      gender: '',
      RetailPrice: '30',
      PriceType: '',
      PriceTypeOption: ['Free', 'Fixed', 'From'],
      number: {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
      },
    }
  },
  mounted() {
    this.getCategory()
    this.getTreatment()
  },

  methods: {
    validationForm() {
      this.$refs.simpleRules.validate().then(success => {
        if (success) {
          // eslint-disable-next-line
                    const   userToken =localStorage.getItem('userToken');
          const token = JSON.parse(userToken)

          const config = {
            headers: { 'content-type': 'multipart/form-data', Authorization: `Bearer ${token}` },
          }
          const formdata = new FormData()

          formdata.append('name', this.ServiceName)
          formdata.append('Treatment_type', this.TreatmentType)
          formdata.append('category_id', this.serviceCategory)
          formdata.append('Aftercare_description', this.AfterCareDescription)
          formdata.append('description', this.description)

          formdata.append('service_gender', this.gender)
          formdata.append('online_booking', this.checkedBookinOnline)
          formdata.append('Duration', this.Duration)
          formdata.append('retail_price', this.RetailPrice)
          formdata.append('price_type', this.PriceType)
          formdata.append('extra_time', this.extraTime)

          axios.post('/api/admin/new-center-services', formdata, config)
            .then(res => {
              this.$router.replace('/catalogueList').then(() => {
                this.$toast({
                  component: ToastificationContent,
                  timeout: 5000,
                  props: {
                    title: 'add service successfuly',
                    icon: 'EditIcon',
                    variant: 'success',
                  },
                })
              })
              this.msg = 'Add service Successufly'
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
    calclength() {
      this.descriptionLength = this.description.length
      this.AfterCareDescriptionLength = this.AfterCareDescription.length
    },

    getCategory() {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)

      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }

      axios
        .get('/api/admin/center_categories', config)
        .then(res => {
          this.serviceCategoryOption = res.data.content.categories
        })
        .then(err => console.log(err))
    },
    getTreatment() {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)

      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }

      axios
        .get('/api/admin/center_treatment', config)
        .then(res => {
          this.TreatmentOption = res.data.content.treatment
        })
        .then(err => console.log(err))
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
.costoum-image{
    padding-left:24px ;
    padding-bottom: 10px;
    padding-top:38px;
    height: 75px;
}
</style>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
</style>
