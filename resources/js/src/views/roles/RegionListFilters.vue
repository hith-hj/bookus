<template>
  <b-card no-body>
    <b-card-header class="pb-50">
      <h5>
        Filters
      </h5>
    </b-card-header>
    <b-card-body>
      <b-row>
       
     
        <b-col
          cols="12"
          md="4"
          class="mb-md-0 mb-2"
        >
          <label>Regions</label>
          <v-select
          v-model="regionVal"
          label="name" 
          :options="regions"
          :reduce="name => name.id"
            class="w-100"
          />
        </b-col>
      </b-row>
    </b-card-body>
  </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardBody, BRow, BCol } from 'bootstrap-vue'
import vSelect from 'vue-select'
import axios from 'axios'
export default {
  components: {
    BRow,
    BCol,
    BCard,
    BCardHeader,
    BCardBody,
    vSelect,
  },
  props: {
    roleFilter: {
      type: [String, null],
      default: null,
    },
  
    statusFilter: {
      type: [String, null],
      default: null,
    },
   
  
  },
  data(){
    return{
        userStatus: ['active', 'inactive'],

          regionVal:'',
          regions:[],
    }
  },
  watch:{
    regionVal(value){
        this.filterStatus();
    },
    roleVal(value){
        this.filterRole();

    }

  },created(){
    this.getDistrict();
  }
  ,
  methods:{
    filterStatus(){
      this.$emit('update:statusFilter', this.regionVal);
    },
    filterRole(){
      this.$emit('update:roleFilter', this.roleVal);
    },
    getDistrict(){
       const   userToken =localStorage.getItem('userToken');
            let token=JSON.parse(userToken);

          let config ={
            headers :{"Authorization" : `Bearer ` + token}
          }
      axios.get('/api/web/regions',config)
      .then(res =>{
        this.regions = res.data.content ;
        
      })
      .then(err => console.log(err))
    }
   
  }
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
</style>
