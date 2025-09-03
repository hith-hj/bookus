<template>
    <div>

        <!-- Media -->
        <b-media class="mb-0">
            <template #aside>

            </template>
        </b-media>
<!--        <error-msg v-show="iserror"  :msg="msg" ></error-msg>-->
<!--        <success-msg v-show="issuccess" :msg="msg"></success-msg>-->
        <b-card>
            <b-tabs
                v-if="true"
                pills
            >
                <b-tab active>
                    <!-- Tab: Account -->
                    <template #title>
                        <feather-icon
                            icon="UserIcon"
                            size="16"
                            class="mr-0 mr-sm-50"
                        />
                        <span class="d-none d-sm-inline .text-primary ">Edit Center</span>
                    </template>

        <validation-observer ref="simpleRules">
            <!-- User Info: Input Fields -->
            <b-form>
                <b-row>

                    <!-- Field: Name -->
                    <b-col
                        cols="6"
                    >
                        <b-form-group
                            label="Name"
                            label-for="name"
                        >
                            <validation-provider
                                #default="{ errors }"
                                name="Name"
                                rules="required"
                            >
                                <b-form-input
                                    :state="errors.length > 0 ? false:null"
                                    v-model="name"
                                    placeholder="center name"
                                />
                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>

                    <!-- email -->
                    <b-col cols="6">
                        <validation-provider
                            #default="{ errors }"
                            name="Email"
                            rules=""
                        >
                            <!-- Field: Email -->

                            <b-form-group label="Email" label-for="email">
                                <validation-provider
                                    #default="{ errors }"
                                    name="email"
                                    rules="required|email"
                                >
                                    <b-form-input
                                        id="email"
                                        type="email"
                                        :state="errors.length > 0 ? false : null"
                                        v-model="mail"
                                        placeholder="Email"
                                    />
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-form-group>


                        </validation-provider>
                    </b-col>
                    <!-- details  -->
                    <b-col cols="6" md="6" class="mt-1">
                        <validation-provider
                            #default="{ errors }"
                            name="Status"
                            rules="required"
                        >
                            <b-form-group label="Status" label-for="Status">
                                <v-select
                                    v-model="statusCenter"
                                    :dir="'ltr'"
                                    :options="statusOptions"
                                    :clearable="false"
                                    input-id="Status"
                                    class="Status-filter-select"
                                    placeholder="Select Status"
                                >
                                </v-select>
                                <small class="text-danger">{{ errors[0] }}</small>
                            </b-form-group>
                        </validation-provider>
                    </b-col>
                    <!-- Phone Number -->
                    <b-col cols="6"  class="pt-1">
                        <label >phone number</label>
                        <br>
                        <VuePhoneNumberInput
                            default-country-code="AE"
                            @update="onUpdate"
                            v-model="phoneNumber"
                        />
                    </b-col>
                    <!-- Field: Name -->
                    <b-col
                        cols="6"
                    >
                        <label>Currency country</label>
                        <br>
                        <v-select
                            v-model="currency"
                            :dir="$store.state.appConfig.isRTL ? 'rtl' : 'ltr'"
                            :options="currency_list"
                            :clearable="false"
                            label="name"
                            :reduce="(name) => name.code"

                            class=""
                        />

                    </b-col>

                    <!-- Field: about -->
                    <b-col
                        cols="12"
                    >
                        <b-form-group
                            label="about"
                            label-for="Address"
                        >
                            <validation-provider
                                #default="{ errors }"
                                name="about"
                                rules=""
                            >
                                <b-form-textarea
                                    id="textarea"
                                    v-model="about"
                                    placeholder="Enter something..."
                                    rows="3"
                                    max-rows="6"
                                ></b-form-textarea>

                                <small class="text-danger">{{ errors[0] }}</small>
                            </validation-provider>
                        </b-form-group>
                    </b-col>


                    <!-- logo -->

                    <b-col cols="12"  class="p-1">
                        <label>LOGO</label>
                        <SingleImageVue :ImageFile.sync="logo"></SingleImageVue>
                    </b-col>
<!--                <b-col cols="12" class="m-1">-->
<!--                    <vue-dropzone-->
<!--                        ref="myVueDropzone"-->
<!--                        id="dropzone"-->
<!--                        :options="dropzoneOptions"-->
<!--                        v-on:vdropzone-sending="sendingEvent"-->
<!--                    />-->

<!--                </b-col>-->
                    <b-col cols="3"  class="p-1">
                        <SingleImageVue :ImageFile.sync="image1"></SingleImageVue>
                    </b-col>
                    <b-col cols="3"  class="p-1">
                    <SingleImageVue :ImageFile.sync="image2"></SingleImageVue>
                </b-col>
                    <b-col cols="3"  class="p-1">
                    <SingleImageVue :ImageFile.sync="image3"></SingleImageVue>
                </b-col>

                </b-row>

                <!-- Action Buttons -->

            </b-form>
        </validation-observer>

<!--                    <Client-Information></Client-Information>-->
                    <!-- Tab: Information -->
                    <!-- Tab: Social -->
                </b-tab>
                <b-tab>
                    <template #title>
                        <feather-icon
                            icon="Share2Icon"
                            size="16"
                            class="mr-0 mr-sm-50"
                        />
                        <span class="d-none d-sm-inline">Contact</span>
                    </template>
                    <b-row v-for="contact in contacts" :key="contact.id" class="mt-1">

                        <!-- Field: Whatsapp -->
                        <b-col cols="2">
                       <label >{{ contact.key }}:</label>
                        </b-col>
                        <b-col
                            cols="6"
                        >


                                    <b-form-input
                                        v-model="contact.value"
                                        placeholder="whatsapp"
                                    />

                        </b-col>
                        <b-col cols="2">
                            <b-form-checkbox
                            :id="`check-${contact.id}`"
                                v-model="contactsDelete"
                                class="mb-3"
                               :value="contact.id"
                                disabled-field="notEnabled"
                                >delete</b-form-checkbox>
                        </b-col>


                    </b-row>
                </b-tab>
                <b-tab>
                    <template #title>
                        <feather-icon
                            icon="InfoIcon"
                            size="16"
                            class="mr-0 mr-sm-50"
                        />
                        <span class="d-none d-sm-inline">Open Days</span>
                    </template>
                    <!-- Saturday -->
                    <b-row class="mt-1">

                        <!-- Field: Saturday -->
                        <b-col
                            cols="2"
                        >
                            <b-form-checkbox
                                id="checkbox-1"
                                v-model="saturdayStatus"
                                name="checkbox-1"

                            >
                                {{"Saturday" }}
                            </b-form-checkbox>

                        </b-col>
                        <b-col
                            cols="4"
                        >
                            <div class="d-flex justify-content-start p-0 m-0">
                                <h5>open time:</h5>
                                <b-form-timepicker
                                    v-model="Saturday.open"
                                    :disabled="!saturdayStatus"
                                    locale="en"></b-form-timepicker>
                            </div>
                        </b-col>
                        <b-col
                            cols="4"
                        >
                            <div class="d-flex justify-content-start p-0 m-0">
                            <h5>close time:</h5>
                            <b-form-timepicker
                                v-model="Saturday.close"
                                :disabled="!saturdayStatus"
                                locale="en"></b-form-timepicker>
                            </div>
                        </b-col>
                    </b-row >
                    <!-- Sunday -->
                    <b-row class="mt-1">

                        <!-- Field: Sunday -->
                        <b-col
                            cols="2"
                        >
                            <b-form-checkbox
                                id="checkbox-2"
                                v-model="sundayStatus"
                                name="checkbox-2"

                            >
                                {{"Sunday" }}
                            </b-form-checkbox>

                        </b-col>
                        <b-col
                            cols="4"
                        >
                            <div class="d-flex justify-content-start p-0 m-0">
                                <h5>open time:</h5>
                                <b-form-timepicker
                                    v-model="Sunday.open"
                                    :disabled="!sundayStatus"
                                    locale="en"></b-form-timepicker>
                            </div>
                        </b-col>
                        <b-col
                            cols="4"
                        >
                            <div class="d-flex justify-content-start p-0 m-0">
                                <h5>close time:</h5>
                                <b-form-timepicker
                                    v-model="Sunday.close"
                                    :disabled="!sundayStatus"
                                    locale="en"></b-form-timepicker>
                            </div>
                        </b-col>

                    </b-row>
                    <!-- Monday -->
                    <b-row class="mt-1">

                        <!-- Field: Whatsapp -->
                        <b-col
                            cols="2"
                        >
                            <b-form-checkbox
                                id="checkbox-3"
                                v-model="mondayStatus"
                                name="checkbox-3"

                            >
                                {{"Monday" }}
                            </b-form-checkbox>

                        </b-col>
                        <b-col
                            cols="4"
                        >
                            <div class="d-flex justify-content-start p-0 m-0">
                                <h5>open time:</h5>
                                <b-form-timepicker
                                    v-model="Monday.open"
                                    :disabled="!mondayStatus"
                                    locale="en"></b-form-timepicker>
                            </div>
                        </b-col>
                        <b-col
                            cols="4"
                        >
                            <div class="d-flex justify-content-start p-0 m-0">
                                <h5>close time:</h5>
                                <b-form-timepicker
                                    v-model="Monday.close"
                                    :disabled="!mondayStatus"
                                    locale="en"></b-form-timepicker>
                            </div>
                        </b-col>
                    </b-row>
                    <!-- Tuesday -->
                    <b-row class="mt-1">

                        <!-- Field: Whatsapp -->
                        <b-col
                            cols="2"
                        >
                            <b-form-checkbox
                                id="checkbox-4"
                                v-model="tuesdayStatus"
                                name="checkbox-4"

                            >
                                {{"Tuesday" }}
                            </b-form-checkbox>

                        </b-col>
                        <b-col
                            cols="4"
                        >
                            <div class="d-flex justify-content-start p-0 m-0">
                                <h5>open time:</h5>
                                <b-form-timepicker
                                    v-model="Tuesday.open"
                                    :disabled="!tuesdayStatus"
                                    locale="en"></b-form-timepicker>
                            </div>
                        </b-col>
                        <b-col
                            cols="4"
                        >
                            <div class="d-flex justify-content-start p-0 m-0">
                                <h5>close time:</h5>
                                <b-form-timepicker
                                    v-model="Tuesday.close"
                                    :disabled="!tuesdayStatus"
                                    locale="en"></b-form-timepicker>
                            </div>
                        </b-col>
                    </b-row>
                    <!-- Wednesday -->
                    <b-row class="mt-1">

                        <!-- Field: Whatsapp -->
                        <b-col
                            cols="2"
                        >
                            <b-form-checkbox
                                id="checkbox-5"
                                v-model="wednesdayStatus"
                                name="checkbox-5"

                            >
                                {{"Wednesday" }}
                            </b-form-checkbox>

                        </b-col>
                        <b-col
                            cols="4"
                        >
                            <div class="d-flex justify-content-start p-0 m-0">
                                <h5>open time:</h5>
                                <b-form-timepicker
                                    v-model="Wednesday.open"
                                    :disabled="!wednesdayStatus"
                                    locale="en"></b-form-timepicker>
                            </div>
                        </b-col>
                        <b-col
                            cols="4"
                        >
                            <div class="d-flex justify-content-start p-0 m-0">
                                <h5>close time:</h5>
                                <b-form-timepicker
                                    v-model="Wednesday.close"
                                    :disabled="!wednesdayStatus"
                                    locale="en"></b-form-timepicker>
                            </div>
                        </b-col>
                    </b-row>
                    <!-- Thursday -->
                    <b-row class="mt-1">

                        <!-- Field: Whatsapp -->
                        <b-col
                            cols="2"
                        >
                            <b-form-checkbox
                                id="checkbox-6"
                                v-model="thursdayStatus"
                                name="checkbox-6"

                            >
                                {{"Thursday" }}
                            </b-form-checkbox>

                        </b-col>
                        <b-col
                            cols="4"
                        >
                            <div class="d-flex justify-content-start p-0 m-0">
                                <h5>open time:</h5>
                                <b-form-timepicker
                                    v-model="Thursday.open"
                                    :disabled="!thursdayStatus"
                                    locale="en"></b-form-timepicker>
                            </div>
                        </b-col>
                        <b-col
                            cols="4"
                        >
                            <div class="d-flex justify-content-start p-0 m-0">
                                <h5>close time:</h5>
                                <b-form-timepicker
                                    v-model="Thursday.close"
                                    :disabled="!thursdayStatus"
                                    locale="en"></b-form-timepicker>
                            </div>
                        </b-col>
                    </b-row>
                    <!-- Friday -->
                    <b-row class="mt-1">

                        <!-- Field: Whatsapp -->
                        <b-col
                            cols="2"
                        >
                            <b-form-checkbox
                                id="checkbox-7"
                                v-model="fridayStatus"
                                name="checkbox-7"

                            >
                                {{"Friday" }}
                            </b-form-checkbox>

                        </b-col>
                        <b-col
                            cols="4"
                        >
                            <div class="d-flex justify-content-start p-0 m-0">
                                <h5>open time:</h5>
                                <b-form-timepicker
                                    v-model="Friday.open"
                                    :disabled="!fridayStatus"
                                    locale="en"></b-form-timepicker>
                            </div>
                        </b-col>
                        <b-col
                            cols="4"
                        >
                            <div class="d-flex justify-content-start p-0 m-0">
                                <h5>close time:</h5>
                                <b-form-timepicker
                                    v-model="Friday.close"
                                    :disabled="!fridayStatus"
                                    locale="en"></b-form-timepicker>
                            </div>
                        </b-col>
                    </b-row>
                </b-tab>
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
    BFormCheckbox,BFormTimepicker,
    BInputGroupPrepend, BTabs, BTab, BCardText, BAlert, BLink,
} from 'bootstrap-vue'

import axios from 'axios'
import { useInputImageRenderer } from '@core/comp-functions/forms/form-utils'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { required, alphaNum, email } from "@validations";

import VuePhoneNumberInput from "vue-phone-number-input";
import "vue-phone-number-input/dist/vue-phone-number-input.css";
import store from '@/store/index'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
// import ErrorMsg from '@/components/Aleart/Error'
// import SuccessMsg from '@/components/Aleart/Success'
// import SingleImageVue from 'src/components/SingleImag/SingleImage.vue'
import SingleImageVue from "@/components/SingleImag/SingleImage.vue";
import useUsersList from './useUsersList'

import vSelect from "vue-select";
import vueDropzone from 'vue2-dropzone-vue3'
export default {

    components: {
        BTab, BTabs,
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
        BInputGroupPrepend,vSelect,BFormTimepicker,
        BFormTextarea,
        BCardText,
        BAlert,

        BLink,

        ValidationProvider,
        ValidationObserver,
        SingleImageVue,
        vueDropzone,
        // ErrorMsg,
        // SuccessMsg,
        VuePhoneNumberInput,

    },
    setup(){
        const statusOptions = ['active', 'inactive']
        const {

            currency_list
        } = useUsersList()

        return{statusOptions,currency_list}

    }
    ,
    data() {
        return {
            // codeSimple,
            required,
            email,
            name : '',
            logo:null,
            image1:null,
            image2:null,
            image3:null,

            saturdayStatus:false,
            sundayStatus:false,
            mondayStatus:false,
            tuesdayStatus:false,
            wednesdayStatus:false,
            thursdayStatus:false,
            fridayStatus :false,
            center:null,
            images:[],
            contacts:null,
            days:null,
            Saturday : {
                        open   :"",
                        close  :""
            },
            Sunday : {status:true,
                open   :"",
                close  :""
            },
            Monday : {status:true,
                open   :"",
                close  :""
            },
            Tuesday : {status:true,
                open   :"",
                close  :""
            },
            Wednesday : {status:true,
                open   :"",
                close  :""
            },  Thursday : {status:true,
                open   :"",
                close  :""
            },
            Friday : {status:true,
                open   :"",
                close  :""
            },
            mail:"",
            about:"",
            currency:"",
            results: {},
            statusCenter: "active",
            msg:'',
            iserror:false,
            issuccess:false,
            X:'',
            facebook:'',
            telegram:"",
            whatsapp:"",
            phoneNumber:'',
            contactsDelete:[],
            dropzoneOptions: {
                url: '/api/web/import-images',
                thumbnailWidth: 150,
                maxFilesize: 0.5,
                headers :{ "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content  },

            }
        }
    },
    mounted(){
this.getCenter();
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
                    //basic information
                    formdata.append('name',this.name)
                    formdata.append('about',this.about)
                    formdata.append('email',this.mail)
                    formdata.append('phoneNumber',this.phoneNumber)
                    formdata.append('currency',this.currency)

                    //social information
                    if(this.whatsapp != "")
                        formdata.append('whatsapp',this.whatsapp)
                    if(this.X != "")
                        formdata.append('X',this.X)
                    if(this.X != "")
                        formdata.append('telegram',this.telegram)
                    if(this.facebook != "")
                        formdata.append('facebook',this.facebook)

                    

                    if(this.logo)
                        formdata.append("logo", this.logo);
                    if(typeof this.image1 == "object" && this.image1 != null){
                        formdata.append("images[]", this.image1);

                    }

                    if(typeof this.image1 == "string")
                    formdata.append("old_images[]", this.images.images[0]);


                    if(typeof this.image2 == "object" && this.image2 != null)
                        formdata.append("images[]", this.image2);
                        if(typeof this.image2 == "string")
                        formdata.append("old_images[]", this.images.images[1]);


                    if(typeof this.image3 == "object" && this.image3 != null)
                        formdata.append("images[]", this.image3);

                        if(typeof this.image3 == "string")
                        formdata.append("old_images[]", this.images.images[2]);


                    if (this.statusCenter == "active") formdata.append("status", 1);
                    else formdata.append("status", 0);

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
                    for (let i = 0; i < this.contactsDelete.length; i++) {
                        formdata.append("contactDelete[]", this.contactsDelete[i]);

                        }

                        formdata.append("contacts",JSON.stringify(this.contacts));
                    formdata.append("_method", "put");

                    axios.post('/api/admin/centers/'+ this.$route.params.id,formdata,config)
                        .then(res =>{
                            this.$router.replace("/centersList").then(() => {
                                this.$toast({
                                    component: ToastificationContent,
                                    timeout: 5000,
                                    props: {
                                        title: 'add center successfuly',
                                        icon: 'EditIcon',
                                        variant: 'success',
                                    },
                                })})
                            this.msg = 'Add center Successufly';
                            this.iserror=false;
                            this.issuccess=true;

                        }).catch(error=>{
                        if(error.response.status == 422){
                            this.msg = error.response.data.message;
                            this.iserror=true;
                            this.issuccess=false;
                        }

                    });
                }
            })
        },
        onUpdate(payload) {
            this.results = payload;
        },
        sendingEvent(file, xhr, formData) {

            this.$refs.myVueDropzone.autoProcessQueue = true;
            this.$refs.myVueDropzone.processQueue();
        },
        triggerSend() {
            this.sendingEvent();
        },
        getCenter(){
            const userToken = localStorage.getItem("userToken");
            let token = JSON.parse(userToken);

            let config = {
                headers: { Authorization: `Bearer ` + token },
            };
            axios
                .get("/api/admin/centers/" + this.$route.params.id, config)
                .then((res) => {
                    this.center = res.data.content.center;
                    this.name=this.center.name;
                    this.mail=this.center.email;
                    this.about=this.center.about;

                    this.images = this.center.images;
                    this.contacts = this.center.contacts;
                    this.days = this.center.days;
                    if(this.center.status == 0)
                    this.statusCenter="inactive"

                    this.phoneNumber=this.center.phone_number;
                    this.currency=this.center.currency
                    this.logo='/storage/'+this.center.images.logo
                    if(this.images.images.length>0){
                        this.images.images.forEach((element,index,array) => {
                            if(index == 0)
                            this.image1= '/storage/'+this.images.images[index];
                            if(index == 1)
                            this.image2= '/storage/'+this.images.images[index];
                            if(index == 2)
                            this.image3= '/storage/'+this.images.images[index];
                        });
                    }

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
    }

}
</script>

<style lang="scss" scoped>

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
