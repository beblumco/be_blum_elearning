<template>
    <div class="dev-mini-loading mt-4" v-show="is_show">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        is_show: Boolean
    }
}
</script>

<style scoped>
</style>