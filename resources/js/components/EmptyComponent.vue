<template>
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6">
                <h2>{{ title }}</h2>
                <p>{{ description }}</p>
            </div>
            <div class="col-md-6">
                <img :src="'/images/empty.svg'" class="img-fluid">
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['title', 'description'],
        data() {
            return {

            };
        },
        mounted() {

        },
        beforeMount() {
        },
        methods: {

        }
    }
</script>
