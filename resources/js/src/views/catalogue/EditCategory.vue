<template>
    <b-card>


            <!-- Tab: Account -->


                    <span class="d-none d-sm-inline .text-primary ">Add Category</span>

  <div>

    <!-- Media -->
    <b-media class="mb-0">
      <template #aside />
    </b-media>
           <error-msg v-show="iserror"  :msg="msg" ></error-msg>
           <success-msg v-show="issuccess" :msg="msg"></success-msg>
    <validation-observer ref="simpleRules">
      <!-- User Info: Input Fields -->
      <b-form>
        <b-row>

          <!-- Field: First Name -->
          <b-col
            cols="7"
          >
            <b-form-group
              label="Category Name"
              label-for="category_name"
            >
              <validation-provider
                #default="{ errors }"
                name="Category Name"
                rules="required"
              >
                <b-form-input
                  v-model="CategoryName"
                  :state="errors.length > 0 ? false:null"
                  placeholder=""
                />
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <b-col
            cols="7"
            md="7"
            class="mt-1"
          >
            <validation-provider
              #default="{ errors }"
              name="Status"
              rules="required"
            >
              <b-form-group
                label="Status"
                label-for="Status"
              >
                <v-select
                  v-model="statusCenter"
                  :dir="'ltr'"
                  :options="statusOptions"
                  :clearable="false"
                  input-id="Status"
                  class="Status-filter-select"
                  placeholder="Select Status"
                />
                <small class="text-danger">{{ errors[0] }}</small>
              </b-form-group>
            </validation-provider>
          </b-col>
          <b-col cols="12"  class="p-1">

            <SingleImageVue :ImageFile.sync="image"></SingleImageVue>
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
    </b-card>
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
import ErrorMsg from '@/components/Aleart/Error'
import SuccessMsg from '@/components/Aleart/Success'
// import SingleImageVue from 'src/components/SingleImag/SingleImage.vue'
import SingleImageVue from '@/components/SingleImag/SingleImage.vue'

import vSelect from 'vue-select'
import useUsersList from '@/views/centers/useUsersList'

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
    ErrorMsg,
    SuccessMsg,
    VuePhoneNumberInput,

  },
  setup() {
    const statusOptions = ['active', 'inactive']

    return { statusOptions }
  },
  data() {
    return {
      // codeSimple,
      required,
      email,
      CategoryName: '',
      category:"",
      statusCenter: 'active',
      msg: '',
      image:null,
      iserror: false,
      issuccess: false,
    }
  },
  mounted() {
this.getCategory();
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

          formdata.append('name', this.CategoryName)

          if (this.statusCenter == 'active') formdata.append('status', 1)
          else formdata.append('status', 0)

          if(this.image)
            formdata.append("image", this.image);
          axios.post('/api/admin/edit-center-category/'+this.$route.params.id, formdata, config)
            .then(res => {
              this.$router.replace('/catalogueList').then(() => {
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
              this.msg = 'Add Category Successufly'
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
    getCategory() {
      const userToken = localStorage.getItem("userToken");
      const token = JSON.parse(userToken);
      const userDetails = this.$store.getters['auth/getUser']
      console.log('user',userDetails);
      const config = {
        headers: { Authorization: `Bearer ${token}` },
      };
      axios
        .get(`/api/admin/center_category/${this.$route.params.id}`, config)
        .then((res) => {
          this.category = res.data.content.category;
          this.image='/storage/'+this.category.image;
          this.CategoryName =this.category.name;
          if(this.category.status==1)
          this.statusCenter ='active'
        else
        this.statusCenter ='inactive'

        })
        .catch((err) => {
          console.log(err);
        });
    }

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
