<template>
  <div>
                   <error-msg v-show="iserror" :msg="msg"></error-msg>
               <success-msg v-show="issuccess" :msg="msg"></success-msg>

    <form-wizard color="#7367F0" :title="null" :subtitle="null" layout="vertical" finish-button-text="Submit"
      back-button-text="Previous" class="wizard-vertical mb-3" @on-complete="formSubmitted">

      <!-- chose services -->
      <tab-content title="Center Services"  >
        <div v-if="loadingtime" class="w-50 m-auto">
          <b-spinner variant="primary"  class=" " label="Spinning"></b-spinner>
        </div>
        <b-row v-else>
          <b-col cols="12" class="mb-2">
            <h5 class="mb-0">
              Services
            </h5>
            <small class="text-muted">
              Please checked service.
            </small>
          </b-col>
          <b-col v-for="category in categories " :key="category.id" cols="10" class="m-1">
            <h2 class="mb-0">
              {{ category.name }}
            </h2>
            <b-row v-for="service in category.Services" :key="service.id"
              class="m-1 d-flex align-items-between w-100 border border-light rounded">
              <b-col cols="10">
                <h3>{{ service.name }} </h3>
                <h5>{{ service.retail_price + " AAD" }} </h5>

                {{ service.Duration }}

              </b-col>
              <b-col cols="2">
                <b-form-checkbox :id="`b-${service.id}`" v-model="servicesIds" :value="service.id" name="check-button"
                  switch class="mt-2" />
              </b-col>
            </b-row>
            <div class="divider my-2" />
          </b-col>

        </b-row>
      </tab-content>

      <!-- personal info tab -->
      <tab-content title="TEAM">
        <b-row>
          <b-col cols="12" class="mb-2">
            <h5 class="mb-0">
              choose member
            </h5>
          </b-col>

          <b-col md="6">
            <b-form-group label="Member" label-for="v-country">
              <v-select id="v-country" v-model="member" :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                :options="team" label="first_name" :reduce="(name) => name.id" />
            </b-form-group>
          </b-col>
          <b-col md="6">
            <b-form-group label-for="v-language" label="Time">
              <v-select id="Language" v-model="shift" :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
           :options="shifts"
                        :disabled="!disibleShift" :reduce="(value) => value" label="text" >
                  <template v-slot:spinner="{ loading }">
                      <b-spinner v-show="loading" label="Loading..."   variant="primary"></b-spinner>
                  </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col md="6">
            <div>
              <label for="datepicker-placeholder"></label>
              <b-form-datepicker id="datepicker-placeholder" v-model="selectDate" placeholder="Choose a date"
                local="ar" />
            </div>
          </b-col>

        </b-row>
      </tab-content>

      <!-- social link -->
      <tab-content title="CLIENT">
        <b-row>
          <b-col md="9">
            <b-form-group label-for="v-language" label="Select Client">
              <v-select id="Language" v-model="client" :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                :options="clients" :reduce="(first_name) => first_name.id" class="Status-filter-select"
                placeholder="Type To Search " label="first_name" @search="getClientsSearch" />
            </b-form-group>
          </b-col>
        </b-row>
      </tab-content>

    </form-wizard>

  </div>
</template>

<script>
import { FormWizard, TabContent } from 'vue-form-wizard'
import vSelect from 'vue-select'
import 'vue-form-wizard/dist/vue-form-wizard.min.css'
import {
  BRow,
  BCol, BFormDatepicker,
  BFormGroup, BFormCheckbox,
  BFormInput,BSpinner
} from 'bootstrap-vue'
import ErrorMsg from "@/components/Aleart/Error";
import SuccessMsg from "@/components/Aleart/Success";
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import axios from 'axios'

export default {
  components: {
    FormWizard,
    TabContent,
    BRow,BSpinner,
    BFormDatepicker,
    BCol,
    BFormGroup,
    BFormCheckbox,
    BFormInput,
    vSelect,
    ErrorMsg,
SuccessMsg,
    // eslint-disable-next-line vue/no-unused-components
    ToastificationContent,
  },
  data() {
    return {
      member: 'select member',
      team: [],
      selectDate: '',
      disibleShift: false,
      clients: [],
      servicesIds: [],
      shift: 'nothing_selected',
      categories: {},
      center: {},
loadingtime:false,
      client: '',
      loading:true,
      msg: "",
            iserror: false,
            issuccess: false,

      shifts: [

      ],
    }
  },
  watch: {
    selectDate() {
      if (this.selectDate != '')
       this.disibleShift = true

       console.log('1',this.selectDate);
    this.getAvalibleShift();

    },
  },
  created() {
    this.getClients()
  },
  mounted() {
    console.log("1",this.loading)
    this.getCategories()
    console.log("2",this.loading)

    this.getCenter()
    console.log("3",this.loading)
    this.loading=false;
    console.log("4",this.loading)

  },
  methods: {
    formSubmitted() {
      // eslint-disable-next-line
      const userToken = localStorage.getItem('userToken');
      const token = JSON.parse(userToken)

      const config = {
        headers: { 'content-type': 'multipart/form-data', Authorization: `Bearer ${token}` },
      }
      const formdata = new FormData()
      // basic information
      for (let i = 0; i < this.servicesIds.length; i++) {
        formdata.append('services[]', this.servicesIds[i])
      }
      formdata.append('member', this.member)
      formdata.append('shift', this.shift)
      formdata.append('appointment_date', this.selectDate)

      formdata.append('client', this.client)

      axios.post('/api/admin/new-appointment', formdata, config)
        .then(res => {
          this.$router.replace('/centersList').then(() => {
            this.$toast({
              component: ToastificationContent,
              timeout: 5000,
              props: {
                title: 'add Appointment successfuly',
                icon: 'EditIcon',
                variant: 'success',
              },
            })
          })
          this.msg = 'Add center Successufly'
          this.iserror = false
          this.issuccess = true
        }).catch(error => {
          if (error.response.status == 422) {
            this.msg = error.response.data.message
            this.iserror = true
            this.issuccess = false
          }
        })
    },
    getCategories() {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)

      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }
      axios
        .get(`/api/admin/get-center-categories-center`, config)
        .then(res => {
          this.categories = res.data.content.categories
          console.log('category', this.categories)
        })
        .catch(err => {
          console.log(err)
        })
    },
    getCenter() {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)

      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }
      axios
        .get(`/api/admin/get-center`, config)
        .then(res => {
          this.center = res.data.content.center
          this.team = this.center.admins

          console.log(this.team)
        })
        .catch(err => {
          console.log(err)
        })
    },
    getClients() {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)

      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }
      axios
        .get('/api/admin/users', config)
        .then(res => {
          this.clients = res.data.content
        })
        .catch(err => {
          console.log(err)
        })
    },
    getClientsSearch(search, loading) {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)

      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }
      axios
        .get(`/api/admin/users?search=${search}`, config)
        .then(res => {
          this.clients = res.data.content
        })
        .catch(err => {
          console.log(err)
        })
    },
    getAvalibleShift() {
      const userToken = localStorage.getItem('userToken')
      const token = JSON.parse(userToken)

      const config = {
        headers: { Authorization: `Bearer ${token}` },
      }
      const formdata = new FormData()
      // basic information

      formdata.append('member', this.member)
      formdata.append('shift', this.shift)
      console.log('2',this.selectDate);

      formdata.append('appointment_date', this.selectDate)
      formdata.append("center_id",this.$route.params.id)

      axios
        .post('/api/admin/get-avaliable-shifts', formdata, config)
        .then(res => {
          this.shifts = res.data.content
        })
        .catch(err => {
          console.log(err)
        })
    },
     getKeyByValue(object, value) {
  return Object.keys(object).find(key => object[key] === value);
}
,
  },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-wizard.scss';
@import '~@resources/scss/vue/libs/vue-select.scss';
</style>
