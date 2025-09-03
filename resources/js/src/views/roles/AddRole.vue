<template>
  <b-card>
   <b-tabs
      v-if="true"
      pills
    >

      <!-- Tab: Account -->


        <role-information></role-information>


      <!-- Tab: Information -->

      <!-- Tab: Social -->

    </b-tabs>
  </b-card>

</template>

<script>
import { BCard, BCardText ,BAlert, BRow, BCol, BLink,BTab, BTabs} from 'bootstrap-vue'
import RoleInformation from './add-role/RoleInformation.vue'
import axios from 'axios'

export default {
  components: {
    BCard,
    BCardText,
    BAlert,
     BRow,
    BCol,
    BLink,
     BTab,
    BTabs,
     // Local Components
     RoleInformation,

  },
  data(){
      return{
      }
  },
  created(){
  },
  methods:{

  }
}
</script>

<style>

</style>
