<template>
    <b-card>
        <b-tabs
            v-if="true"
            pills
        >

            <!-- Tab: Account -->


                    <span class="d-none d-sm-inline .text-primary ">Add Category</span>

                <Category-Information></Category-Information>

            <!-- Tab: Information -->

            <!-- Tab: Social -->

        </b-tabs>
    </b-card>

</template>

<script>
import { BCard, BCardText ,BAlert, BRow, BCol, BLink,BTab, BTabs} from 'bootstrap-vue'
import CategoryInformation from './add-category/CategoryInformation.vue'
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
        CategoryInformation,

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
