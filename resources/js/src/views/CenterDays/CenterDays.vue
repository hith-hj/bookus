<template>
    <b-card>
        <b-tabs
            v-if="true"
            pills
        >

            <!-- Tab: Account -->


            <b-tab active>
          <template #title>
            <feather-icon icon="InfoIcon" size="16" class="mr-0 mr-sm-50" />
            <span class="d-none d-sm-inline">Open Days</span>
          </template>
          <!-- Saturday -->
          <b-row class="mt-1">
            <!-- Field: Saturday -->
            <b-col cols="2">
              <b-form-checkbox
                id="checkbox-1"
                v-model="saturdayStatus"
                name="checkbox-1"
              >
                {{ "Saturday" }}
              </b-form-checkbox>
            </b-col>
            <b-col cols="4">
              <div class="d-flex justify-content-start p-0 m-0">
                <h5>open time:</h5>
                <b-form-timepicker
                  v-model="Saturday.open"
                  :disabled="!saturdayStatus"
                  locale="en"
                ></b-form-timepicker>
              </div>
            </b-col>
            <b-col cols="4">
              <div class="d-flex justify-content-start p-0 m-0">
                <h5>close time:</h5>
                <b-form-timepicker
                  v-model="Saturday.close"
                  :disabled="!saturdayStatus"
                  locale="en"
                ></b-form-timepicker>
              </div>
            </b-col>
          </b-row>
          <!-- Sunday -->
          <b-row class="mt-1">
            <!-- Field: Sunday -->
            <b-col cols="2">
              <b-form-checkbox
                id="checkbox-2"
                v-model="sundayStatus"
                name="checkbox-2"
              >
                {{ "Sunday" }}
              </b-form-checkbox>
            </b-col>
            <b-col cols="4">
              <div class="d-flex justify-content-start p-0 m-0">
                <h5>open time:</h5>
                <b-form-timepicker
                  v-model="Sunday.open"
                  :disabled="!sundayStatus"
                  locale="en"
                ></b-form-timepicker>
              </div>
            </b-col>
            <b-col cols="4">
              <div class="d-flex justify-content-start p-0 m-0">
                <h5>close time:</h5>
                <b-form-timepicker
                  v-model="Sunday.close"
                  :disabled="!sundayStatus"
                  locale="en"
                ></b-form-timepicker>
              </div>
            </b-col>
          </b-row>
          <!-- Monday -->
          <b-row class="mt-1">
            <!-- Field: Whatsapp -->
            <b-col cols="2">
              <b-form-checkbox
                id="checkbox-3"
                v-model="mondayStatus"
                name="checkbox-3"
              >
                {{ "Monday" }}
              </b-form-checkbox>
            </b-col>
            <b-col cols="4">
              <div class="d-flex justify-content-start p-0 m-0">
                <h5>open time:</h5>
                <b-form-timepicker
                  v-model="Monday.open"
                  :disabled="!mondayStatus"
                  locale="en"
                ></b-form-timepicker>
              </div>
            </b-col>
            <b-col cols="4">
              <div class="d-flex justify-content-start p-0 m-0">
                <h5>close time:</h5>
                <b-form-timepicker
                  v-model="Monday.close"
                  :disabled="!mondayStatus"
                  locale="en"
                ></b-form-timepicker>
              </div>
            </b-col>
          </b-row>
          <!-- Tuesday -->
          <b-row class="mt-1">
            <!-- Field: Whatsapp -->
            <b-col cols="2">
              <b-form-checkbox
                id="checkbox-4"
                v-model="tuesdayStatus"
                name="checkbox-4"
              >
                {{ "Tuesday" }}
              </b-form-checkbox>
            </b-col>
            <b-col cols="4">
              <div class="d-flex justify-content-start p-0 m-0">
                <h5>open time:</h5>
                <b-form-timepicker
                  v-model="Tuesday.open"
                  :disabled="!tuesdayStatus"
                  locale="en"
                ></b-form-timepicker>
              </div>
            </b-col>
            <b-col cols="4">
              <div class="d-flex justify-content-start p-0 m-0">
                <h5>close time:</h5>
                <b-form-timepicker
                  v-model="Tuesday.close"
                  :disabled="!tuesdayStatus"
                  locale="en"
                ></b-form-timepicker>
              </div>
            </b-col>
          </b-row>
          <!-- Wednesday -->
          <b-row class="mt-1">
            <!-- Field: Whatsapp -->
            <b-col cols="2">
              <b-form-checkbox
                id="checkbox-5"
                v-model="wednesdayStatus"
                name="checkbox-5"
              >
                {{ "Wednesday" }}
              </b-form-checkbox>
            </b-col>
            <b-col cols="4">
              <div class="d-flex justify-content-start p-0 m-0">
                <h5>open time:</h5>
                <b-form-timepicker
                  v-model="Wednesday.open"
                  :disabled="!wednesdayStatus"
                  locale="en"
                ></b-form-timepicker>
              </div>
            </b-col>
            <b-col cols="4">
              <div class="d-flex justify-content-start p-0 m-0">
                <h5>close time:</h5>
                <b-form-timepicker
                  v-model="Wednesday.close"
                  :disabled="!wednesdayStatus"
                  locale="en"
                ></b-form-timepicker>
              </div>
            </b-col>
          </b-row>
          <!-- Thursday -->
          <b-row class="mt-1">
            <!-- Field: Whatsapp -->
            <b-col cols="2">
              <b-form-checkbox
                id="checkbox-6"
                v-model="thursdayStatus"
                name="checkbox-6"
              >
                {{ "Thursday" }}
              </b-form-checkbox>
            </b-col>
            <b-col cols="4">
              <div class="d-flex justify-content-start p-0 m-0">
                <h5>open time:</h5>
                <b-form-timepicker
                  v-model="Thursday.open"
                  :disabled="!thursdayStatus"
                  locale="en"
                ></b-form-timepicker>
              </div>
            </b-col>
            <b-col cols="4">
              <div class="d-flex justify-content-start p-0 m-0">
                <h5>close time:</h5>
                <b-form-timepicker
                  v-model="Thursday.close"
                  :disabled="!thursdayStatus"
                  locale="en"
                ></b-form-timepicker>
              </div>
            </b-col>
          </b-row>
          <!-- Friday -->
          <b-row class="mt-1">
            <!-- Field: Whatsapp -->
            <b-col cols="2">
              <b-form-checkbox
                id="checkbox-7"
                v-model="fridayStatus"
                name="checkbox-7"
              >
                {{ "Friday" }}
              </b-form-checkbox>
            </b-col>
            <b-col cols="4">
              <div class="d-flex justify-content-start p-0 m-0">
                <h5>open time:</h5>
                <b-form-timepicker
                  v-model="Friday.open"
                  :disabled="!fridayStatus"
                  locale="en"
                ></b-form-timepicker>
              </div>
            </b-col>
            <b-col cols="4">
              <div class="d-flex justify-content-start p-0 m-0">
                <h5>close time:</h5>
                <b-form-timepicker
                  v-model="Friday.close"
                  :disabled="!fridayStatus"
                  locale="en"
                ></b-form-timepicker>
              </div>
            </b-col>
          </b-row>
        </b-tab>
               

            <!-- Tab: Information -->

            <!-- Tab: Social -->

        </b-tabs>
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
    </b-card>

</template>

<script>
import { BCard, BCardText ,BAlert, BRow, BCol, BLink,BTab, BTabs,BFormCheckbox,BButton,
    BFormTimepicker,
} from 'bootstrap-vue'
import axios from 'axios'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
    components: {
        BCard,BButton,
        BCardText,
        BAlert,
        BRow,
        BCol,
        BLink,
        BTab,
  BFormTimepicker,
        BTabs,
        BTab,BFormCheckbox
        // Local Components

    },
    data(){
        return{
          days:[],
          saturdayStatus: false,
      sundayStatus: false,
      mondayStatus: false,
      tuesdayStatus: false,
      wednesdayStatus: false,
      thursdayStatus: false,
      fridayStatus: false,
      Saturday: {
        open: "",
        close: "",
      },
      Sunday: { status: true, open: "", close: "" },
      Monday: { status: true, open: "", close: "" },
      Tuesday: { status: true, open: "", close: "" },
      Wednesday: { status: true, open: "", close: "" },
      Thursday: { status: true, open: "", close: "" },
      Friday: { status: true, open: "", close: "" },
    
        }
    },
    created(){
    },
    methods:{
      validationForm() {
          
                    // eslint-disable-next-line
                    const   userToken =localStorage.getItem('userToken');
                    let token=JSON.parse(userToken);

                    let config ={
                        headers :{"content-type" : "multipart/form-data","Authorization" : `Bearer ` + token}
                    }
                    let formdata= new FormData();
                  
                    //open days information
                    if(this.saturdayStatus){
                        formdata.append("saturdayOpen", this.Saturday.open);
                        formdata.append("saturdayClose", this.Saturday.close);
                    }
                    if(this.sundayStatus){
                        formdata.append("sundayOpen", this.Sunday.open);
                        formdata.append("sundayClose", this.Sunday.close);
                    }

                    if(this.mondayStatus){
                        formdata.append("mondayOpen", this.Monday.open);
                        formdata.append("mondayClose", this.Monday.close);
                    }
                    if(this.tuesdayStatus){
                        formdata.append("tuesdayOpen", this.Tuesday.open);
                        formdata.append("tuesdayClose", this.Tuesday.close);
                    }

                    if(this.wednesdayStatus){
                        formdata.append("wednesdayOpen", this.Wednesday.open);
                        formdata.append("wednesdayClose", this.Wednesday.close);
                    }

                    // thursdayStatus
                    if(this.thursdayStatus){
                        formdata.append("thursdayOpen", this.Thursday.open);
                        formdata.append("thursdayClose", this.Thursday.close);
                    }

                    if(this.fridayStatus){
                        formdata.append("fridayOpen", this.Friday.open);
                        formdata.append("fridayClose", this.Friday.close);
                    }
                 


                    axios.post('/api/admin/update-center-days',formdata,config)
                        .then(res =>{
                     
                                this.$toast({
                                    component: ToastificationContent,
                                    timeout: 5000,
                                    props: {
                                        title: 'update center days successfuly',
                                        icon: 'EditIcon',
                                        variant: 'success',
                                    },
                                })
                            this.msg = 'update center days Successufly';
                            this.iserror=false;
                            this.issuccess=true;

                        }).catch(error=>{
                        if(error.response.status == 422){
                            this.msg = error.response.data.message;
                            this.iserror=true;
                            this.issuccess=false;
                        }

                    });
            
        },
    getCenter(){
            const userToken = localStorage.getItem("userToken");
            let token = JSON.parse(userToken);
            let config = {
                headers: { Authorization: `Bearer ` + token },
            };
            axios
                .get("/api/admin/get-center", config)
                .then((res) => {
                    this.center = res.data.content.center;
                   
                    this.days = this.center.days;
                
                   

                    for(let i=0 ;i<this.days.length;i++){
                        if(this.days[i].day=="Saturday"){
                           this.saturdayStatus=true
                           this.Saturday.open=this.days[i].open;
                           this.Saturday.close=this.days[i].close;

                        }
                        if(this.days[i].day=="Sunday"){
                        this.sundayStatus=true
                        this.Sunday.open=this.days[i].open;
                           this.Sunday.close=this.days[i].close;

                        }
                        if(this.days[i].day=="Monday"){
                          this.mondayStatus=true;
                          this.Monday.open=this.days[i].open;
                           this.Monday.close=this.days[i].close;
                        }
                        if(this.days[i].day=="Tuesday"){
                            this.tuesdayStatus=true;
                            this.Tuesday.open=this.days[i].open;
                           this.Tuesday.close=this.days[i].close;
                        }
                        if(this.days[i].day=="Wednesday"){
                            this.wednesdayStatus=true;
                            this.Wednesday.open=this.days[i].open;
                            this.Wednesday.close=this.days[i].close;
                        }
                        if(this.days[i].day=="Thursday"){
                             this.thursdayStatus=true;
                             this.Thursday.open=this.days[i].open;
                             this.Thursday.close=this.days[i].close;
                        }
                        if(this.days[i].day=="Friday"){
                            this.fridayStatus =true;
                            this.Friday.open=this.days[i].open;
                             this.Friday.close=this.days[i].close;

                        }
                    }

                })
                .catch((err) => {
                    console.log(err);
                });
            return;
        }

    },
    mounted() {
      this.getCenter()
    },
}
</script>

<style>

</style>
