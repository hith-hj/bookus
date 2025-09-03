<template>


                <Client-Information></Client-Information>
            <!-- Tab: Information -->

</template>

<script>
import { BCard, BCardText ,BAlert, BRow, BCol, BLink,BTab, BTabs} from 'bootstrap-vue'
import ClientInformation from '@/views/centers/add-center/CenterInformation.vue'
import axios from 'axios'
import UserEditTabInformation from "@/views/apps/user/users-edit/UserEditTabInformation.vue";

export default {
    components: {
        UserEditTabInformation,
        BCard,
        BCardText,
        BAlert,
        BRow,
        BCol,
        BLink,
        BTab,
        BTabs,
        // Local Components
        ClientInformation,

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
