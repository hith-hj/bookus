<template>
    <div>
        <!-- <label for="amount">Enter amount</label> -->
        <div class="flex">
            <span class="currency">$</span>
            <input v-model="amount" id="amount" name="amount" type="text" maxlength="15"/>
        </div>
    </div>


</template>

<script>

export default {
    name: "CurrencyInput",
    props: {
        modelValue: Number,
        options: Object,
    },
    setup(props) {
        const { inputRef } = useCurrencyInput(props.options);

        return { inputRef };
    },
    watch:{
        amount(v){
            this.amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
    }
};
</script>
<style scoped>
label{
    color: #999;
    display: block;
    margin-bottom: 10px;
    text-transform: uppercase;
    font-size: 10px;
    font-weight: bold;
    letter-spacing: 0.05em;
}
.flex {
    display: flex;
    justify-content: flex-start;
}
.flex input {
    max-width: 300px;
    flex: 1 1 300px;
}
.flex .currency {
    font-size: 15px;
    padding: 0 10px 0 20px;
    color: #999;
    border: 2px solid #ccc;
    border-right: 0;
    line-height: 2.5;
    border-radius: 7px 0 0 7px;
    background: white;
}

</style>
