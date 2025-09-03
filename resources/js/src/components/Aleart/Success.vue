<template>
    <b-card>
        <div>
            <!-- success -->
            <b-alert
                variant="success"
            >
                <h4 class="alert-heading">
                    Success
                </h4>
                <div class="alert-body">
                    <span>{{this.msg}}</span>
                </div>
            </b-alert>

        </div>
    </b-card>
</template>

<script>
import { BAlert, BCardText,BCard } from 'bootstrap-vue'
import { stringify } from 'uuid'

export default {
    components: {
        BAlert,BCard,
        BCardText,
    },
    data() {
        return {

        }
    },
    props:{
        msg:String,
        isSuccess:Boolean
    }
}
</script>

<style>

</style>
