import TitleInput from "../components/TitleInput";
import _ from 'underscore';
import {v4 as uuid} from 'uuid';
import {createErrorsObject} from "../helpers";

export default {

    props: {

    },

    data() {
        const data = MeiliPress.index_data;
        const errors = createErrorsObject(data.form);

        return {
            ...data,
            errors,
            loading: false,
            selectedFieldType: ''
        }
    },

    mounted() {

    },

    watch: {
        'form.data.query': {
            deep: true,
            handler() {
                this.postCount();
            }
        }
    },

    methods: {
        async save() {
            if(this.loading) {
                return;
            }

            this.loading = true;
            let res = await this.$api.post('index/crud/save', this.form);

            if(! res.success) {
                this.errors = {
                    ...this.errors,
                    ...res.data
                }

                this.loading = false;

                return;
            }

            if(res.data.content) {
                this.$root.successMessage('index/crud/save', res.data);
            }

            if(res.data.redirect) {
                window.location.href = res.data.redirect;
            }

            if(res.data.index.reindex_required) {
                if (confirm(this.$trans("The changes you made to the index require to reindex, do you want to reindex now?"))) {
                    this.reindex();
                }
            }

            this.form.last_index = res.data.index.last_index;
            this.form.reindex_required = res.data.index.reindex_required;
            this.form.updated_at = res.data.index.updated_at;
            this.form.index_state = res.data.index.index_state;

            this.loading = false;
        },

        async postCount() {
            let res = await this.$api.post('index/crud/postCount', this.form);

            if(res.data) {
                this.form.posts_count = res.data.count;
            }
        },

        addField() {
            if(! this.selectedFieldType) {
                return;
            }

            let field = this.availableFields.find(af => af.form.id === this.selectedFieldType);

            if(! field) {
                return;
            }

            field = JSON.parse(JSON.stringify(field));
            field.form.id = uuid();
            field.settings.isOpen = true;

            this.form.data.fields.push(field);
        },

        removeField(id) {
            const index = this.form.data.fields.findIndex(fld => fld.form.id === id);

            if(index < 0) {
                return;
            }

            this.$delete(this.form.data.fields, index);
        },

        reindex() {
            this.$api.reindex(this.form.id)
                .then(() => {
                    this.form.last_inde = (new Date()).toLocaleTimeString();
                    this.form.reindex_required = false;
                });
        }

    },

    computed: {

    },

    components: {
        TitleInput
    }

}