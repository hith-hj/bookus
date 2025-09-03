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
        <b-row class="m-1">
          <b-col cols="3" 
                    
                    class="d-flex align-items-center justify-content-start  m-0 p-0"
                    >
                       
                <label>{{ t('select_contact') }}</label>

                        <v-select
                            v-model="contactSelect"
                            :options="listContacte"
                            :clearable="false"
                            class="contact d-inline-block mx-100"
                        />
                    </b-col>
                    <b-col cols="3">
                        
                    <b-form-input
                        id="name-input"
                        v-model="newContact"
                        required
                        placeholder="facebook/yourAddress"
                    ></b-form-input>
          
                    </b-col>
        </b-row>

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
  BCard,BInputGroup,
  BCardHeader,
  BCardTitle,
  BFormTextarea,
  BFormCheckbox,
  BInputGroupPrepend,BInputGroupAppend
} from 'bootstrap-vue'

import axios from 'axios'
import { useInputImageRenderer } from '@core/comp-functions/forms/form-utils'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, alphaNum, email } from '@validations'
import { togglePasswordVisibility } from '@core/mixins/ui/forms'

import VuePhoneNumberInput from 'vue-phone-number-input'
import 'vue-phone-number-input/dist/vue-phone-number-input.css'
import store from '@/store/index'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
// import ErrorMsg from '@/components/Aleart/Error'
// import SuccessMsg from '@/components/Aleart/Success'
import SingleImageVue from '@/components/SingleImag/SingleImage.vue'
import { useUtils as useI18nUtils } from '@core/libs/i18n'

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
    BFormTextarea,BInputGroupAppend,BInputGroup,

    ValidationProvider,
    ValidationObserver,
    SingleImageVue,
    // ErrorMsg,
    // SuccessMsg,
    VuePhoneNumberInput,
  },
  setup() {
    const statusOptions = ['active', 'inactive']
    const { t } = useI18nUtils()
    return { statusOptions,t }
  },
  mixins: [togglePasswordVisibility],

  data() {
    return {
      // codeSimple,
      required,
      email,
      listContacte:["phone number","whatsapp","X","landline","telegram",'facebook'],
      contactSelect:"facebook",
      newContact:"",
    
      msg: '',
      iserror: false,
      issuccess: false,
    }
  },
  mounted() {

  },
  computed: {
   
   
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

          formdata.append('key', this.contactSelect)
          formdata.append('value', this.newContact)
        

          axios.post('/api/admin/new-contact', formdata, config)
            .then(res => {
              this.$router.replace('/contactList').then(() => {
                this.$toast({
                  component: ToastificationContent,
                  timeout: 5000,
                  props: {
                    title: 'add contact successfuly',
                    icon: 'EditIcon',
                    variant: 'success',
                  },
                })
              })
              this.msg = 'Add contact Successufly'
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

  },

}
</script>

<style lang="scss" scoped>
.per-page-selector {
}
.contact {
    width: 300px;
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
