<template>
    <div class="mp-button">
        <button :type="type" class="button"
                :disabled="loading || disabled"
                :class="{'button-primary': primary, 'button-large': large}">
            <slot></slot>
        </button>
        <span class="spinner is-active" v-if="loading"></span>
    </div>
</template>

<script>
    export default {
        name: 'mp-button',

        props: {
            type: {
                type: String,
                default: "submit"
            },
            disabled: Boolean,
            loading: Boolean,
            large: {
                type: Boolean,
                default: true
            },
            primary: {
                type: Boolean,
                default: true
            }
        },

        data() {
            return {

            }
        }

    }
</script>

<style lang="scss" scoped>

    .mp-button {
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
    }

</style>