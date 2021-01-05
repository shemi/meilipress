const $ = window.jQuery;
import {createErrorsObject} from "../helpers";

export default {

    props: {
        formData: Object
    },

    data() {
        return {
            form: this.formData,
            errors: createErrorsObject(this.formData),
            testing: false,
            saving: false
        }
    },

    mounted() {
        for(let key of Object.keys(this.form)) {
            this.errors[key] = "";
        }
    },

    methods: {
        async testConnection() {
            MeiliPress.app.dismissMessage('success-connection-test');
            MeiliPress.app.dismissMessage('meilipress/settings/testConnection');
            this.testing = true;

            let res = await this.$api.post('settings/testConnection', this.form.instance);

            this.testing = false;

            if(! res) {
                return;
            }

            const {data} = res;

            MeiliPress.app.successMessage('success-connection-test', data);
            $("#mp-disabled-notice").hide();
        },

        async submit() {
            MeiliPress.app.dismissMessage('settings-saved');
            MeiliPress.app.dismissMessage('meilipress/settings/save');
            MeiliPress.app.dismissMessage('success-connection-test');

            this.saving = true;

            let res = await this.$api.post('settings/save', this.form);

            this.saving = false;

            if(! res) {
                return;
            }

            if(! res.success) {
               this.errors = {
                   ...this.errors,
                   ...res.data
               };
            }

            const {data} = res;

            MeiliPress.app.successMessage('settings-saved', data);
            $("#mp-disabled-notice").hide();
        }
    },

    computed: {

    }

}