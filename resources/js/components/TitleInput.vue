<template>
    <div class="mp-title-input">
        <div class="input" :class="{'in-focus': isInFocus}">
            <span class="prefix" v-if="isInFocus || newValue">{{ prefix }}</span>
            <label class="screen-reader-text" for="index_name">{{ label }}</label>
            <input type="text" id="index_name"
                   v-model="newValue"
                   :placeholder="placeholder || label"
                   @focusin="isInFocus = true"
                   @focusout="isInFocus = false"
                   :disabled="disabled"
                   @input="$emit('input', newValue)"
                   autocomplete="off" />
        </div>
        <div class="message">
            <slot name="message"></slot>
        </div>

        <div class="error-message" v-if="error">{{ error }}</div>
    </div>
</template>

<script>
    export default {

        props: {
            disabled: Boolean,
            value: String,
            label: String,
            prefix: String,
            placeholder: String,
            error: String
        },

        data() {
            return {
                newValue: this.value,
                isInFocus: false
            }
        }

    }
</script>

<style lang="scss" scoped>
    .mp-title-input {
        position: relative;

        .input {
            padding: 3px 8px;
            font-size: 1.7em;
            line-height: 100%;
            height: 1.7em;
            width: 100%;
            outline: none;
            margin: 0 0 3px;
            background-color: #fff;
            display: flex;
            align-items: center;
            min-height: 30px;
            color: #32373c;
            box-shadow: 0 0 0 transparent;
            border-radius: 4px;
            border: 1px solid #7e8993;

            &.in-focus {
                border-color: #007cba;
                box-shadow: 0 0 0 1px #007cba;
            }

        }

        .prefix {
            opacity: 0.5;
        }

        input {
            background-color: transparent;
            border: 0;
            padding: 0;
            margin: 0;
            width: 100%;
            flex-grow: 1;
            font-size: inherit;

            &:focus {
                border: 0;
                outline: none;
                box-shadow: none;
            }

        }

    }
</style>