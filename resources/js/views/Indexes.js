import ListTable from 'vue-wp-list-table';

export default {

    props: {

    },

    data() {
        return {
            searchTerm: '',
            indexes: MeiliPress.indexes_data.indexes,
            columns: MeiliPress.indexes_data.columns,
            actions: MeiliPress.indexes_data.actions,
            bulkActions: MeiliPress.indexes_data.bulkActions
        }
    },

    methods: {
        onActionClick(action, row) {
            switch (action) {
                case 'edit':
                    window.location.href = row.editUrl;
                    break;

                case 'trash':

                    break;
            }
        },

        onBulkAction() {

        }
    },

    components: {
        ListTable
    }

}