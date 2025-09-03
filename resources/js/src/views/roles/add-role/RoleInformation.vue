<template>
  <div>

    <!-- Media -->
    <b-media class="mb-0">
      <template #aside>

      </template>


    </b-media>
<!--    <error-msg v-show="iserror"  :msg="msg" ></error-msg>-->
<!--    <success-msg v-show="issuccess" :msg="msg"></success-msg>-->
    <validation-observer ref="simpleRules">
    <!-- User Info: Input Fields -->
    <b-form>
      <b-row>

        <!-- Field: First Name -->
        <b-col
          cols="12"
          md="6"
        >
          <b-form-group
            label="Role Name"
            label-for="role_Name"
          >
            <validation-provider
                #default="{ errors }"
                name="Role Name"
                rules="required"
              >
            <b-form-input
              :state="errors.length > 0 ? false:null"
              v-model="roleName"
                  placeholder=""
            />
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
          </b-form-group>
        </b-col>


      </b-row>
<h1>Chose Permission</h1>
        <div class="d-flex flex-wrap justify-content-start">
            <div v-for="permission in permissions" :key="permission.id" class="w-25 mt-1">
                <!--        {{permission.can_delete == null?permission.can_delete=true:"" }}-->
                <b-form-checkbox v-model="permission.status"  >
                    {{permission.name  }}
                </b-form-checkbox>
            </div>
        </div>
    <!-- Action Buttons -->
    <b-button
      variant="primary"
      class="mb-1 mb-sm-0 mr-0 mr-sm-1"
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
  BFormCheckbox,
   BInputGroupPrepend,
} from 'bootstrap-vue'

import axios from 'axios'
import { useInputImageRenderer } from '@core/comp-functions/forms/form-utils'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import store from '@/store/index'
import {  required} from '@validations'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
// import ErrorMsg from '@/components/Aleart/Error'
// import SuccessMsg from '@/components/Aleart/Success'
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


    ValidationProvider,
    ValidationObserver,
    vSelect,
    // ErrorMsg,
    // SuccessMsg,

  },
  setup(){

  }
  ,
  data() {
    return {
      // codeSimple,
      required,

        permissions:[],

        regionId:'',
      roleName : '',


      //
      // msg:'',
      // iserror:false,
      // issuccess:false,

    }
  },
  mounted(){
  this.getPermissions();
  },


   methods: {
       validationForm() {
      this.$refs.simpleRules.validate().then(success => {
        if (success) {
          // eslint-disable-next-line
           const   userToken =localStorage.getItem('userToken');
            let token=JSON.parse(userToken);

          let config ={
            headers :{"content-type" : "multipart/form-data","Authorization" : `Bearer ` + token}
          }
          let formdata= new FormData();

          formdata.append('name',this.roleName)


        formdata.append("permissions", JSON.stringify(this.permissions));
          console.log('hi1');
          axios.post('/api/admin/new-role',formdata,config)
            .then(res =>{

          this.$router.replace('/roles/list').then(()=>{
            this.$toast({
            component: ToastificationContent,
             timeout: 5000,
            props: {
              title: 'add Role sucessfuly',
              icon: 'EditIcon',
              variant: 'success',
            },
          })})
          // this.msg = 'Add Role successufly';
          this.iserror=false;
          this.issuccess=true;

            }).catch(error=>{
                if(error.response.status == 422){
                    // this.msg = error.response.data.message;
                    this.iserror=true;
                   this.issuccess=false;
                }

        });

        }
      })
    },
      getPermissions(){
       const   userToken =localStorage.getItem('userToken');
            let token=JSON.parse(userToken);

          let config ={
            headers :{"Authorization" : `Bearer ` + token}
          }
      axios.get('/api/admin/get-permissions',config)
      .then(res =>{
        this.permissions = res.data.content ;
          for (let i = 0; i < this.permissions.length; i++) {
              this.permissions[i].status  = true;
          }
          console.log('permission =',this.permissions);
      })
      .then(err => console.log(err))
    }
  },

}
</script>

<style lang="scss" scoped>
.per-page-selector {
  width: 90px;
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
