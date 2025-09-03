<template>
  <div>
    <b-alert
      variant="danger"
      :show="userProfile === undefined"
    >
      <h4 class="alert-heading">
        Error fetching user data
      </h4>
      <div class="alert-body">
        No user found with this user id. Check
        <b-link
          class="alert-link"
          :to="{ name: 'users-list'}"
        >
          User List
        </b-link>
        for other users.
      </div>
    </b-alert>
     <template v-if="userProfile">
      <!-- First Row -->
      <b-row>
        <b-col
          cols="12"
          xl="9"
          lg="8"
          md="7"
        >
          <user-view-user-info-card :user-data="userProfile" />
        </b-col>

        <b-col
          cols="12"
          md="5"
          xl="3"
          lg="4"
        >
        </b-col>
      </b-row>

      <b-row>
        <b-col
          cols="12"
          lg="6"
        >
        </b-col>
        <b-col
          cols="12"
          lg="6"
        >
        </b-col>
      </b-row>

      <invoice-list />
    </template>

  </div>
</template>

<script>
import { BCard, BCardText ,BAlert, BRow, BCol, BLink,} from 'bootstrap-vue'
import axios from 'axios'
import UserViewUserInfoCard from './UserViewUserInfoCard.vue'

export default {
  components: {
    BCard,
    BCardText,
    BAlert,
     BRow,
    BCol,
    BLink,
     // Local Components
    UserViewUserInfoCard,
  },
  data(){
      return{
          userProfile:null,
      }
  },
  created(){
      this.getProfile();
  },
  methods:{
      getProfile(){
          axios.get('/api/web/user-details/'+this.$route.params.id)
          .then(res =>{
              this.userProfile = res.data.content
          })
          .catch(err =>{
              console.log(err)
          })
          return
      }
  }
}
</script>

<style>

</style>
