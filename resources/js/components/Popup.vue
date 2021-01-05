<template>
    <div class="mp-popup" :class="{'is-active': isActive}">
        <div class="backdrop"></div>

        <div class="mp-popup-content">
            <button type="button"
                    v-if="! hideCloseButton"
                    @click.prevent="hide"
                    class="close-button">
                <span class="dashicons dashicons-no"></span>
            </button>

            <div class="mp-popup-content-inner">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        props: {
            isOpen: Boolean,
            hideCloseButton: Boolean
        },

        data() {
            return {
                isActive: this.isOpen
            }
        },

        watch: {
            isOpen()  {
                this.isActive = this.isOpen;
            }
        },

        methods: {
            toggle() {
                if(this.isActive) {
                    this.hide();
                }

                else {
                    this.show();
                }
            },

            show() {
                this.isActive = true;
                this.$emit('showing');
            },

            hide() {
                this.isActive = false;
                this.$emit('hiding');
            }

        }

    }
</script>

<style lang="scss" scoped>

    .mp-popup {
        position: fixed;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
        visibility: hidden;
        opacity: 0;
        pointer-events: none;

        &.is-active {
            opacity: 1;
            visibility: visible;
            pointer-events: all;
        }

        .backdrop {
            background-color: rgba(black, 0.5);
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
        }

        .mp-popup-content {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            max-width: 500px;
            max-height: 85%;

            .close-button {
                position: absolute;
                right: 0;
                padding: 2px;
                margin: 0;
                bottom: calc(100% + 8px);
                background-color: white;
                border: 0;
                line-height: 1;
                display: block;
                color: black;
                border-radius: 4px;
                cursor: pointer;

                span {
                    font-size: 32px;
                    display: block;
                    width: 32px;
                    height: 32px;
                }
            }

            &-inner {
                position: relative;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: white;
                padding: 8px;
                border-radius: 4px;
            }

        }

    }

</style>