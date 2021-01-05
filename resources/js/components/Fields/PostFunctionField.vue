<template>
    <mp-index-field v-model="form"
                    :settings="settings"
                    @remove="$emit('remove', form.id)"
                    class="mp-index-post-function-field"
                    :order="order"
                    :index="index"
                    :local-name-label="$trans('Function Name')">

        <mp-field :label="$trans('Function Name')"
                  slot="localName"
                  :message="$trans('The function name (The function must accept WP_Post as the first argument)')"
                  message-tooltip
                  :error="error"
                  :inline="false">
            <input type="text" v-model="form.localName" @blur="fetchFunctionInfo">
        </mp-field>

        <div class="row">
            <mp-field slot="localName" :label="$trans('Function return type')" :message="$trans('The type that the function return will cast to')" message-tooltip :inline="false">
                <select v-model="form.type">
                    <option value="">{{ 'Select type' | __ }}</option>
                    <option v-for="type in settings.types" :value="type">{{ type }}</option>
                </select>
            </mp-field>
        </div>

        <div class="row">
            <p>{{ 'Function arguments' | __ }}</p>
        </div>

        <div class="row">
            <mp-field v-for="argument in form.arguments"
                      :label="argument.name"
                      :key="argument.name"
                      :inline="false">
                <input type="text" v-model="argument.value"
                       :placeholder="argument.default">
            </mp-field>
        </div>

    </mp-index-field>
</template>

<script>
    export default {

        props: {
            value: Object,
            settings: Object,
            index: Object,
            order: Number,
        },

        data() {
            return {
                form: this.value,
                error: ''
            }
        },

        methods: {

            async fetchFunctionInfo() {
                this.error = '';

                let res = await this.$api.post('index/fields/inspectFunction', {
                    'function': this.form.localName
                });

                if(! res.success) {
                    this.error = res.data.function;
                    this.$set(this.form, 'arguments', []);
                    return;
                }

                this.$set(this.form, 'arguments', res.data);
            }

        }

    }
</script>

<style scoped>

</style>