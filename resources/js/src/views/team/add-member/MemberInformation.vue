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
          <!-- Field: first -->
          <b-col cols="6">
            <b-form-group label="First Name" label-for="first_name">
              <validation-provider
                #default="{ errors }"
                name="first_name"
                rules="required"
              >
                <b-form-input
                  :state="errors.length > 0 ? false : null"
                  v-model="first_name"
                  placeholder="last name"
                />
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <b-col cols="6">
            <b-form-group label="Last Name" label-for="last_name">
              <validation-provider
                #default="{ errors }"
                name="last_name"
                rules="required"
              >
                <b-form-input
                  :state="errors.length > 0 ? false : null"
                  v-model="last_name"
                  placeholder="last name"
                />
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <!-- email -->
          <b-col cols="6">
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
                  v-model="email"
                  placeholder="Email"
                />
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <b-col cols="12" class="p-1">
            <SingleImageVue :ImageFile.sync="image"></SingleImageVue>
          </b-col>
          <!-- Field: Name -->
          <b-col cols="6">
            <!-- phone number -->

            <!-- password -->

            <b-form-group label-for="register-password" label="Password">
              <validation-provider
                #default="{ errors }"
                name="Password"
                vid="password"
                rules="required"
              >
                <b-input-group
                  class="input-group-merge"
                  :class="errors.length > 0 ? 'is-invalid' : null"
                >
                  <b-form-input
                    id="register-password"
                    v-model="password"
                    class="form-control-merge"
                    :type="passwordFieldType"
                    :state="errors.length > 0 ? false : null"
                    name="register-password"
                    placeholder="············"
                  />
                  <b-input-group-append is-text>
                    <feather-icon
                      :icon="passwordToggleIcon"
                      class="cursor-pointer"
                      @click="togglePasswordVisibility"
                    />
                  </b-input-group-append>
                </b-input-group>
                <small class="text-danger">{{ errors[0] }}</small>
              </validation-provider>
            </b-form-group>
          </b-col>
          <!-- Phone Number -->
          <b-col cols="6" class="my-1">
            <label>phone number</label>
            <br />
            <VuePhoneNumberInput
              default-country-code="AE"
              @update="onUpdate"
              v-model="phoneNumber"
            />
          </b-col>
        </b-row>
        <div class="m-2">
          <h4>Available Days</h4>
          <b-card
            title="Add the available days"
            header-tag="header"
            bg-variant="light"
            class="mx-3 my-3"
          >
            <template #header> </template>
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
                  <h5>start time:</h5>
                  <b-form-timepicker
                    v-model="Saturday.start"
                    :disabled="!saturdayStatus"
                    locale="en"
                  ></b-form-timepicker>
                </div>
              </b-col>
              <b-col cols="4">
                <div class="d-flex justify-content-start p-0 m-0">
                  <h5>end time:</h5>
                  <b-form-timepicker
                    v-model="Saturday.end"
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
                  <h5>start time:</h5>
                  <b-form-timepicker
                    v-model="Sunday.start"
                    :disabled="!sundayStatus"
                    locale="en"
                  ></b-form-timepicker>
                </div>
              </b-col>
              <b-col cols="4">
                <div class="d-flex justify-content-start p-0 m-0">
                  <h5>end time:</h5>
                  <b-form-timepicker
                    v-model="Sunday.end"
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
                  <h5>start time:</h5>
                  <b-form-timepicker
                    v-model="Monday.start"
                    :disabled="!mondayStatus"
                    locale="en"
                  ></b-form-timepicker>
                </div>
              </b-col>
              <b-col cols="4">
                <div class="d-flex justify-content-start p-0 m-0">
                  <h5>end time:</h5>
                  <b-form-timepicker
                    v-model="Monday.end"
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
                  <h5>start time:</h5>
                  <b-form-timepicker
                    v-model="Tuesday.start"
                    :disabled="!tuesdayStatus"
                    locale="en"
                  ></b-form-timepicker>
                </div>
              </b-col>
              <b-col cols="4">
                <div class="d-flex justify-content-start p-0 m-0">
                  <h5>end time:</h5>
                  <b-form-timepicker
                    v-model="Tuesday.end"
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
                  <h5>start time:</h5>
                  <b-form-timepicker
                    v-model="Wednesday.start"
                    :disabled="!wednesdayStatus"
                    locale="en"
                  ></b-form-timepicker>
                </div>
              </b-col>
              <b-col cols="4">
                <div class="d-flex justify-content-start p-0 m-0">
                  <h5>end time:</h5>
                  <b-form-timepicker
                    v-model="Wednesday.end"
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
                  <h5>start time:</h5>
                  <b-form-timepicker
                    v-model="Thursday.start"
                    :disabled="!thursdayStatus"
                    locale="en"
                  ></b-form-timepicker>
                </div>
              </b-col>
              <b-col cols="4">
                <div class="d-flex justify-content-start p-0 m-0">
                  <h5>end time:</h5>
                  <b-form-timepicker
                    v-model="Thursday.end"
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
                  <h5>start time:</h5>
                  <b-form-timepicker
                    v-model="Friday.start"
                    :disabled="!fridayStatus"
                    locale="en"
                  ></b-form-timepicker>
                </div>
              </b-col>
              <b-col cols="4">
                <div class="d-flex justify-content-start p-0 m-0">
                  <h5>end time:</h5>
                  <b-form-timepicker
                    v-model="Friday.end"
                    :disabled="!fridayStatus"
                    locale="en"
                  ></b-form-timepicker>
                </div>
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
        <b-button variant="outline-secondary" type="reset"> Reset </b-button>
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
  BInputGroup,
  BCardHeader,
  BCardTitle,
  BFormTextarea,
  BFormCheckbox,BFormTimepicker,
  BInputGroupPrepend,
  BInputGroupAppend,BFormDatepicker
} from "bootstrap-vue";

import axios from "axios";
import { useInputImageRenderer } from "@core/comp-functions/forms/form-utils";
import { ValidationProvider, ValidationObserver } from "vee-validate";
import { required, alphaNum, email } from "@validations";
import { togglePasswordVisibility } from "@core/mixins/ui/forms";

import VuePhoneNumberInput from "vue-phone-number-input";
import "vue-phone-number-input/dist/vue-phone-number-input.css";
import store from "@/store/index";
import ToastificationContent from "@core/components/toastification/ToastificationContent.vue";
// import ErrorMsg from '@/components/Aleart/Error'
// import SuccessMsg from '@/components/Aleart/Success'
import SingleImageVue from "@/components/SingleImag/SingleImage.vue";

import vSelect from "vue-select";

export default {
  components: {
    BButton,BFormTimepicker,
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
    // ErrorMsg,
    // SuccessMsg,
    VuePhoneNumberInput,
  },
  setup() {
    const statusOptions = ["active", "inactive"];

    return { statusOptions };
  },
  mixins: [togglePasswordVisibility],

  data() {
    return {
      // codeSimple,
      required,
      email,
      image: null,
      first_name: "",
      password: "",
      last_name: "",
      email: "",
      statusCenter: "active",
      phoneNumber: "",
      msg: "",
      iserror: false,
      issuccess: false,
      //avaliable days
      saturdayStatus: false,
      sundayStatus: true,
      mondayStatus: true,
      tuesdayStatus: true,
      wednesdayStatus: true,
      thursdayStatus: true,
      fridayStatus: true,
      Saturday: {
        start: "",
        end: "",
      },
      Sunday: { status: true, start: "10:00:00", end: "22:00:00" },
      Monday: { status: true, start: "10:00:00", end: "22:00:00" },
      Tuesday: { status: true, start: "10:00:00", end: "22:00:00" },
      Wednesday: { status: true, start: "10:00:00", end: "22:00:00" },
      Thursday: { status: true, start: "10:00:00", end: "22:00:00" },
      Friday: { status: true, start: "10:00:00", end: "22:00:00" },

    };
  },
  mounted() {},
  computed: {
    passwordToggleIcon() {
      return this.passwordFieldType === "password" ? "EyeIcon" : "EyeOffIcon";
    },

  },

  methods: {
    validationForm() {
      this.$refs.simpleRules.validate().then((success) => {
        if (success) {
          // eslint-disable-next-line
          const userToken = localStorage.getItem("userToken");
          const token = JSON.parse(userToken);

          const config = {
            headers: {
              "content-type": "multipart/form-data",
              Authorization: `Bearer ${token}`,
            },
          };
          const formdata = new FormData();

          formdata.append("first_name", this.first_name);
          formdata.append("last_name", this.last_name);
          formdata.append("email", this.email);
          formdata.append("password", this.password);
          formdata.append("phone_number", this.phoneNumber);

          formdata.append("status", 1);

             //open days information
             if (this.saturdayStatus) {
            formdata.append("saturdayStart", this.Saturday.start);
            formdata.append("saturdayEnd", this.Saturday.end);
          }
          if (this.sundayStatus) {
            formdata.append("sundayStart", this.Sunday.start);
            formdata.append("sundayEnd", this.Sunday.end);
          }

          if (this.mondayStatus) {
            formdata.append("mondayStart", this.Monday.start);
            formdata.append("mondayEnd", this.Monday.end);
          }
          if (this.tuesdayStatus) {
            formdata.append("tuesdayStart", this.Tuesday.start);
            formdata.append("tuesdayEnd", this.Tuesday.end);
          }
          if (this.wednesdayStatus) {
            formdata.append("wednesdayStart", this.Wednesday.start);
            formdata.append("wednesdayEnd", this.Wednesday.end);
          }
          // thursdayStatus
          if (this.thursdayStatus) {
            formdata.append("thursdayStart", this.Thursday.start);
            formdata.append("thursdayEnd", this.Thursday.end);
          }
          if (this.fridayStatus) {
            formdata.append("fridayStart", this.Friday.start);
            formdata.append("fridayEnd", this.Friday.end);
          }

          if (this.image) formdata.append("cover_image", this.image);
          axios
            .post("/api/admin/new-member", formdata, config)
            .then((res) => {
              this.$router.replace("/teamList").then(() => {
                this.$toast({
                  component: ToastificationContent,
                  timeout: 5000,
                  props: {
                    title: "add member successfuly",
                    icon: "EditIcon",
                    variant: "success",
                  },
                });
              });
              this.msg = "Add member Successufly";
              this.iserror = false;
              this.issuccess = true;
            })
            .catch((error) => {
              if (error.response.status == 422) {
                this.msg = error.response.data.message;
                this.iserror = true;
                this.issuccess = false;
              }
            });
        }
      });
    },
    onUpdate(payload) {
      this.results = payload;
    },
  },
};
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
